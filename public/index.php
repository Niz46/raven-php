<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';
// Basic front controller
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = preg_replace('#/+$#','',$uri);
if ($uri === '' || $uri === '/' || $uri === '/index.php') {
    // Show home
    include __DIR__ . '/../src/views/base.php';
    exit;
}
// Try to map to a page under src/views/pages
$page = ltrim($uri, '/');
$candidate = __DIR__ . '/../src/views/pages/' . $page . '.php';
if (file_exists($candidate)) {
    $viewFile = $candidate;
    include __DIR__ . '/../src/views/base.php';
    exit;
}
http_response_code(404);
echo '404 - Not Found';
