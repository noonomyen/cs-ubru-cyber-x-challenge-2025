<?php
declare(strict_types=1);

if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(404);
    exit;
}

return [
    [
        'prompt' => '1) IP Address ของ Attacker คืออะไร?',
        'placeholder' => 'เช่น 10.0.0.1',
        'answer' => '10.42.0.1',
    ],
    [
        'prompt' => '2) Attacker Upload ไฟล์ไหนถึงสามารถ command injection ได้',
        'placeholder' => 'shell.php',
        'answer' => 'Arona.php',
    ],
    [
        'prompt' => '3) คำสั่งแรกที่ Attacker ใช้คืออะไร?',
        'placeholder' => 'เช่น pwd',
        'answer' => 'ls',
    ]
];
