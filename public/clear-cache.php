<?php
// ⚠️ TEMPORARY FILE - Delete after use!
// Visit: https://yoursite.com/clear-cache.php to clear cache

$secret = $_GET['secret'] ?? '';
if ($secret !== 'jhrclear2024') {
    die('Access denied. Use ?secret=jhrclear2024');
}

// Change this to your actual project root path on the server
$appRoot = dirname(__DIR__);
chdir($appRoot);

$output = [];

// Clear file-based cache manually
$cacheDir = $appRoot . '/storage/framework/cache/data';
if (is_dir($cacheDir)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($files as $file) {
        if ($file->isFile()) {
            unlink($file->getRealPath());
            $count++;
        }
    }
    $output[] = "✅ Cleared $count cache files from storage/framework/cache/data";
}

// Clear views
$viewsDir = $appRoot . '/storage/framework/views';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '/*.php');
    foreach ($files as $file) {
        unlink($file);
    }
    $output[] = "✅ Cleared " . count($files) . " compiled views";
}

// Try artisan via exec
$artisanOut = [];
@exec("php $appRoot/artisan cache:clear 2>&1", $artisanOut);
if (!empty($artisanOut)) {
    $output[] = "Artisan: " . implode(' ', $artisanOut);
}

echo '<pre style="font-family:monospace;padding:20px;background:#1a1a2e;color:#00ff88;font-size:14px;">';
echo "JHR Bazar Cache Clear\n";
echo "===================\n\n";
foreach ($output as $line) {
    echo $line . "\n";
}
echo "\n✅ Done! Delete this file immediately!\n";
echo '</pre>';
?>
