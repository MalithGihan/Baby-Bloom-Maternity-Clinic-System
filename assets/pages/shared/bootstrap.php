<?php
/**
 * BabyBloom Application Bootstrap
 * Include this file at the top of EVERY PHP page
 * Handles: Security headers, session management, common utilities
 */

// Prevent direct access
if (!defined('BABYBLOOM_APP')) {
    define('BABYBLOOM_APP', true);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Headers - Applied to ALL pages
// Note: unsafe-inline is needed for this legacy application's inline styles/scripts
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self'; frame-src 'none'; frame-ancestors 'none'; object-src 'none'; base-uri 'self'; form-action 'self'; manifest-src 'self'; media-src 'self'; worker-src 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Prevent caching of sensitive pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Load environment variables if not already loaded
if (!getenv('DB_HOST')) {
    $envPath = __DIR__ . '/../../../.env';
    if (is_readable($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            [$k, $v] = array_map('trim', explode('=', $line, 2) + ['', '']);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            $_ENV[$k] = $v;
            $_SERVER[$k] = $v;
            putenv("$k=$v");
        }
    }
}

// Common utility functions can go here
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function redirect_with_error($location, $error) {
    $_SESSION['error_message'] = $error;
    header("Location: $location");
    exit();
}

function redirect_with_success($location, $message) {
    $_SESSION['success_message'] = $message;
    header("Location: $location");
    exit();
}
?>