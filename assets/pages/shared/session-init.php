<?php
/**
 * Session Initialization
 * Include this file at the top of every protected page
 * DO NOT include on login/registration pages
 */

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

// Only validate session security if user is actually logged in
$isLoggedIn = isset($_SESSION["mamaEmail"]) || isset($_SESSION["staffEmail"]);

if ($isLoggedIn) {
    // Initialize session security metadata if not present
    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = time();
    }
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
    if (!isset($_SESSION['user_ip'])) {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    // Auto-determine user type and validate session
    $userType = 'mama'; // default
    if (isset($_SESSION["staffEmail"])) {
        $userType = 'staff';
    } elseif (isset($_SESSION["mamaEmail"])) {
        $userType = 'mama';
    }

    // Validate session security
    validateSessionSecurity($userType);
}
?>