
<?php
include 'config.php';
session_start();

$page_title = $lang['site_tagline'] . " - " . $lang['site_title'];
$page_desc = $lang['homepage_desc'];
$page_keywords = $lang['homepage_keywords'];

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$error_message = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);

// Count forums
$forumCountQuery = "SELECT COUNT(*) AS total FROM forums";
$forumResult = $conn->query($forumCountQuery);
$forumCount = $forumResult->fetch_assoc()['total'] ?? 0;

// Count users
$userCountQuery = "SELECT COUNT(*) AS total FROM users";
$userResult = $conn->query($userCountQuery);
$userCount = $userResult->fetch_assoc()['total'] ?? 0;

// Fetch recent forums (Limit 5)
$recentForumsQuery = "SELECT name, subdomain, description, created_at FROM forums ORDER BY created_at DESC LIMIT 8";
$recentForumsResult = $conn->query($recentForumsQuery);
?>
 
<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/home.php"; ?>
<?php include "/themes/{$active_theme}/footer.php"; ?>

