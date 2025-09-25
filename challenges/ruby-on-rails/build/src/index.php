<?php
declare(strict_types=1);

const APP_BOOTSTRAPPED = true;

$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 0,
    'path' => $cookieParams['path'] ?? '/',
    'domain' => $cookieParams['domain'] ?? '',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_name('PASSWORDLEAKSession');
session_start();

require_once __DIR__ . '/lib.php';

$config = loadChallengeConfig();
$questions = getQuestions();
$totalQuestions = count($questions);

$pageTitle = is_string($config['title'] ?? null) && trim((string) $config['title']) !== ''
    ? trim((string) $config['title'])
    : 'Blue Team Incident Desk';
$heroBadge = is_string($config['hero_badge'] ?? null) && trim((string) $config['hero_badge']) !== ''
    ? trim((string) $config['hero_badge'])
    : 'B-SOC Case File';
$heroDescription = is_string($config['hero_description'] ?? null) && trim((string) $config['hero_description']) !== ''
    ? trim((string) $config['hero_description'])
    : 'Trace the adversary\'s footprint and answer each investigative step in order. Close all stages to secure the flag for your team.';
$worksheetTitle = is_string($config['worksheet_title'] ?? null) && trim((string) $config['worksheet_title']) !== ''
    ? trim((string) $config['worksheet_title'])
    : 'Incident Response Worksheet';

$downloadSettings = $config['download'] ?? [];
if (!is_array($downloadSettings)) {
    $downloadSettings = [];
}
$downloadHref = is_string($downloadSettings['href'] ?? null) && trim((string) $downloadSettings['href']) !== ''
    ? trim((string) $downloadSettings['href'])
    : 'download.php';
$downloadLabel = is_string($downloadSettings['label'] ?? null) && trim((string) $downloadSettings['label']) !== ''
    ? trim((string) $downloadSettings['label'])
    : 'Download log';
$downloadTitle = is_string($downloadSettings['title'] ?? null) && trim((string) $downloadSettings['title']) !== ''
    ? trim((string) $downloadSettings['title'])
    : 'Download LogFile.zip';

$rateLimitSettings = $config['rate_limit'] ?? [];
if (!is_array($rateLimitSettings)) {
    $rateLimitSettings = [];
}
$rateLimitMax = isset($rateLimitSettings['limit']) ? (int) $rateLimitSettings['limit'] : 30;
$rateLimitWindow = isset($rateLimitSettings['window']) ? (int) $rateLimitSettings['window'] : 30;

if ($totalQuestions === 0) {
    http_response_code(500);
    echo '<h1>Configuration error</h1>';
    echo '<p>No questions defined. Please update <code>questions.php</code>.</p>';
    exit;
}

$rawState = $_SESSION['state'] ?? null;
$isNewState = !is_array($rawState);
$state = ensureValidState($rawState, $totalQuestions);
$_SESSION['state'] = $state;

if ($isNewState) {
    session_regenerate_id(true);
}

$flash = $_SESSION['flash'] ?? null;
if (!is_array($flash)) {
    $flash = [];
}
unset($_SESSION['flash']);

