<?php
// src/views/base.php - basic layout loader
declare(strict_types=1);

// load header from same folder
require_once __DIR__ . '/header.php';

$contentFile = __DIR__ . '/pages/home.php';
if (isset($viewFile) && file_exists($viewFile)) {
    $contentFile = $viewFile;
}
include $contentFile;

// load footer from same folder
require_once __DIR__ . '/footer.php';
