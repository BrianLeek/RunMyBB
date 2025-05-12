<?php
include 'config.php';
session_start();

if ($maintenance_mode && $_SERVER['REMOTE_ADDR'] !== $allowed_ip) {
    $page_title = "Maintenance";

    include "themes/" . $active_theme . "/header.php";
    include "themes/" . $active_theme . "/maintenance.php";
    include "themes/" . $active_theme . "/footer.php";
    exit();
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$page_title = $lang['login_title'] . " - " . $lang['site_title'];
$page_desc = $lang['login_desc'];
$page_keywords = $lang['login_keywords'];

// Get and clear the error message
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

// Fetch user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();

?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/login.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>