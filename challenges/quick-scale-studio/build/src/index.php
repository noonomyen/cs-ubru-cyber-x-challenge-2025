<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

$message = '';
$convertedFile = null;
$resizedPreview = null;
$resizeOptions = [
    '25' => '25%',
    '50' => '50%',
    '75' => '75%',
    '100' => '100%',
];
$selectedScale = $_COOKIE['qs_scale'] ?? '50';
if (!isset($resizeOptions[$selectedScale])) {
    $selectedScale = '50';
}
if (isset($_POST['scale']) && isset($resizeOptions[$_POST['scale']])) {
    $selectedScale = $_POST['scale'];
    setcookie('qs_scale', $selectedScale, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function detectMagicFormat(string $path): ?string
{
    if (!is_file($path) || !is_readable($path)) {
        return null;
    }

    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        return null;
    }

    $header = fread($handle, 8) ?: '';
    $tail = '';
    $size = filesize($path);
    if ($size !== false && $size >= 2 && fseek($handle, -2, SEEK_END) === 0) {
        $tail = fread($handle, 2) ?: '';
    }

    fclose($handle);

    if (strncmp($header, "\x89PNG\r\n\x1a\n", 8) === 0) {
        return 'png';
    }

    if (strncmp($header, 'GIF87a', 6) === 0 || strncmp($header, 'GIF89a', 6) === 0) {
        return 'gif';
    }

    if (strncmp($header, "\xFF\xD8", 2) === 0 && $tail === "\xFF\xD9") {
        return 'jpg';
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload failed: missing file or upload error.';
    } else {
        $uploadsDir = __DIR__ . '/uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $originalName = $_FILES['upload']['name'] ?? 'file';
        $sanitizedName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', $originalName);
        $targetName = bin2hex(random_bytes(8)) . '_' . $sanitizedName;
        $targetPath = $uploadsDir . '/' . $targetName;

        if (!move_uploaded_file($_FILES['upload']['tmp_name'], $targetPath)) {
            $message = 'Upload failed: could not move file.';
        } else {
            $detectedFormat = detectMagicFormat($targetPath);
            if ($detectedFormat === null) {
                $message = 'Upload rejected: unsupported or invalid file signature.';
                @unlink($targetPath);
            } else {
                $processedDir = __DIR__ . '/processed';
                if (!is_dir($processedDir)) {
                    mkdir($processedDir, 0755, true);
                }

                $convertedName = pathinfo($targetName, PATHINFO_FILENAME) . '_scaled.png';
                $convertedPath = $processedDir . '/' . $convertedName;

                $cmd = 'magick convert ' . escapeshellarg($targetPath) . ' -resize ' . escapeshellarg($resizeOptions[$selectedScale]) . ' ' . escapeshellarg($convertedPath);
                $convertOutput = [];
                $exitCode = 0;
                exec($cmd . ' 2>&1', $convertOutput, $exitCode);

                if ($exitCode !== 0 || !is_file($convertedPath)) {
                    $message = 'Processing failed: ImageMagick convert error.';
                    error_log('convert failed: ' . implode("\n", $convertOutput));
                    @unlink($convertedPath);
                } else {
                    $message = 'Image resized successfully at ' . $resizeOptions[$selectedScale] . '.';
                    $convertedFile = 'processed/' . basename($convertedPath);
                    $resizedPreview = $convertedFile . '?t=' . time();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QuickScale Studio</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 48rem; margin: 2rem auto; padding: 0 1rem 3rem; background: #f5f7fb; color: #1d2a3a; }
        header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        header svg { width: 2.5rem; height: 2.5rem; fill: #2e5aac; }
        h1 { margin: 0; font-size: 2rem; }
        .card { background: #fff; border-radius: 0.75rem; box-shadow: 0 15px 40px rgba(16, 40, 80, 0.08); padding: 2rem; }
        .message { padding: 0.75rem 1rem; background: #e9f4ff; border: 1px solid #c2dbff; margin-bottom: 1.5rem; border-radius: 0.5rem; }
        form { display: flex; flex-direction: column; gap: 1.25rem; }
        .field { display: flex; flex-direction: column; gap: 0.5rem; }
        label { font-weight: 600; }
        input[type="file"], select { padding: 0.75rem; border: 1px solid #cdd7e6; border-radius: 0.5rem; background: #f8fbff; }
        button { padding: 0.9rem 1rem; background: linear-gradient(135deg, #3561d1, #2748a0); color: #fff; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 1rem; font-weight: 600; }
        button:hover { background: linear-gradient(135deg, #2d53ba, #1f3c8a); }
        .preview { margin-top: 2rem; display: flex; flex-direction: column; gap: 0.75rem; align-items: flex-start; }
        .preview img { max-width: 100%; border-radius: 0.5rem; border: 1px solid #d7e1f1; box-shadow: 0 10px 25px rgba(13, 37, 70, 0.08); }
        .downloads { display: flex; gap: 1rem; align-items: center; }
        a.download-link { padding: 0.75rem 1.25rem; background: #1f6b3a; color: #fff; border-radius: 0.5rem; text-decoration: none; font-weight: 600; }
        a.download-link:hover { background: #195730; }
        small { color: #4c5d75; }
        footer { margin-top: 2.5rem; text-align: center; font-size: 0.9rem; color: #5a6d82; }
        footer span { font-weight: 600; color: #294787; }
    </style>
</head>
<body>
    <header>
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3l4 6h-8l4-6zm0 18l-4-6h8l-4 6zm9-6l-3-4.5 3-4.5 3 4.5-3 4.5zm-18 0L0 10.5 3 6l3 4.5L3 15zm9 0c-1.933 0-3.5-1.567-3.5-3.5S9.067 8.5 11 8.5s3.5 1.567 3.5 3.5S12.933 15 11 15z"/>
        </svg>
        <h1>QuickScale Studio</h1>
    </header>
    <div class="card">
        <p>Upload a photo, choose how much you want to resize it (25% – 100%), and we will generate the scaled version instantly.</p>

        <?php if ($message !== ''): ?>
            <div class="message">
                <strong><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="field">
                <label for="upload">Choose image</label>
                <input type="file" name="upload" id="upload" accept="image/*" required />
                <small>Supported formats: PNG, JPEG, GIF</small>
            </div>

            <div class="field">
                <label for="scale">Resize to</label>
                <select name="scale" id="scale">
                    <?php foreach ($resizeOptions as $value => $label): ?>
                        <?php $isSelected = ((string) $selectedScale === (string) $value); ?>
                        <option value="<?php echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isSelected ? 'selected="selected"' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Resize Image</button>
        </form>
    </div>

    <?php if ($convertedFile !== null): ?>
        <div class="preview">
            <div class="downloads">
                <a class="download-link" href="<?php echo htmlspecialchars($convertedFile, ENT_QUOTES, 'UTF-8'); ?>" download>Download resized image</a>
                <small>Saved as PNG at the selected scale.</small>
            </div>
            <img src="<?php echo htmlspecialchars($resizedPreview, ENT_QUOTES, 'UTF-8'); ?>" alt="Resized preview" />
        </div>
    <?php endif; ?>

    <footer>
        Powered by <span>ImageMagick</span>
    </footer>
</body>
</html>
