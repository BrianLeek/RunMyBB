<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['forum_id'])) {
    header("Location: login.php");
    exit();
}

$forum_id = intval($_POST['forum_id']);
$user_id = $_SESSION['user_id'];

// Check current status
$stmt = $conn->prepare("SELECT hidden FROM forums WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $forum_id, $user_id);
$stmt->execute();
$stmt->bind_result($hidden);
$stmt->fetch();
$stmt->close();

$new_status = $hidden ? 0 : 1;

// Update hidden status
$stmt = $conn->prepare("UPDATE forums SET hidden=? WHERE id=? AND user_id=?");
$stmt->bind_param("iii", $new_status, $forum_id, $user_id);
$stmt->execute();

header("Location: dashboard.php?updated=true");
exit();
