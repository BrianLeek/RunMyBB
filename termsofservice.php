<?php
include 'config.php';
session_start();

$page_title = $lang['tos_title'] . " - " . $lang['site_title'];
$page_desc = $lang['tos_desc'];
$page_keywords = $lang['tos_keywords'];
?>

<?php include "themes/{$active_theme}/header.php"; ?>
<?php include "themes/{$active_theme}/tos.php"; ?>
<?php include "themes/{$active_theme}/footer.php"; ?>
