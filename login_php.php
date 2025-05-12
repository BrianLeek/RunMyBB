<?php

include 'config.php';
session_start();

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SELECT query
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;

            // Update last seen after login
            $updateStmt = $conn->prepare("UPDATE users SET last_seen_at = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $id);
            $updateStmt->execute();
            $updateStmt->close();

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error_message'] = "The password you entered is invalid. Please try again.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "The username you entered is invalid. Please try again.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}

?>