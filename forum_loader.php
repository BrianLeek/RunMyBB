<?php

$host = $_SERVER['HTTP_HOST'];
$domain = 'runmybb.com'; // change to your root domain

if ($host === $domain || $host === "www.$domain") {
    // This is the main site, not a forum subdomain
    header("Location: index.php");
    exit();
}

// Extract subdomain from the host
$subdomain = str_replace(".$domain", "", $host);

$subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];
$forum_dir = __DIR__ . "/forum/{$subdomain}";

if (file_exists("{$forum_dir}/index.php")) {
    chdir($forum_dir);
    include "index.php";
    exit;
} else {
    http_response_code(404);
    echo "Forum not found for subdomain: " . htmlspecialchars($subdomain);
}

exit();


