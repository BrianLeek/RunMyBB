<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['forum_id'])) {
    die("Unauthorized access");
}

$forum_id = (int) $_POST['forum_id'];
$user_id = $_SESSION['user_id'];

// Get current status
$stmt = $conn->prepare("SELECT status FROM forums WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $forum_id, $user_id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();

$new_status = ($status === 'suspended') ? 'active' : 'suspended';

$stmt = $conn->prepare("UPDATE forums SET status = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $new_status, $forum_id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php");
exit;
?>
