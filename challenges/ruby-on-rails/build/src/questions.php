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
        'answer' => '172.18.0.1',
    ],
    [
        'prompt' => '2) Attacker เริ่มโจมตีวันไหน',
        'placeholder' => 'เช่น 12/May/2022',
        'answer' => '25/Sep/2025',
    ],
    [
        'prompt' => '3) Attacker ใช้ช่องโหว่อะไรในการโจมตี?',
        'placeholder' => 'เช่น Sql Injection',
        'answer' => 'Path Traversal',
    ],
    [
        'prompt' => '4) ไฟล์ไหนที่ Attacker Path Traversal สำเร็จเป็นไฟล์แรก?',
        'placeholder' => 'เช่น /home/user/victom/file.txt',
        'answer' => '/etc/passwd',
    ],
    [
        'prompt' => '5) ไฟล์ไหนที่ Attacker ต้องการจริงๆ ?',
        'placeholder' => 'เช่น index.php',
        'answer' => 'nuclearLaunchCode',
        'helper' => 'ตอบแค่ชื่อไฟล์',
    ],
];
