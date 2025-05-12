<?php
include 'config.php';
session_start();

$page_title = $lang['features_title'] . " - " . $lang['site_title'];
$page_desc = $lang['features_desc'];
$page_keywords = $lang['features_keywords'];

// Count forums
$forumCountQuery = "SELECT COUNT(*) AS total FROM forums";
$forumResult = $conn->query($forumCountQuery);
$forumCount = $forumResult->fetch_assoc()['total'] ?? 0;

// Count users
$userCountQuery = "SELECT COUNT(*) AS total FROM users";
$userResult = $conn->query($userCountQuery);
$userCount = $userResult->fetch_assoc()['total'] ?? 0;
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/features.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>
