<?php
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook("admin_config_menu", "themeuploader_admin_nav");
$plugins->add_hook("admin_config_action_handler", "themeuploader_action_handler");

function themeuploader_info()
{
    return [
        "name" => "Theme Uploader",
        "description" => "Allows uploading and extracting theme image folders into the /images directory.",
        "website" => "https://t.me/MostafaShiraali",
        "author" => "Mostafa Shiraali (Modified by Brian)",
        "authorsite" => "https://t.me/MostafaShiraali",
        "version" => "1.0",
        "guid" => "themeuploader_guid",
        "compatibility" => "*"
    ];
}

function themeuploader_activate() {}
function themeuploader_deactivate() {}

function themeuploader_admin_nav(&$sub_menu)
{
    end($sub_menu);
    $key = (key($sub_menu)) + 10;
    if (!$key) {
        $key = 60;
    }

    $sub_menu[$key] = [
        'id' => 'themeuploader',
        'title' => "Theme Uploader",
        'link' => "index.php?module=config-themeuploader"
    ];
}

function themeuploader_action_handler(&$action)
{
    $action['themeuploader'] = ['active' => 'themeuploader', 'file' => 'themeuploader.php'];
}

// Admin Page
if (defined('IN_ADMINCP') && IN_ADMINCP && $mybb->input['module'] == "config-themeuploader") {
    $page->add_breadcrumb_item("Theme Uploader", "index.php?module=config-themeuploader");

    $page->output_header("Theme Uploader");

    if ($mybb->request_method == "post" && isset($_FILES['themezip'])) {
        $upload = $_FILES['themezip'];
        $targetDir = MYBB_ROOT . "images/";

        if ($upload['type'] == "application/zip" || pathinfo($upload['name'], PATHINFO_EXTENSION) == "zip") {
            $zip = new ZipArchive;
            $tmp = $upload['tmp_name'];
            $themeName = pathinfo($upload['name'], PATHINFO_FILENAME);
            $dest = $targetDir . $themeName;

            if (!is_dir($dest)) {
                mkdir($dest, 0755, true);
            }

            if ($zip->open($tmp) === TRUE) {
                $zip->extractTo($dest);
                $zip->close();
                flash_message("Theme images uploaded to 'images/{$themeName}/'", "success");
            } else {
                flash_message("Failed to extract the zip file.", "error");
            }
        } else {
            flash_message("Invalid file type. Only ZIPs are allowed.", "error");
        }

        admin_redirect("index.php?module=config-themeuploader");
    }

    echo '<form action="" method="post" enctype="multipart/form-data">';
    echo '<fieldset><legend>Upload Theme ZIP</legend>';
    echo '<label>Choose a theme .zip file (will be extracted to /images/):</label><br>';
    echo '<input type="file" name="themezip" /><br><br>';
    echo '<input type="submit" value="Upload Theme" class="button" />';
    echo '</fieldset>';
    echo '</form>';

    $page->output_footer();
}
