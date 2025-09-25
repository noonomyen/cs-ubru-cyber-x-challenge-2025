<?php
declare(strict_types=1);

if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Invalid bootstrap sequence.');
}

return [
    'title' => 'Ruby On Rails',
    'hero_badge' => 'B-SOC Case File',
    'hero_description' => 'Trace the adversary\'s footprint and answer each investigative step in order. Close all stages to secure the flag for your team.',
    'worksheet_title' => 'Incident Response Worksheet',
    'download' => [
        'href' => 'download.php',
        'label' => 'Download log',
        'title' => 'Download nginxLog_easy.zip',
        'filename' => 'nginxLog_easy.zip',
    ],
    'rate_limit' => [
        'limit' => 30,
        'window' => 30,
    ],
    // Provide a custom flag per deployment. When null the value falls back to
    // $GZCTF_FLAG or the hard-coded default.
    'flag' => null,
    'questions' => require __DIR__ . '/questions.php',
];