$errorMessage = isset($flash['error']) && is_string($flash['error']) ? $flash['error'] : null;
$infoMessage = isset($flash['info']) && is_string($flash['info']) ? $flash['info'] : null;
$trophyMessage = isset($flash['trophy']) && is_string($flash['trophy']) ? $flash['trophy'] : null;
$redirectRequired = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) && is_string($_POST['action']) ? $_POST['action'] : 'answer';
    $token = isset($_POST['token']) && is_string($_POST['token']) ? $_POST['token'] : '';

    if (!hash_equals($state['token'], $token)) {
        regenerateToken($state);
        $_SESSION['state'] = $state;
        $_SESSION['flash'] = ['error' => 'Invalid session token. Please refresh the page and try again.'];
        $redirectRequired = true;
    } else {
        if ($action === 'reset') {
            clearProgress();
            $_SESSION['flash'] = ['info' => 'Investigation reset. Ready for another run.'];
            $redirectRequired = true;
        } elseif ($action === 'answer') {
            if ($state['completed']) {
                regenerateToken($state);
                $_SESSION['state'] = $state;
                $_SESSION['flash'] = ['info' => 'This run has already completed. Start a new round to play again.'];
                $redirectRequired = true;
            } else {
                $answer = isset($_POST['answer']) && is_string($_POST['answer']) ? trim($_POST['answer']) : '';

                $currentIndex = $state['index'];
                if ($currentIndex >= 0 && $currentIndex < $totalQuestions) {
                    if (!checkAnswerRateLimit($rateLimitMax, $rateLimitWindow)) {
                        regenerateToken($state);
                        $_SESSION['state'] = $state;
                        $_SESSION['flash'] = ['error' => 'Answer limit reached. Please wait a moment before trying again.'];
                        $redirectRequired = true;
                    } else {
                        $question = $questions[$currentIndex];
                        $isCorrect = answersMatch($answer, $question['answer']);

                        if ($isCorrect) {
                            $state['index']++;
                            if ($state['index'] >= $totalQuestions) {
                                $state['completed'] = true;
                                $state['failed'] = false;
                                $_SESSION['flash'] = ['trophy' => 'Mission accomplished! Claim your reward.'];
                            } else {
                                $_SESSION['flash'] = ['trophy' => 'Stage cleared! Trophy secured—keep hunting.'];
                            }
                            regenerateToken($state);
                            $_SESSION['state'] = $state;
                            $redirectRequired = true;
                        } else {
                            $state['completed'] = false;
                            $state['failed'] = false;
                            regenerateToken($state);
                            $_SESSION['state'] = $state;
                            $_SESSION['flash'] = ['error' => 'Not quite right. Review the evidence and try again.'];
                            $redirectRequired = true;
                        }
                    }
                }
            }
        }
    }

    if ($redirectRequired) {
        $destination = strtok($_SERVER['REQUEST_URI'], '?') ?: '/';
        header('Location: ' . $destination);
        exit;
    }
}

$state = $_SESSION['state'];
$currentQuestion = null;
if (!$state['completed'] && $state['index'] < $totalQuestions) {
    $currentQuestion = $questions[$state['index']];
}

