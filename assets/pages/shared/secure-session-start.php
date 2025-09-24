<?php
/**
 * Secure Session Start
 * Use this instead of session_start() on login/public pages
 */

date_default_timezone_set('Asia/Colombo');

// Include security headers
require_once __DIR__ . '/security-headers.php';

// Include centralized logger
require_once __DIR__ . '/logger.php';

// Include session security utilities
require_once __DIR__ . '/session-security.php';

// Configure secure session settings before starting session
configureSecureSession();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>