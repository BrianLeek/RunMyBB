<?php
include 'C:/wamp64/www/createmybb/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forum_name'])) {
    $forum_name = $_POST['forum_name'];

    // Set up paths
    $forum_dir = "C:/wamp64/www/createmybb/forum/$forum_name";
    $backup_dir = "C:/wamp64/www/createmybb/exports/$forum_name";
    $zip_file = "$backup_dir.zip";

    // Create export folder if not exists
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }

    // Copy forum files to export directory
    function copy_folder($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') continue;
            $src_file = $src . '/' . $file;
            $dst_file = $dst . '/' . $file;
            if (is_dir($src_file)) {
                copy_folder($src_file, $dst_file);
            } else {
                copy($src_file, $dst_file);
            }
        }
        closedir($dir);
    }

    copy_folder($forum_dir, $backup_dir);

    // Export the forum database
    $db_name = "mybb_$forum_name";
    $db_backup_file = "$backup_dir/$forum_name-database.sql";

    $dump_command = "mysqldump --user={$db_user} --password={$db_pass} --host=localhost $db_name > $db_backup_file";
    system($dump_command, $output);

    // Create a ZIP file with the forum files and database dump
    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($backup_dir), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $file_path = $file->getRealPath();
                $relative_path = substr($file_path, strlen($backup_dir) + 1);
                $zip->addFile($file_path, $relative_path);
            }
        }
        $zip->close();
    }

    // Download the ZIP file
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $forum_name . '_backup.zip"');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);

    // Clean up
    exec("rm -rf $backup_dir");
    unlink($zip_file);
    exit();
} else {
    echo "Invalid request.";
}
?>
