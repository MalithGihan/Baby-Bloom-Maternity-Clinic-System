<?php
/**
 * Secure Session Start
 * Use this instead of session_start() on login/public pages
 */

// Include session security utilities
require_once __DIR__ . '/session-security.php';

// Configure secure session settings before starting session
configureSecureSession();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>