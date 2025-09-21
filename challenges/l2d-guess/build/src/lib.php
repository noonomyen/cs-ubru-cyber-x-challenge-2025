<?php
declare(strict_types=1);

function loadEnv(string $path): array
{
    if (!is_readable($path)) {
        return [];
    }

    $vars = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        if (str_starts_with($line, '#')) {
            continue;
        }
        [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
        $vars[trim($name)] = trim($value);
    }

    return $vars;
}

function serveAsset(array $env): void
{
    if (!isset($env['R2_ACCESS_KEY_ID'], $env['R2_SECRET_ACCESS_KEY'], $env['R2_ENDPOINT'], $env['R2_BUCKET'])) {
        http_response_code(500);
        echo 'Missing configuration';
        return;
    }

    $key = $_GET['asset'] ?? '';
    if (!is_string($key)) {
        http_response_code(400);
        echo 'Invalid asset key';
        return;
    }

    $key = trim($key);
    if ($key === '' || !str_starts_with($key, 'images/')) {
        http_response_code(404);
        echo 'Not found';
        return;
    }

    $object = r2GetObject($env, $key);
    if ($object['status'] !== 200) {
        http_response_code(404);
        echo 'Not found';
        return;
    }

    if ($object['contentType'] !== null) {
        header('Content-Type: ' . $object['contentType']);
    } else {
        header('Content-Type: application/octet-stream');
    }
    header('Cache-Control: no-store, max-age=0');
    echo $object['body'];
}

function startNewGame(array $env): array
{
    $message = null;
    $keys = fetchImageKeys($env, 'images/');

    if ($keys === null) {
        $message = 'เชื่อมต่อ Cloudflare R2 ไม่สำเร็จ (HTTP ' . ($GLOBALS['lastR2Status'] ?? 'unknown') . ')';
        return [null, $message];
    }

    if (empty($keys)) {
        $message = 'ไม่พบไฟล์รูปภาพใน bucket โปรดตรวจสอบการตั้งค่า R2 หรืออัปโหลดรูปก่อน';
        return [null, $message];
    }

    shuffle($keys);
    $selected = array_slice($keys, 0, min(TOTAL_ROUNDS, count($keys)));

    $rounds = [];
    foreach ($selected as $key) {
        $label = basename($key);
        $display = preg_replace('/[_-]+/', ' ', pathinfo($label, PATHINFO_FILENAME)) ?? $label;
        $rounds[] = [
            'key' => $key,
            'answer' => normalizeAnswer(pathinfo($label, PATHINFO_FILENAME)),
            'answer_label' => $display,
        ];
    }

    $game = [
        'rounds' => $rounds,
        'current' => 0,
        'score' => 0,
        'failed' => false,
        'finished' => false,
        'total' => count($rounds),
        'history' => [],
    ];

    return [$game, $message];
}

function evaluateGuess(array $game, string $guess, int $roundIndex): array
{
    if (!isset($game['rounds'][$game['current']])) {
        return $game;
    }

    $round = $game['rounds'][$game['current']];

    $isExpectedRound = ($roundIndex === $game['current']);
    if (!$isExpectedRound) {
        return $game;
    }

    $normalizedGuess = normalizeAnswer($guess);
    $isCorrect = $normalizedGuess !== '' && $normalizedGuess === $round['answer'];

    $game['history'][] = [
        'answer_label' => $round['answer_label'],
        'guess_label' => sanitizeDisplayGuess($guess),
        'correct' => $isCorrect,
    ];

    if ($isCorrect) {
        $game['score']++;
        $game['current']++;
        if ($game['current'] >= $game['total']) {
            $game['finished'] = true;
        }
    } else {
        $game['failed'] = true;
        $game['finished'] = true;
    }

    return $game;
}

function sanitizeDisplayGuess(string $guess): string
{
    $guess = trim($guess);
    return $guess === '' ? '-' : $guess;
}

function normalizeAnswer(?string $answer): string
{
    $answer = strtolower((string) $answer);
    return preg_replace('/[^a-z0-9]+/i', '', $answer) ?? '';
}

function buildAssetUrl(string $key): string
{
    $encoded = rawurlencode($key);
    $encoded = str_replace('%2F', '/', $encoded);
    return '?asset=' . $encoded . '&t=' . time();
}

function fetchImageKeys(array $env, string $prefix): ?array
{
    $keys = [];
    $continuationToken = null;

    do {
        $query = [
            'list-type' => '2',
            'prefix' => $prefix,
        ];
        if ($continuationToken !== null) {
            $query['continuation-token'] = $continuationToken;
        }

        $response = r2Request($env, 'GET', buildCanonicalUri($env['R2_BUCKET']), $query, '');
        $GLOBALS['lastR2Status'] = $response['status'];

        if ($response['status'] !== 200) {
            error_log('R2 list error: HTTP ' . $response['status'] . ' body=' . substr($response['body'], 0, 120));
            return null;
        }

        $xml = simplexml_load_string($response['body']);
        if ($xml === false) {
            error_log('Failed to parse R2 XML response.');
            return null;
        }

        foreach ($xml->Contents ?? [] as $content) {
            $key = (string) ($content->Key ?? '');
            if ($key === '' || !str_starts_with($key, $prefix)) {
                continue;
            }
            if (str_ends_with($key, '/')) {
                continue;
            }
            $keys[] = $key;
        }

        $isTruncated = ((string) ($xml->IsTruncated ?? 'false')) === 'true';
        $continuationToken = $isTruncated ? (string) ($xml->NextContinuationToken ?? '') : null;
        if ($continuationToken === '') {
            $continuationToken = null;
        }
    } while ($continuationToken !== null);

    return $keys;
}

