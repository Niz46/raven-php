<?php
declare(strict_types=1);
function asset(string $path): string {
    $path = ltrim($path, '/');
    return '/assets/' . $path;
}
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
