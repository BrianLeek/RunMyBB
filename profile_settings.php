<?php
// Include your configuration and database connection
include 'config.php';
session_start();

$page_title = "Profile Settings - RunMyBB";
$page_desc = "";
$page_keyworda = "";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$userId = $_SESSION['user_id'];

// Fetch current user data including privacy settings
$sql = "SELECT username, email, bio, public_forum, avatar, hide_email, hide_last_seen, hide_registered, hide_forums 
        FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$currentUsername       = $userData['username'] ?? '';
$currentEmail          = $userData['email'] ?? '';
$currentBio            = $userData['bio'] ?? '';
$currentPublicForumURL = $userData['public_forum'] ?? '';
$currentAvatar         = $userData['avatar'] ?? '';
$hide_email            = $userData['hide_email'] ?? 0;
$hide_last_seen        = $userData['hide_last_seen'] ?? 0;
$hide_registered       = $userData['hide_registered'] ?? 0;
$hide_forums           = $userData['hide_forums'] ?? 0;

$stmt->close();
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/profile_settings.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>