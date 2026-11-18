<?php
// Local development only: `php -S localhost:8000 dev-router.php`
// Mirrors the .htaccess rules so pretty URLs work without Apache.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri === '/ads.txt') {
    require __DIR__ . '/ads.php';
    return true;
}
if (strpos($uri, '/inc/') === 0) {
    http_response_code(403);
    return true;
}
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false; // serve the file as-is
}
require __DIR__ . '/default.php';
