<?php
include 'config.php';
session_start();

$page_title = "Contact Us - RunMyBB";
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $issue_type = trim($_POST['issue_type'] ?? '');
    $message = trim($_POST['message'] ?? '');
	$forum_url = trim($_POST['forum_url'] ?? '');
	$email = trim($_POST['email'] ?? '');

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error_message = "Please enter a valid email address.";
	}

	if ($issue_type === 'Report a Forum' && empty($forum_url)) {
		$error_message = "Forum URL is required when reporting a forum.";
	}

    if (empty($username) || empty($name) || empty($issue_type) || empty($message)) {
        $error_message = "All fields are required.";
    } else {
        // Email settings
        $to = 'contact@runmybb.com'; // <-- Set your admin/support email here
        $subject = "$issue_type - Contact Form Submission";
		$body = "Username: $username\n";
		$body .= "Email: $email\n";
		$body .= "Name: $name\n";
		$body .= "Issue Type: $issue_type\n";
		$body .= "Forum URL: " . (!empty($forum_url) ? $forum_url : 'N/A') . "\n";
		$body .= "\nMessage:\n$message\n";

        $headers = "From: noreply@runmybb.com\r\n";
        $headers .= "Reply-To: noreply@runmybb.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $body, $headers)) {
            $success_message = "Thank you for contacting us! We'll get back to you soon.";
        } else {
            $error_message = "Oops! Something went wrong while sending your message.";
        }
    }
}
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/contact.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>
