<?php
declare(strict_types=1);

const APP_BOOTSTRAPPED = true;

require_once __DIR__ . '/lib.php';

$config = loadChallengeConfig();
$downloadSettings = $config['download'] ?? [];
if (!is_array($downloadSettings)) {
    $downloadSettings = [];
}
$downloadFilename = is_string($downloadSettings['filename'] ?? null) && trim((string) $downloadSettings['filename']) !== ''
    ? basename(trim((string) $downloadSettings['filename']))
    : 'LogFile.zip';

$attachmentsDir = realpath(__DIR__ . '/../attachments');

if ($attachmentsDir === false) {
    $attachmentsDir = realpath(__DIR__ . '/attachments');
}

$candidatePath = ($attachmentsDir !== false ? $attachmentsDir : __DIR__) . '/' . $downloadFilename;
$targetFile = realpath($candidatePath);

if ($attachmentsDir === false || $targetFile === false) {
    http_response_code(404);
    echo 'Log file not found.';
    exit;
}

if (strpos($targetFile, $attachmentsDir) !== 0 || !is_file($targetFile) || !is_readable($targetFile)) {
    http_response_code(404);
    echo 'Log file not found.';
    exit;
}

$filename = $downloadFilename;
$filesize = filesize($targetFile);

header('Content-Type: application/octet-stream');
if ($filesize !== false) {
    header('Content-Length: ' . (string) $filesize);
}
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, max-age=0');

$handle = fopen($targetFile, 'rb');
if ($handle === false) {
    http_response_code(500);
    echo 'Unable to read log file.';
    exit;
}

while (!feof($handle)) {
    $chunk = fread($handle, 8192);
    if ($chunk === false) {
        break;
    }
    echo $chunk;
}

fclose($handle);
exit;