function r2GetObject(array $env, string $key): array
{
    $canonical = buildCanonicalUri($env['R2_BUCKET'], $key);
    $response = r2Request($env, 'GET', $canonical, [], '');
    return [
        'status' => $response['status'],
        'body' => $response['body'],
        'contentType' => $response['headers']['content-type'] ?? null,
    ];
}

function r2Request(array $env, string $method, string $canonicalUri, array $query, string $body): array
{
    $endpoint = rtrim($env['R2_ENDPOINT'], '/');
    $host = parse_url($endpoint, PHP_URL_HOST) ?: '';
    $accessKey = $env['R2_ACCESS_KEY_ID'];
    $secretKey = $env['R2_SECRET_ACCESS_KEY'];
    $region = $env['R2_REGION'] ?? 'auto';
    $service = 's3';

    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $amzDate = $now->format('Ymd\THis\Z');
    $dateStamp = $now->format('Ymd');

    $payloadHash = ($body === '') ? 'UNSIGNED-PAYLOAD' : hash('sha256', $body);

    $canonicalQuery = buildCanonicalQuery($query);

    $headers = [
        'host' => $host,
        'x-amz-content-sha256' => $payloadHash,
        'x-amz-date' => $amzDate,
    ];

    $canonicalHeaders = '';
    ksort($headers);
    foreach ($headers as $name => $value) {
        $canonicalHeaders .= $name . ':' . trim(preg_replace('/\s+/', ' ', $value)) . "\n";
    }
    $canonicalHeaders = rtrim($canonicalHeaders, "\n");

    $signedHeaders = implode(';', array_keys($headers));

    $canonicalRequest = implode("\n", [
        $method,
        $canonicalUri,
        $canonicalQuery,
        $canonicalHeaders,
        '',
        $signedHeaders,
        $payloadHash,
    ]);

    $algorithm = 'AWS4-HMAC-SHA256';
    $credentialScope = implode('/', [$dateStamp, $region, $service, 'aws4_request']);
    $hashCanonical = hash('sha256', $canonicalRequest);
    $stringToSign = implode("\n", [$algorithm, $amzDate, $credentialScope, $hashCanonical]);

    $signingKey = buildSigningKey($secretKey, $dateStamp, $region, $service);
    $signature = hash_hmac('sha256', $stringToSign, $signingKey);

    $authorizationHeader = sprintf(
        '%s Credential=%s/%s, SignedHeaders=%s, Signature=%s',
        $algorithm,
        $accessKey,
        $credentialScope,
        $signedHeaders,
        $signature
    );

    if (getenv('L2D_DEBUG')) {
        $debugBlock = "CanonicalRequest:\n" . $canonicalRequest . "\n\nStringToSign:\n" . $stringToSign . "\n\n";
        file_put_contents('/tmp/r2-debug.log', $debugBlock, FILE_APPEND);
    }

    $url = $endpoint . $canonicalUri;
    if ($canonicalQuery !== '') {
        $url .= '?' . $canonicalQuery;
    }

    $requestHeaders = [
        'Host: ' . $host,
        'x-amz-date: ' . $amzDate,
        'x-amz-content-sha256: ' . $payloadHash,
        'Authorization: ' . $authorizationHeader,
    ];

    $receivedHeaders = [];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$receivedHeaders) {
        $len = strlen($header);
        $parts = explode(':', $header, 2);
        if (count($parts) === 2) {
            $name = strtolower(trim($parts[0]));
            $value = trim($parts[1]);
            $receivedHeaders[$name] = $value;
        }
        return $len;
    });
    if ($body !== '') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $responseBody = curl_exec($ch);
    if ($responseBody === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'status' => 500,
            'body' => $error,
            'headers' => $receivedHeaders,
        ];
    }

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
    curl_close($ch);

    return [
        'status' => $statusCode,
        'body' => $responseBody,
        'headers' => $receivedHeaders,
    ];
}

function buildCanonicalQuery(array $query): string
{
    if (empty($query)) {
        return '';
    }

    $items = [];
    foreach ($query as $key => $value) {
        $values = is_array($value) ? $value : [$value];
        foreach ($values as $single) {
            $items[] = [
                rawurlencode((string) $key),
                rawurlencode((string) $single),
            ];
        }
    }

    usort($items, function ($a, $b) {
        return [$a[0], $a[1]] <=> [$b[0], $b[1]];
    });

    $pairs = [];
    foreach ($items as $item) {
        $pairs[] = $item[0] . '=' . $item[1];
    }

    return implode('&', $pairs);
}

function buildSigningKey(string $secretKey, string $date, string $region, string $service)
{
    $kDate = hash_hmac('sha256', $date, 'AWS4' . $secretKey, true);
    $kRegion = hash_hmac('sha256', $region, $kDate, true);
    $kService = hash_hmac('sha256', $service, $kRegion, true);
    return hash_hmac('sha256', 'aws4_request', $kService, true);
}

function buildCanonicalUri(string $bucket, string $key = ''): string
{
    $segments = [$bucket];
    $key = trim($key, '/');
    if ($key !== '') {
        $segments = array_merge($segments, explode('/', $key));
    }

    $encoded = array_map(function ($segment) {
        return str_replace('%2F', '/', rawurlencode($segment));
    }, $segments);

    return '/' . implode('/', $encoded);
}
