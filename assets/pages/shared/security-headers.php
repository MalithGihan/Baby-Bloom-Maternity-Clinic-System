<?php
// Security headers for BabyBloom application
// Include this file at the top of every PHP page

// Content Security Policy - Limited unsafe-eval for third-party QR scanner
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-eval'; style-src 'self'; img-src 'self' data: https://api.qrserver.com; font-src 'self'; connect-src 'self'; frame-ancestors 'none'; object-src 'none'; base-uri 'self';");

// Additional security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Prevent caching of sensitive pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>