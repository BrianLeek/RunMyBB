<?php
include 'config.php';
session_start();

$page_title = $lang['privacy_title'] . " - " . $lang['site_title'];
$page_desc = $lang['privacy_desc'];
$page_keywords = $lang['privacy_keywords'];
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/privacy.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>
