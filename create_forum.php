<?php
include 'config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($maintenance_mode && $_SERVER['REMOTE_ADDR'] !== $allowed_ip) {
    $page_title = "Maintenance";

    include "themes/{$active_theme}/header.php";
    include "themes/{$active_theme}/maintenance.php";
    include "themes/{$active_theme}/footer.php";
    exit();
}

#if (!isset($_SESSION['user_id'])) {
#    header("Location: login.php");
#    exit();
#}

global $website_domain;

// Set security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net");

function mybbUsernameExists($forum_db, $username) {
    $stmt = $forum_db->prepare("SELECT uid FROM mybb_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// Function to create a directory safely
function createDirectory($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
}

function recursiveCopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while(false !== ($file = readdir($dir))) {
        if ($file !== '.' && $file !== '..') {
            $srcPath = "$src/$file";
            $dstPath = "$dst/$file";
            if (is_dir($srcPath)) {
                recursiveCopy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }
    closedir($dir);
}


// Function to delete a file safely
function safeDelete($file)
{
    if (file_exists($file) && is_writable($file)) {
        unlink($file);
        return true;
    }
    return false;
}

// Function to update MyBB settings
function updateMyBBSetting($forum_db, $key, $value)
{
    $stmt = $forum_db->prepare("UPDATE mybb_settings SET value = ? WHERE name = ?");
    if (!$stmt) {
        die("❌ ERROR: Prepare statement failed - " . $forum_db->error);
    }
    $stmt->bind_param("ss", $value, $key);
    $stmt->execute();
}

function createMyBBAdminUser($forum_db, $username, $email, $password, $ip_address = '127.0.0.1') {
    // Generate salt and login key
    $salt = bin2hex(random_bytes(4)); // 8-char salt
    $loginkey = bin2hex(random_bytes(25)); // 50-char key

    // MyBB-style password hashing (confirmed from your code)
    $password_hash = md5(md5($salt) . md5($password));

    // Set user details
    $reg_date = time();
    $usergroup = 4; // Admin group
    $user_title = "Administrator";
    $timezone = "UTC";
    $language = "";

    $stmt = $forum_db->prepare("INSERT INTO mybb_users 
        (username, email, password, salt, loginkey, usergroup, additionalgroups, usertitle, regdate, lastactive, lastvisit, lastip, timezone, language)
        VALUES (?, ?, ?, ?, ?, ?, '', ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssiisiiiss", $username, $email, $password_hash, $salt, $loginkey, $usergroup, $user_title, $reg_date, $reg_date, $reg_date, $ip_address, $timezone, $language);

    if (!$stmt->execute()) {
        die("❌ ERROR: Failed to insert MyBB admin user: " . $stmt->error);
    }

    $uid = $forum_db->insert_id;

    // Grant admin panel access
    $stmt = $forum_db->prepare("INSERT INTO mybb_adminoptions (uid, permissions, cpstyle, notes) VALUES (?, '', '', '')");
    $stmt->bind_param("i", $uid);
    $stmt->execute();

    return $uid;
}

function deleteDefaultMyBBAdmin($forum_db) {
    $default_uid = 1;

    // Delete from main users table
    $stmt = $forum_db->prepare("DELETE FROM mybb_users WHERE uid = ?");
    $stmt->bind_param("i", $default_uid);
    $stmt->execute();

    // Delete from admin options
    $stmt = $forum_db->prepare("DELETE FROM mybb_adminoptions WHERE uid = ?");
    $stmt->bind_param("i", $default_uid);
    $stmt->execute();

    // Delete from safe tables that use uid
    $safe_tables = [
        "mybb_sessions",
        "mybb_privatemessages",
        "mybb_posts",
        "mybb_threads"
    ];

    foreach ($safe_tables as $table) {
        if ($forum_db->query("SHOW COLUMNS FROM $table LIKE 'uid'")->num_rows > 0) {
            $forum_db->query("DELETE FROM $table WHERE uid = $default_uid");
        }
    }

    echo "✅ Default MyBB admin (UID 1) deleted successfully.\n";
}


// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("❌ ERROR: Invalid CSRF token.");
}
    ob_start();

    // Get user inputs and sanitize them
    $username = trim($_POST['username']);
    $forum_name = strtolower(trim($_POST['forum_name']));
	$forum_description = trim($_POST['forum_description'] ?? '');
    $forum_name = preg_replace("/[^a-zA-Z0-9-_]/", "", str_replace(" ", "-", $forum_name));

    $forum_domain = trim($_POST['forum_domain']);
	$forum_domain = preg_replace("/[^a-zA-Z0-9-_]/", "", str_replace(" ", "-", $forum_domain));
    if (empty($forum_domain)) {
        $forum_domain = $forum_name;
    }

    $email = trim($_POST['email']);
    $admin_password = $_POST['admin_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate username
    if (empty($username) || !preg_match("/^[a-zA-Z0-9-_]{3,20}$/", $username)) {
		$_SESSION['form_error'] = "Invalid username. Use 3-20 alphanumeric characters.";
		header("Location: index.php#create_forum");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$_SESSION['form_error'] = "Please enter a valid email address and try again.";
		header("Location: index.php#create_forum");
    }

    // Validate password confirmation
    if ($admin_password !== $confirm_password) {
		$_SESSION['form_error'] = "The password you entered do not match.";
		header("Location: index.php#create_forum");	
    }

    // Hash the password
    $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

    global $conn, $db_user, $db_pass, $host;

    // **Check if the user already exists**
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    if (!$stmt) {
        die("❌ ERROR: Prepare statement failed - " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // User exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($existing_user_id, $existing_password_hash);
        $stmt->fetch();

        // **Check if the password matches**
        if (!password_verify($admin_password, $existing_password_hash)) {
			$_SESSION['form_error'] = "The username you entered is taken. If you are trying to create another forum under your account be sure the password is correct.";
			header("Location: index.php#create_forum");
        } else {
            // **Password is correct → Link forum to existing user**
            $user_id = $existing_user_id;
        }
    } else {
        // **User doesn't exist → Create a new user**
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, registered_at) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            die("❌ ERROR: Prepare statement failed - " . $conn->error);
        }
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if (!$stmt->execute()) {
            die("❌ ERROR: Failed to create user: " . $stmt->error);
        }

        $user_id = $conn->insert_id;
    }
    $stmt->close();

    // **Now Proceed with Forum Creation**
    #$base_path = "C:/wamp64/www/createmybb";
    $forum_dir = "$base_path/forum/" . preg_replace("/[^a-zA-Z0-9-_]/", "", str_replace(" ", "-", $forum_domain));
    
    $forum_url = rtrim($website_domain, '/') . "/forum/" . ($forum_domain ?: $forum_name);
    $db_name = "mybb_" . preg_replace("/[^a-zA-Z0-9_]/", "_", $forum_domain);

	if (file_exists($forum_dir)) {
		$_SESSION['form_error'] = "The name and/or URL you entered for your forum is taken.";
		header("Location: index.php#create_forum");
	}

	$res = $conn->query("SHOW DATABASES LIKE '$db_name'");
	if ($res && $res->num_rows > 0) {
		$_SESSION['form_error'] = "Forum database already exists.";
		header("Location: index.php#create_forum");
	}

    createDirectory($forum_dir);
    $mybb_source_path = __DIR__ . "/mybb_source";
    recursiveCopy($mybb_source_path, $forum_dir);

    // Create forum database
    $sql_create_db = "CREATE DATABASE `$db_name`";
    if ($conn->query($sql_create_db) !== TRUE) {
        die("❌ ERROR: " . $conn->error);
    }

    // Connect to the newly created database
    $forum_db = new mysqli($host, $db_user, $db_pass, $db_name);
    if ($forum_db->connect_error) {
        die("❌ ERROR: Failed to connect to forum database: " . $forum_db->connect_error);
    }

    $sql_commands = file_get_contents("$base_path/mybb_source/mybb_clean.sql");
    if (!$sql_commands) {
        die("❌ ERROR: Failed to read SQL file.");
    }

    // Execute SQL queries
    $queries = explode(";\n", $sql_commands);
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if (!$forum_db->query($query)) {
                die("❌ ERROR: SQL import failed: " . $forum_db->error . "<br>Query: " . htmlspecialchars($query));
            }
        }
    }

	$forum_description = trim($_POST['forum_description'] ?? '');
    // Insert forum details into `createforum` database and assign to user
	$stmt = $conn->prepare("INSERT INTO forums (name, subdomain, database_name, user_id, description) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sssis", $forum_name, $forum_domain, $db_name, $user_id, $forum_description);

    if (!$stmt) {
        die("❌ ERROR: Prepare statement failed - " . $conn->error);
    }
    #$stmt->bind_param("sssi", $forum_name, $forum_domain, $db_name, $user_id);

    if (!$stmt->execute()) {
        die("❌ ERROR: " . $stmt->error);
    }

    // Restore MyBB settings
    updateMyBBSetting($forum_db, 'bburl', $forum_url);
    updateMyBBSetting($forum_db, 'homeurl', $forum_url);
    updateMyBBSetting($forum_db, 'adminemail', $email);
    
    // Parse the URL to get the path component for the cookiepath
    $parsed_url = parse_url($forum_url);
    $cookie_path = isset($parsed_url['path']) ? $parsed_url['path'] : "/";

    updateMyBBSetting($forum_db, 'cookiepath', $cookie_path);
    updateMyBBSetting($forum_db, 'cookiedomain', "");
    updateMyBBSetting($forum_db, 'bbname', $forum_name);
    updateMyBBSetting($forum_db, 'homename', $forum_name);

	updateMyBBSetting($forum_db, 'homename', $forum_name);

	// Delete default MyBB admin BEFORE making the real one
	deleteDefaultMyBBAdmin($forum_db);

	// Auto-renaming still acts as a backup (just in case)
	$base_username = $username;
	$suffix = 1;

	while (mybbUsernameExists($forum_db, $username)) {
		$username = $base_username . "_" . $suffix;
		$suffix++;
	}

	createMyBBAdminUser($forum_db, $username, $email, $admin_password);

    $config_file = "$forum_dir/inc/config.php";
    if (file_exists($config_file)) {
        $config_content = file_get_contents($config_file);
        $config_content = preg_replace(
            "/\\\$config\['database'\]\['database'\] = '.*?';/",
            "\$config['database']['database'] = '$db_name';",
            $config_content
        );
        file_put_contents($config_file, $config_content);
    }

    safeDelete("$forum_dir/inc/settings.php");

    // Redirect to forum
    header("Location: $forum_url");
    exit();
}
?>
