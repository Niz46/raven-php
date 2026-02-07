<?php
declare(strict_types=1);
// Basic config
$projectRoot = realpath(__DIR__ . '/..');
define('BASE_PATH', $projectRoot);
define('PUBLIC_PATH', $projectRoot . '/public');
// Environment flags
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'development');
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN));
}
ini_set('display_errors', APP_DEBUG ? '1' : '0');
error_reporting(E_ALL);
