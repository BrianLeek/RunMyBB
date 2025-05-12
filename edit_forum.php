<?php
include 'config.php';
session_start();

$page_title = $lang['edit_title'] . " - " . $lang['site_title'];
$page_desc = $lang['edit_desc'];
$page_keywords = $lang['edit_keywords'];

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$forum_id = $_GET['id'] ?? null;

if (!$forum_id) {
    die("Forum ID not provided.");
}

// Fetch original forum details
$stmt = $conn->prepare("SELECT name, description, subdomain FROM forums WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $forum_id, $user_id);
$stmt->execute();
$stmt->bind_result($original_name, $original_description, $original_subdomain);
$stmt->fetch();
$stmt->close();

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = trim($_POST['forum_name'] ?? '');
    $new_description = trim($_POST['forum_description'] ?? '');
    $new_subdomain = preg_replace("/[^a-zA-Z0-9-_]/", "", strtolower(trim($_POST['forum_subdomain'] ?? '')));

    // Check if subdomain is already taken (by a different forum)
    if ($new_subdomain !== $original_subdomain) {
        $stmt = $conn->prepare("SELECT id FROM forums WHERE subdomain = ? AND id != ?");
        $stmt->bind_param("si", $new_subdomain, $forum_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "❌ That forum URL is already taken. Please choose a different one.";
        }

        $stmt->close();
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE forums SET name = ?, description = ?, subdomain = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $new_name, $new_description, $new_subdomain, $forum_id, $user_id);

        if ($stmt->execute()) {
            $success = true;

            // Rename directory if subdomain changed
            if ($new_subdomain !== $original_subdomain) {
                $old_dir = __DIR__ . "/forum/" . $original_subdomain;
                $new_dir = __DIR__ . "/forum/" . $new_subdomain;

                if (is_dir($old_dir)) {
                    rename($old_dir, $new_dir);
                }
            }

            header("Location: dashboard.php?updated=1");
            exit();
        } else {
            $error = "❌ Failed to update forum details.";
        }

        $stmt->close();
    }
}
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/edit_forum.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>
