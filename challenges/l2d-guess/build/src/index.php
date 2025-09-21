<?php
declare(strict_types=1);

require_once __DIR__ . '/lib.php';

const TOTAL_ROUNDS = 10;

session_name('L2DGuessSession');
session_start();

$env = loadEnv(__DIR__ . '/.env');

if (isset($_GET['asset'])) {
    serveAsset($env);
    exit;
}

$fatalError = null;
if (!isset($env['R2_ACCESS_KEY_ID'], $env['R2_SECRET_ACCESS_KEY'], $env['R2_ENDPOINT'], $env['R2_BUCKET'])) {
    $fatalError = 'R2 credentials are missing. Please configure them in .env.';
}

$message = null;
$finalMessage = null;
$finalGame = null;
$game = $_SESSION['game'] ?? null;
$completedGame = $_SESSION['completed_game'] ?? null;

if ($fatalError === null) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!is_array($game)) {
            [$game, $message] = startNewGame($env);
        }

        if (is_array($game) && !$game['finished']) {
            $guess = $_POST['guess'] ?? '';
            $guessIndex = isset($_POST['round']) ? (int) $_POST['round'] : $game['current'];
            $game = evaluateGuess($game, $guess, $guessIndex);
            $_SESSION['game'] = $game;

            if ($game['finished']) {
                $_SESSION['completed_game'] = $game;
                $_SESSION['game'] = null;
                header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
                exit;
            }
        }
    } else {
        [$game, $message] = startNewGame($env);
        $_SESSION['game'] = $game;
    }

    if (is_array($completedGame)) {
        unset($_SESSION['completed_game']);
        $finalGame = $completedGame;
    } elseif (is_array($game) && $game['finished']) {
        $finalGame = $game;
    }

    if (is_array($finalGame)) {
        if ($finalGame['failed']) {
            $finalMessage = 'น่าเสียดาย! คุณตอบผิดอย่างน้อยหนึ่งข้อ ลองใหม่อีกครั้งนะ!';
        } else {
            $finalMessage = 'ยินดีด้วย! คุณคือ Blue Archive Big Fan!';
        }
    }
}

?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>L2D Guess</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            color-scheme: light;
        }
        body {
            margin: 0;
            font-family: 'Kanit', 'Segoe UI', sans-serif;
            background: #f0f4ff;
            color: #0f1a2b;
        }
        header {
            background: linear-gradient(135deg, #6c8dff, #3247d7);
            color: #fff;
            padding: 2.5rem 1.25rem;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 2.2rem;
            letter-spacing: 0.04em;
        }
        main {
            max-width: 720px;
            margin: -2rem auto 3rem;
            padding: 0 1.5rem;
        }
        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 1.25rem;
            box-shadow: 0 25px 70px rgba(29, 49, 120, 0.15);
        }
        .message {
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            background: #eaf2ff;
            border: 1px solid #c5d9ff;
        }
        .message.error {
            background: #ffeceb;
            border-color: #ffb8b3;
            color: #8f1b12;
        }
        .game-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            font-weight: 500;
        }
        .game-status span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        figure {
            margin: 0 0 1.5rem;
            background: #f7f9ff;
            border: 1px solid #d7e3ff;
            border-radius: 1rem;
            padding: 1.25rem;
            text-align: center;
        }
        figure img {
            max-width: 100%;
            border-radius: 0.75rem;
            box-shadow: 0 15px 35px rgba(35, 59, 140, 0.18);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        label {
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        input[type="text"] {
            padding: 0.9rem 1rem;
            border: 1px solid #cbd6f1;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #5d79ff;
            box-shadow: 0 0 0 3px rgba(80, 115, 255, 0.2);
        }
        button {
            align-self: flex-start;
            padding: 0.85rem 1.75rem;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #5b70ff, #2f40c8);
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 25px rgba(47, 64, 200, 0.25);
        }
        button:disabled {
            background: #95a1e4;
            cursor: not-allowed;
            box-shadow: none;
        }
        .results {
            margin-top: 2rem;
        }
        .results table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        .results th, .results td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #e0e7ff;
            text-align: left;
        }
        .results tr:last-child td {
            border-bottom: none;
        }
        footer {
            margin-top: 2.5rem;
            text-align: center;
            color: #5c6da9;
            font-size: 0.9rem;
        }
        .hint {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #4f5e8f;
        }
    </style>
</head>
<body>
    <header>
        <h1>L2D Guess Challenge</h1>
        <p>มาลองทายชื่อนักเรียน จากภาพ L2D กันเถอะ!</p>
    </header>
    <main>
        <section class="card">
            <?php if ($fatalError !== null): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($fatalError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php else: ?>
                <?php if ($message !== null): ?>
                    <div class="message">
                        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($finalMessage !== null && is_array($finalGame)): ?>
                    <div class="message">
                        <strong><?php echo htmlspecialchars($finalMessage, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <div>คุณตอบได้ <?php echo htmlspecialchars((string) ($finalGame['score'] ?? 0), ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars((string) ($finalGame['total'] ?? TOTAL_ROUNDS), ENT_QUOTES, 'UTF-8'); ?> ข้อ</div>
                        <form method="get" style="margin-top: 1rem;">
                            <button type="submit">เล่นอีกครั้ง</button>
                        </form>
                    </div>
                    <?php if (!empty($finalGame['history'])): ?>
                        <div class="results">
                            <h2>รายละเอียดรอบที่ผ่านมา</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th>รอบ</th>
                                        <th>คำตอบที่ถูก</th>
                                        <th>คำตอบของคุณ</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($finalGame['history'] as $index => $entry): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars((string) ($index + 1), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($entry['answer_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($entry['guess_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo $entry['correct'] ? '✅' : '❌'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php elseif (is_array($game) && !$game['finished'] && isset($game['rounds'][$game['current']])): ?>
                    <div class="game-status">
                        <span>รอบ <?php echo htmlspecialchars((string) ($game['current'] + 1), ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars((string) $game['total'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span>คะแนน: <?php echo htmlspecialchars((string) $game['score'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <figure>
                        <img src="<?php echo htmlspecialchars(buildAssetUrl($game['rounds'][$game['current']]['key']), ENT_QUOTES, 'UTF-8'); ?>" alt="Light & Delight Guess" />
                    </figure>
                    <form method="post" autocomplete="off">
                        <label for="guess">ชื่อนักเรียนคือ?</label>
                        <input type="text" id="guess" name="guess" placeholder="เช่น Arisu" required autofocus>
                        <input type="hidden" name="round" value="<?php echo htmlspecialchars((string) $game['current'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit">ส่งคำตอบ</button>
                    </form>
                <?php else: ?>
                    <div class="message error">ไม่สามารถเริ่มเกมได้ในตอนนี้ ลองรีเฟรชดูอีกครั้ง</div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
