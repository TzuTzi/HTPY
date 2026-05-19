<?php

require_once __DIR__ . '/php/auth.php';
require_login();

$file = $_GET['file'] ?? '';

// SECURE — basename() strips any ../ traversal
$file = basename($file);
$path = __DIR__ . '/uploads/' . $file;

/* ── VULNERABLE version (remove basename() above and uncomment to demo path traversal) ──
$path = __DIR__ . '/uploads/' . $file;
── END VULNERABLE ── */

if (!file_exists($path) || !is_file($path)) {
    http_response_code(404);
    exit('File not found.');
}

// Serve the file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($path) . '"');
readfile($path);
exit;
