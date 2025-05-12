<?php
include 'config.php';

$page_title = "User Profile - RunMyBB";
$page_desc = "";
$page_keyworda = "";

$username = $_GET['username'] ?? null;
if (!$username) {
    die("No username specified.");
}

// Fetch all user data
$stmt = $conn->prepare("SELECT id, username, email, bio, avatar, public_forum, registered_at, last_seen_at, hide_email, hide_last_seen, hide_registered, hide_forums FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$userData = $result->fetch_assoc();

$user_id          = $userData['id'];
$userUsername     = $userData['username'];
$userEmail        = $userData['email'] ?? '';
$userBio          = $userData['bio'] ?? '';
$userAvatarPath   = $userData['avatar'] ?? 'assets/img/default-avatar.png';
$userPublicForum  = $userData['public_forum'] ?? '#';
$userRegistered   = $userData['registered_at'] ?? 'Unknown';
$userLastSeen     = $userData['last_seen_at'] ?? 'Unknown';
$hide_email       = $userData['hide_email'] ?? 0;
$hide_last_seen   = $userData['hide_last_seen'] ?? 0;
$hide_registered  = $userData['hide_registered'] ?? 0;
$hide_forums      = $userData['hide_forums'] ?? 0;

// Fetch user's forums and member counts
$stmt = $conn->prepare("SELECT name, subdomain, description, database_name FROM forums WHERE user_id=? AND hidden=0 ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$forums = $stmt->get_result();

$userForums = [];

while ($forum = $forums->fetch_assoc()) {
    $memberCount = "N/A";

    // Connect to forum DB
    $forum_db = new mysqli($host, $db_user, $db_pass, $forum['database_name']);
    if (!$forum_db->connect_error) {
        $res = $forum_db->query("SELECT COUNT(*) AS total FROM mybb_users");
        if ($res && $row = $res->fetch_assoc()) {
            $memberCount = $row['total'];
        }
        $forum_db->close();
    }

    $userForums[] = [
        'name' => $forum['name'],
		'description' => $forum['description'],
        'subdomain' => $forum['subdomain'],
        'members' => $memberCount
    ];
}

$stmt->close();
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/profile.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>