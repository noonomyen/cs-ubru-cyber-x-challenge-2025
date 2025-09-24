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
        'answer' => '192.168.35.1',
    ],
    [
        'prompt' => '2) IP Address ของ Victim คืออะไร?',
        'placeholder' => 'เช่น 10.0.0.1',
        'answer' => '192.168.35.2',
    ],
    [
        'prompt' => '3) ssh ถูกเปิดใช้งานบน Port ไหน?',
        'placeholder' => 'เช่น 8080',
        'answer' => '22',
    ],
    [
        'prompt' => '4) User ที่ถูก hack ชื่ออะไร?',
        'placeholder' => 'เช่น admin',
        'answer' => 'jordan23',
    ],
    [
        'prompt' => '5) หลังจาก Attacker ได้เข้าไปในเครื่อง Victim แล้ว Attacker ได้สร้าง User สำหรับ Login โดยที่ใช้ Certificate User นั้นชื่ออะไร?',
        'placeholder' => 'เช่น admin',
        'answer' => 'whiterabbit',
    ],
];
