<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Get privacy options
$hide_email = isset($_POST['hide_email']) ? 1 : 0;
$hide_last_seen = isset($_POST['hide_last_seen']) ? 1 : 0;
$hide_registered = isset($_POST['hide_registered']) ? 1 : 0;
$hide_forums = isset($_POST['hide_forums']) ? 1 : 0;

// Update privacy settings first
$stmt = $conn->prepare("UPDATE users SET hide_email=?, hide_last_seen=?, hide_registered=?, hide_forums=? WHERE id=?");
$stmt->bind_param("iiiii", $hide_email, $hide_last_seen, $hide_registered, $hide_forums, $user_id);
$stmt->execute();
$stmt->close();

// Sanitize and validate the input data
$username = trim($_POST['profile_username']);
$email = trim($_POST['profile_email']);
$password = trim($_POST['profile_password']);
#$display_name = trim($_POST['profile_display_name']);
$public_forum = isset($_POST['profile_public_forum']) ? trim($_POST['profile_public_forum']) : '';
$bio = trim($_POST['profile_bio']);
$avatar = $_FILES['profile_avatar'];

// Start the SQL update query
$update_query = "UPDATE users SET username = ?, email = ?, public_forum = ?, bio = ?";

// Parameters for the prepared statement
$params = [$username, $email, $public_forum, $bio];
$types = "ssss";

// Handle avatar upload
if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/avatars/";
    $target_file = $target_dir . basename($avatar["name"]);
    
    // Check if the file is a valid image
    $image_info = getimagesize($avatar["tmp_name"]);
    if ($image_info !== false) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($avatar["tmp_name"], $target_file)) {
            // Set the avatar column in the query
            $update_query .= ", avatar = ?";
            $params[] = $target_file;
            $types .= "s";
        } else {
            echo "Failed to upload avatar.";
        }
    } else {
        echo "Invalid image file.";
    }
}

// If a password is provided, hash it and add it to the query
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $update_query .= ", password = ?";
    $params[] = $hashed_password;
    $types .= "s";
}

$update_query .= " WHERE id = ?";

// Add the user ID as the last parameter
$params[] = $user_id;
$types .= "i";

// Prepare the SQL statement
$stmt = $conn->prepare($update_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind the parameters dynamically
$stmt->bind_param($types, ...$params);

// Execute the statement
if ($stmt->execute()) {
    echo "Profile updated successfully.";
    // Redirect back to the profile page or display a success message
    header("Location: profile_settings.php");
    exit();
} else {
    echo "Error updating profile: " . $stmt->error;
}
