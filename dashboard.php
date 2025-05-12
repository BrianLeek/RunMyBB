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

$page_title = $lang['dashboard_title'] . " - " . $lang['site_title'];
$page_desc = $lang['dashboard_desc'];
$page_keywords = $lang['dashboard_keywords'];

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

// Fetch user's forums
$stmt = $conn->prepare("SELECT id, name, subdomain, status, hidden FROM forums WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Include theme parts (they now have access to $result, $username, etc.)
include "themes/{$active_theme}/header.php";
include "themes/{$active_theme}/dashboard_content.php";
include "themes/{$active_theme}/footer.php";