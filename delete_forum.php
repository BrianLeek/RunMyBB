<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$forum_id = intval($_GET['id']);

// Fetch forum details
$stmt = $conn->prepare("SELECT database_name, subdomain FROM forums WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $forum_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($database_name, $subdomain);
$stmt->fetch();
$stmt->close();

if (!$database_name || !$subdomain) {
    die("Forum not found or you don't have permission.");
}

// Drop database
$conn->query("DROP DATABASE IF EXISTS `$database_name`");

// Delete forum directory (ensure correct path)
$forum_dir = __DIR__ . "/forum/" . $subdomain;

function deleteDir($dir) {
    if (!file_exists($dir)) return;
    foreach (array_diff(scandir($dir), ['.', '..']) as $file) {
        (is_dir("$dir/$file")) ? deleteDir("$dir/$file") : unlink("$dir/$file");
    }
    rmdir($dir);
}
deleteDir($forum_dir);

// Delete forum from database
$stmt = $conn->prepare("DELETE FROM forums WHERE id=?");
$stmt->bind_param("i", $forum_id);
$stmt->execute();

header("Location: dashboard.php?deleted=true");
exit();