$flag = $state['completed'] && !$state['failed'] ? flagValue() : null;
$totalCleared = max(0, min($state['index'], $totalQuestions));
$currentStageLabel = $state['completed'] ? 'Complete' : 'Stage ' . (string) ($state['index'] + 1);
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title><?php echo esc($pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            :root {
                color-scheme: light;
            --bg: #070b18;
            --bg-alt: #0d142c;
            --text: #edf1ff;
            --muted: rgba(205, 216, 255, 0.68);
            --highlight: #63f5ff;
            --accent: #5f7bff;
            --accent-soft: rgba(98, 126, 255, 0.22);
            --border: rgba(130, 150, 230, 0.25);
            --danger: #ff6f76;
            --success: #8bf5c4;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Prompt', 'Kanit', 'Segoe UI', Tahoma, sans-serif;
            background: radial-gradient(circle at top left, rgba(114, 180, 255, 0.18) 0%, transparent 55%),
                radial-gradient(circle at bottom right, rgba(121, 99, 255, 0.2) 0%, transparent 55%),
                var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('data:image/svg+xml,%3Csvg width="160" height="160" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3ClinearGradient id="g" x1="0%25" y1="0%25" x2="100%25" y2="100%25"%3E%3Cstop offset="0%25" stop-color="%238aa4ff" stop-opacity="0.15"/%3E%3Cstop offset="100%25" stop-color="%233458ff" stop-opacity="0"/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width="160" height="160" fill="url(%23g)"/%3E%3Cpath d="M80 0L90 30H70L80 0ZM130 30L140 60H120L130 30ZM30 30L40 60H20L30 30ZM80 60L90 90H70L80 60ZM30 90L40 120H20L30 90ZM130 90L140 120H120L130 90Z" fill="rgba(105, 137, 255, 0.08)"/%3E%3C/svg%3E');
            opacity: 0.65;
            pointer-events: none;
            mix-blend-mode: screen;
        }
        .app-shell {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .hero {
            padding: 3rem 1.5rem 2.75rem;
        }
        .hero__grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2.5rem;
            align-items: center;
        }
        .hero__badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 999px;
            background: rgba(99, 245, 255, 0.12);
            border: 1px solid rgba(99, 245, 255, 0.3);
            color: var(--highlight);
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .hero h1 {
            margin: 1rem 0 1rem;
            font-size: 2.6rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .hero p {
            margin: 0;
            color: var(--muted);
            font-size: 1.05rem;
            max-width: 36rem;
        }
        .hero__stats {
            display: grid;
            gap: 1rem;
            align-self: stretch;
        }
        .stat-card {
            padding: 1rem 1.25rem;
            border-radius: 1.2rem;
            background: linear-gradient(130deg, rgba(22, 32, 68, 0.85), rgba(28, 40, 84, 0.65));
            border: 1px solid rgba(120, 145, 240, 0.35);
            box-shadow: 0 25px 60px rgba(8, 12, 28, 0.6);
        }
        .stat-card__label {
            font-size: 0.85rem;
            color: rgba(210, 220, 250, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .stat-card__value {
            display: block;
            margin-top: 0.45rem;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--highlight);
        }
        .content {
            flex: 1 1 auto;
            padding: 0 1.5rem 3rem;
        }
        .content-inner {
            max-width: 960px;
            margin: 0 auto;
        }
        .panel {
            background: linear-gradient(145deg, rgba(12, 18, 38, 0.92), rgba(14, 26, 52, 0.78));
            border-radius: 1.6rem;
            padding: 2.25rem 2.4rem;
            box-shadow: 0 30px 70px rgba(3, 10, 26, 0.7);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(99, 128, 255, 0.25), transparent 55%);
            opacity: 0.7;
            pointer-events: none;
        }
        .panel > * {
            position: relative;
            z-index: 1;
        }
        .board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .board-header strong {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
        }
        .board-header__right {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.6rem;
        }
        .chip {
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: rgba(99, 245, 255, 0.14);
            border: 1px solid rgba(99, 245, 255, 0.25);
            color: rgba(210, 240, 255, 0.85);
            font-size: 0.85rem;
            letter-spacing: 0.04em;
        }
        .link-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.32rem 0.7rem;
            border-radius: 999px;
            background: rgba(34, 54, 96, 0.55);
            border: 1px solid rgba(115, 160, 255, 0.3);
            color: rgba(200, 218, 255, 0.88);
            font-size: 0.82rem;
            text-decoration: none;
            transition: background 0.2s ease, border 0.2s ease, transform 0.2s ease;
        }
        .link-chip svg {
            width: 14px;
            height: 14px;
            fill: currentColor;
        }
        .link-chip:hover {
            background: rgba(60, 90, 155, 0.7);
            border-color: rgba(160, 200, 255, 0.45);
            transform: translateY(-1px);
        }
        .alert {
            margin-bottom: 1.3rem;
            padding: 1rem 1.2rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 120, 120, 0.35);
            background: rgba(255, 110, 118, 0.12);
            color: rgba(255, 207, 210, 0.9);
        }
        .alert.info {
            border-color: rgba(110, 190, 255, 0.35);
            background: rgba(90, 150, 255, 0.14);
            color: rgba(195, 225, 255, 0.9);
        }
        .trophy-overlay {
            position: fixed;
            inset: 0;
            background: rgba(3, 6, 14, 0.72);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 1.5rem;
            opacity: 1;
            transition: opacity 0.22s ease;
        }
        .trophy-overlay.is-hiding {
            opacity: 0;
            pointer-events: none;
        }
        .trophy-card {
            position: relative;
            max-width: 420px;
            width: 100%;
            background: linear-gradient(150deg, rgba(20, 34, 72, 0.94), rgba(10, 18, 42, 0.88));
            border-radius: 1.6rem;
            padding: 2.2rem 2rem 2.4rem;
            border: 1px solid rgba(150, 180, 255, 0.35);
            box-shadow: 0 30px 70px rgba(4, 10, 26, 0.75);
            text-align: center;
            overflow: hidden;
        }
        .trophy-card::after {
            content: '';
            position: absolute;
            inset: -30% -30% auto -30%;
            height: 60%;
            background: radial-gradient(circle, rgba(255, 215, 120, 0.35) 0%, transparent 65%);
            opacity: 0.8;
            pointer-events: none;
        }
        .trophy-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 1.2rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd972, #ffb74b);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 45px rgba(255, 198, 92, 0.45);
        }
        .trophy-icon svg {
            width: 38px;
            height: 38px;
            fill: #513400;
        }
        .trophy-card h2 {
            margin: 0 0 0.6rem;
            font-size: 1.9rem;
            color: #ffeacf;
            letter-spacing: 0.03em;
        }
        .trophy-card p {
            margin: 0 0 1.4rem;
            color: rgba(240, 225, 200, 0.9);
            font-size: 1.05rem;
        }
        .trophy-dismiss {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.75rem 1.5rem;
            border-radius: 999px;
            border: none;
            font-weight: 600;
            letter-spacing: 0.03em;
            cursor: pointer;
            background: linear-gradient(135deg, #63f5ff, #5f7bff);
            color: #051326;
            box-shadow: 0 15px 35px rgba(40, 140, 255, 0.35);
            transition: transform 0.2s ease;
        }
        .trophy-dismiss:hover {
            transform: translateY(-1px);
        }
        form {
            display: grid;
            gap: 1.2rem;
        }
        label {
            font-size: 1.1rem;
            font-weight: 600;
            color: rgba(235, 240, 255, 0.92);
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 1rem 1.2rem;
            border-radius: 1rem;
            border: 1px solid rgba(130, 155, 240, 0.35);
            background: rgba(10, 16, 34, 0.85);
            color: var(--text);
            font-size: 1rem;
            transition: border 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: rgba(99, 245, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(99, 245, 255, 0.2);
            transform: translateY(-1px);
        }
        textarea {
            min-height: 140px;
            resize: vertical;
        }
        .field-note {
            margin: -0.5rem 0 0;
            font-size: 0.9rem;
            color: rgba(180, 205, 255, 0.65);
        }
        .btn {
            justify-self: flex-start;
            padding: 0.9rem 2rem;
            border-radius: 999px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: linear-gradient(135deg, var(--highlight), var(--accent));
            color: #04112a;
            box-shadow: 0 18px 45px rgba(20, 120, 255, 0.35);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 55px rgba(20, 120, 255, 0.45);
        }
        .btn.secondary {
            background: transparent;
            border: 1px solid rgba(130, 160, 255, 0.45);
            color: rgba(215, 225, 255, 0.9);
            box-shadow: none;
        }
        .result-block h2 {
            margin: 0 0 0.6rem;
            font-size: 1.7rem;
        }
        .result-block p {
            margin: 0 0 1rem;
            color: var(--muted);
        }
        .flag-card {
            margin: 1.5rem 0 1.8rem;
            padding: 1.2rem 1.5rem;
            border-radius: 1.1rem;
            border: 1px dashed rgba(99, 245, 210, 0.6);
            background: rgba(18, 50, 42, 0.52);
            font-family: 'Fira Code', 'Consolas', 'Courier New', monospace;
            font-size: 1.05rem;
            color: var(--success);
            word-break: break-all;
        }
        footer {
            padding: 2rem 1rem 3rem;
            text-align: center;
            color: rgba(205, 215, 240, 0.55);
            font-size: 0.85rem;
        }
        @media (max-width: 640px) {
            .panel {
                padding: 1.8rem 1.6rem;
            }
            .hero h1 {
                font-size: 2.1rem;
            }
            input[type="text"], textarea {
                font-size: 0.95rem;
            }
            .btn {
                width: 100%;
                justify-self: stretch;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<?php if ($trophyMessage !== null): ?>
    <div class="trophy-overlay" id="trophy-overlay">
        <div class="trophy-card">
            <div class="trophy-icon" aria-hidden="true">
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8h28v6h6v8c0 6.3-4.7 11.5-10.8 11.9C39.3 39.8 35 43 30 44.2V48h10v4H24v-4h10v-3.8c-5-1.2-9.3-4.4-11.2-10.3C16.7 33.5 12 28.3 12 22V14h6V8zm4 6v2h20v-2H22zm-8 6v2c0 3.3 2.7 6 6 6H20c-3.3 0-6-2.7-6-6zm36 0c0 3.3-2.7 6-6 6h0c3.3 0 6-2.7 6-6v-2z" />
                </svg>
            </div>
            <h2>Nice Work!</h2>
            <p><?php echo esc($trophyMessage); ?></p>
            <button type="button" class="trophy-dismiss" data-dismiss-trophy>Continue Investigation</button>
        </div>
    </div>
<?php endif; ?>
<div class="app-shell">
    <header class="hero">
        <div class="hero__grid">
            <div>
                <span class="hero__badge"><?php echo esc($heroBadge); ?></span>
                <h1><?php echo esc($pageTitle); ?></h1>
                <p><?php echo esc($heroDescription); ?></p>
            </div>
            <div class="hero__stats">
                <div class="stat-card">
                    <span class="stat-card__label">Steps Cleared</span>
                    <span class="stat-card__value"><?php echo esc((string) $totalCleared); ?> / <?php echo esc((string) $totalQuestions); ?></span>
                </div>
                <div class="stat-card">
                    <span class="stat-card__label">Active Stage</span>
                    <span class="stat-card__value"><?php echo esc($currentStageLabel); ?></span>
                </div>
            </div>
        </div>
    </header>
    <main class="content">
        <div class="content-inner">
            <section class="panel">
                <div class="board-header">
                    <strong><?php echo esc($worksheetTitle); ?></strong>
                    <div class="board-header__right">
                        <a class="link-chip" href="<?php echo esc($downloadHref); ?>" title="<?php echo esc($downloadTitle); ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 3a1 1 0 011 1v8.586l2.293-2.293a1 1 0 111.414 1.414l-4.004 4.004a1 1 0 01-1.414 0L7.29 11.707a1 1 0 011.414-1.414L11 12.586V4a1 1 0 011-1zm-5 14a1 1 0 100 2h10a1 1 0 100-2H7z"/></svg>
                            <?php echo esc($downloadLabel); ?>
                        </a>
                        <span class="chip">Status: <?php echo esc($currentStageLabel); ?></span>
                    </div>
                </div>

                <?php if ($errorMessage !== null): ?>
                    <div class="alert"><?php echo esc($errorMessage); ?></div>
                <?php endif; ?>
                <?php if ($infoMessage !== null): ?>
                    <div class="alert info"><?php echo esc($infoMessage); ?></div>
                <?php endif; ?>

                <?php if ($state['completed'] && !$state['failed']): ?>
                    <div class="result-block">
                        <h2>Case Closed!</h2>
                        <p>You identified every clue correctly. Take this flag back to HQ as proof of your success.</p>
                        <div class="flag-card"><?php echo esc($flag ?? ''); ?></div>
                        <form method="post">
                            <input type="hidden" name="token" value="<?php echo esc($state['token']); ?>">
                            <button type="submit" name="action" value="reset" class="btn secondary">Restart Investigation</button>
                        </form>
                    </div>
                <?php elseif ($state['completed'] && $state['failed']): ?>
                    <div class="result-block">
                        <h2>Evidence Mismatch</h2>
                        <p>Review the incident artefacts again and return with the correct answers to close the case.</p>
                        <form method="post">
                            <input type="hidden" name="token" value="<?php echo esc($state['token']); ?>">
                            <button type="submit" name="action" value="reset" class="btn">Try Again</button>
                        </form>
                    </div>
                <?php elseif ($currentQuestion !== null): ?>
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="token" value="<?php echo esc($state['token']); ?>">
                        <label for="answer"><?php echo esc($currentQuestion['prompt']); ?></label>
                        <?php if (mb_strlen($currentQuestion['prompt']) > 120): ?>
                            <textarea name="answer" id="answer" placeholder="<?php echo esc($currentQuestion['placeholder'] ?? 'Type your answer'); ?>" required></textarea>
                        <?php else: ?>
                            <input type="text" name="answer" id="answer" placeholder="<?php echo esc($currentQuestion['placeholder'] ?? 'Type your answer'); ?>" required>
                        <?php endif; ?>
                        <?php if (!empty($currentQuestion['helper'] ?? '')): ?>
                            <p class="field-note">Note: <?php echo esc((string) $currentQuestion['helper']); ?></p>
                        <?php endif; ?>
                        <button type="submit" name="action" value="answer" class="btn">Submit Answer</button>
                    </form>
                <?php endif; ?>

            </section>
        </div>
    </main>
</div>
<script>
    (function () {
        const overlay = document.getElementById('trophy-overlay');
        if (!overlay) {
            return;
        }

        const removeOverlay = () => {
            overlay.classList.add('is-hiding');
            window.setTimeout(() => {
                if (overlay && overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 240);
        };

        const dismissButton = overlay.querySelector('[data-dismiss-trophy]');
        if (dismissButton) {
            dismissButton.addEventListener('click', removeOverlay);
        }

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                removeOverlay();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                removeOverlay();
            }
        });

        window.setTimeout(removeOverlay, 3600);
    })();
</script>
</body>
</html>
