<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$forum_id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE forums SET status='active' WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $forum_id, $_SESSION['user_id']);
$stmt->execute();

header("Location: dashboard.php?unsuspended=true");
exit();
