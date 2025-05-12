<?php
include 'config.php';
session_start();

$page_title = $lang['about_title'] . " - " . $lang['site_title'];
$page_desc = $lang['about_desc'];
$page_keywords = $lang['about_keywords'];
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/about.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>
