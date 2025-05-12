<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$forum_id = $_POST['forum_id'];
$forum_name = trim($_POST['forum_name']);
$forum_description = trim($_POST['forum_description']);
$forum_domain = preg_replace("/[^a-zA-Z0-9-_]/", "", strtolower(trim($_POST['forum_domain'])));

$stmt = $conn->prepare("UPDATE forums SET name = ?, description = ?, subdomain = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sssii", $forum_name, $forum_description, $forum_domain, $forum_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    header("Location: dashboard.php?updated=true");
} else {
    die("Error updating forum: " . $stmt->error);
}
