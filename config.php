<?php

$maintenance_mode = false; // Toggle this ON/OFF
$allowed_ip = ''; // Replace with your IP address

function is_under_maintenance() {
    global $maintenance_mode, $allowed_ip;
    return $maintenance_mode && ($_SERVER['REMOTE_ADDR'] !== $allowed_ip);
}

// Database Info
$host = "localhost"; // Update with your MySQL hostname
$db_user = ""; // Update with your MySQL username
$db_pass = ""; // Update with your MySQL password
$base_db = ""; // Master database for managing forum records

// Website Configs
$active_theme = "default"; // Default theme to be used on website
$language = 'english'; // Default language to be used on website
$website_domain = "https://runmybb.com"; // Domain where RunMyBB is installed
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/'; // Existing base path (specific to CreateMyBB directory)
$lang = include __DIR__ . "/languages/{$language}/main.lang.php"; // Load the language file

// Default user profile values when no info is provied.
$defaultUser = [
    'avatar'       => '/default-avatar.jpg', // Users default avatar that is show on RunMyBB user profiles.
    'display_name' => 'Unknown Display Name', // This shouldn't matter to much since this shouldn't be possible.
    'bio'          => '',
    'username'     => 'Unknown Username', // This shouldn't matter to much since this shouldn't be possible.
    'public_forum' => 'http://example.com/',
    'email'        => 'not@available.com',
    'last_seen'    => 'Never', // This shouldn't matter to much since this shouldn't be possible.
    'registered'   => 'Unknown' // This shouldn't matter to much since this shouldn't be possible.
];


// Do not touch
$conn = new mysqli($host, $db_user, $db_pass, $base_db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
