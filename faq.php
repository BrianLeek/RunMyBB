<?php
include 'config.php';
session_start();

$page_title = $lang['faq_title'] . " - " . $lang['site_title'];
$page_desc = $lang['faq_desc'];
$page_keywords = $lang['faq_keywords'];

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
<?php include "themes/{$active_theme}/faq.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>