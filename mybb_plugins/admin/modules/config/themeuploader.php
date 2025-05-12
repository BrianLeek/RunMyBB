<?php
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

$page->add_breadcrumb_item("Theme Uploader", "index.php?module=config-themeuploader");

if (!$mybb->input['action']) {
    $page->output_header("Theme Uploader");

    $form = new Form("index.php?module=config-themeuploader&action=installtheme", "post", "", true);
    $form_container = new FormContainer("Upload Theme ZIP");

    $form_container->output_row(
        "Select Theme ZIP File",
        "ZIP must contain folders like images, jscripts, inc, etc. They can be inside any parent folder (e.g., upload/).",
        $form->generate_file_upload_box('theme_file', ['id' => 'theme_file']),
        'theme_file'
    );

    $form_container->end();
    $buttons[] = $form->generate_submit_button("Install Theme");
    $form->output_submit_wrapper($buttons);
    $form->end();

    $page->output_footer();
}

// Creates any missing folder (and optional index.html)
function create_folder_with_index($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
        file_put_contents($path . '/index.html', "<html><body></body></html>");
    }
}

if ($mybb->input['action'] == "installtheme") {
    if (!verify_post_check($mybb->get_input('my_post_key'))) {
        flash_message("Invalid request (missing post key).", 'error');
        admin_redirect("index.php?module=config-themeuploader");
    }

    if (!class_exists('ZipArchive')) {
        flash_message("ZipArchive PHP extension is required.", 'error');
        admin_redirect("index.php?module=config-themeuploader");
    }

    $tmp_name = $_FILES["theme_file"]["tmp_name"];
    $filename = basename($_FILES["theme_file"]["name"]);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $theme_name = pathinfo($filename, PATHINFO_FILENAME);

    if ($ext !== "zip") {
        flash_message("Only ZIP files are allowed.", 'error');
        admin_redirect("index.php?module=config-themeuploader");
    }

    $zip = new ZipArchive();
    if (!$zip->open($tmp_name)) {
        flash_message("Failed to open ZIP file.", 'error');
        admin_redirect("index.php?module=config-themeuploader");
    }

    $destination_root = MYBB_ROOT;
    $extracted_anything = false;

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $entry = $zip->getNameIndex($i);
        $entry = str_replace('\\', '/', $entry); // Normalize paths

        // Skip system or junk files
        if (str_contains($entry, '__MACOSX') || substr($entry, -1) == '/') {
            continue;
        }

        // We want to find: */images/, */jscripts/, */inc/, */uploads/
        if (preg_match('#(?:^|/)(images|jscripts|inc|uploads)(/.*?)$#', $entry, $matches)) {
            $folder = $matches[1];
            $subpath = $matches[2];

if ($folder == "images") {
    // Get the path after 'images/', e.g., FancyTheme/logo.png
    $after_images = ltrim($subpath, '/');
    $parts = explode('/', $after_images);

    // Skip files directly inside 'images/' (we only want images/FancyTheme/...)
    if (count($parts) < 2) {
        continue;
    }

    $theme_folder = $parts[0]; // e.g., FancyTheme
    $relative_path = implode('/', array_slice($parts, 1)); // e.g., logo.png

    // Final target path: images/FancyTheme/logo.png
    $target_path = $destination_root . "images/" . $theme_folder . "/" . $relative_path;

    // Skip if for some reason path is still empty
    if (empty($target_path)) {
        continue;
    }

    create_folder_with_index(dirname($target_path));
    file_put_contents($target_path, $zip->getFromIndex($i));
    $extracted_anything = true;
}


 if (!empty($target_path)) {
    create_folder_with_index(dirname($target_path));
    file_put_contents($target_path, $zip->getFromIndex($i));
    $extracted_anything = true;
}
        }
    }

    $zip->close();

    if ($extracted_anything) {
        flash_message("Theme contents extracted successfully!", "success");
    } else {
        flash_message("No valid theme folders found in ZIP. (Expected folders: images, jscripts, inc, uploads)", "error");
    }

    admin_redirect("index.php?module=config-themeuploader");
}
