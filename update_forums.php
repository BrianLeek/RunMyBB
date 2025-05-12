<?php
set_time_limit(0);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$latestMyBBPath = __DIR__ . '/mybb_latest'; // Folder containing the latest MyBB
$forumsRoot = __DIR__ . '/forum';           // Folder containing all forums
$excluded = ['inc/settings.php', 'inc/config.php']; // Files to skip

function recursiveUpdate($src, $dst, $excluded) {
    $src = rtrim($src, '/');
    $dst = rtrim($dst, '/');

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($items as $item) {
        $relPath = str_replace($src . '/', '', $item->getPathname());
        $targetPath = $dst . '/' . $relPath;

        if (in_array(str_replace('\\', '/', $relPath), $excluded)) {
            continue;
        }

        if ($item->isDir()) {
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
        } else {
            copy($item->getPathname(), $targetPath);
        }
    }
}

// Scan all forums and apply update
$forums = array_filter(glob($forumsRoot . '/*'), 'is_dir');
$updated = [];

foreach ($forums as $forumPath) {
    recursiveUpdate($latestMyBBPath, $forumPath, $excluded);

    // Add suspension check to global.php if not present
    $globalPath = $forumPath . '/global.php';
    if (file_exists($globalPath)) {
        $content = file_get_contents($globalPath);
        if (strpos($content, 'SELECT status FROM forums WHERE subdomain=') === false) {
            $injection = <<<PHP
<?php
include '../../config.php';
\$subdomain = basename(__DIR__);
\$stmt = \$conn->prepare("SELECT status FROM forums WHERE subdomain=?");
\$stmt->bind_param("s", \$subdomain);
\$stmt->execute();
\$stmt->bind_result(\$status);
\$stmt->fetch();
if (\$status == 'suspended') {
    echo "<h1>This forum has been suspended by the forum administrator.</h1>";
    exit();
}
?>
PHP;
            $content = $injection . "\n" . $content;
            file_put_contents($globalPath, $content);
        }
    }

    // Modify settings.php visibility
    $settingsPath = $forumPath . '/admin/modules/config/settings.php';
    if (file_exists($settingsPath)) {
        $settingsContent = file_get_contents($settingsPath);
        if (strpos($settingsContent, "cookiepath") !== false) {
            // Already modified
        } else {
            $search = "foreach(\$cache_settings[\$groupinfo['gid']] as \$setting)";
            $replace = <<<PHP
foreach(\$cache_settings[\$groupinfo['gid']] as \$setting)
{
    if(in_array(\$setting['name'], ['cookiepath','cookiedomain','cookieprefix'])) continue;
PHP;
            $settingsContent = str_replace($search, $replace, $settingsContent);
            file_put_contents($settingsPath, $settingsContent);
        }
    }

    $updated[] = basename($forumPath);
}

echo "<h2>✅ Forums Updated:</h2><ul>";
foreach ($updated as $forum) {
    echo "<li>$forum</li>";
}
echo "</ul>";
?>
