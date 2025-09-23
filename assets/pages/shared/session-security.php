<?php
/**
 * Session Security Utility
 * Provides session timeout, hijacking protection, and security validation
 */

// Session timeout in seconds (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Maximum login duration in seconds (8 hours)
define('MAX_LOGIN_DURATION', 28800);

/**
 * Validate session security and handle timeouts
 *
 * @param string $userType - 'mama' or 'staff'
 * @return bool - true if session is valid, redirects if invalid
 */
function validateSessionSecurity($userType = 'mama') {
    $currentTime = time();

    // Check if user is logged in
    $isLoggedIn = false;
    $redirectPath = '';

    if ($userType === 'mama') {
        $isLoggedIn = isset($_SESSION["mamaEmail"]);
        $redirectPath = "../auth/mama-login.php";
    } else {
        $isLoggedIn = isset($_SESSION["staffEmail"]);
        $redirectPath = "../auth/staff-login.php";
    }

    if (!$isLoggedIn) {
        header("Location: $redirectPath");
        exit();
    }

    // Check session timeout (last activity)
    if (isset($_SESSION['last_activity'])) {
        if (($currentTime - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
            logSecurityEvent("Session timeout for user type: $userType");
            session_destroy();
            $_SESSION = array();
            header("Location: $redirectPath?timeout=1");
            exit();
        }
    }

    // Check maximum login duration
    if (isset($_SESSION['login_time'])) {
        if (($currentTime - $_SESSION['login_time']) > MAX_LOGIN_DURATION) {
            logSecurityEvent("Maximum login duration exceeded for user type: $userType");
            session_destroy();
            $_SESSION = array();
            header("Location: $redirectPath?expired=1");
            exit();
        }
    }

    // Basic session hijacking protection - IP address validation
    if (isset($_SESSION['user_ip'])) {
        $currentIP = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!empty($_SESSION['user_ip']) && $_SESSION['user_ip'] !== $currentIP) {
            logSecurityEvent("Potential session hijacking detected. Original IP: {$_SESSION['user_ip']}, Current IP: $currentIP");
            session_destroy();
            $_SESSION = array();
            header("Location: $redirectPath?security=1");
            exit();
        }
    }

    // Basic session hijacking protection - User Agent validation
    if (isset($_SESSION['user_agent'])) {
        $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (!empty($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $currentUserAgent) {
            logSecurityEvent("Potential session hijacking detected. User agent mismatch.");
            session_destroy();
            $_SESSION = array();
            header("Location: $redirectPath?security=1");
            exit();
        }
    }

    // Update last activity time
    $_SESSION['last_activity'] = $currentTime;

    // Regenerate session ID periodically for additional security
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = $currentTime;
    } else if (($currentTime - $_SESSION['last_regeneration']) > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = $currentTime;
    }

    return true;
}

/**
 * Secure session destruction
 */
function secureLogout($userType = 'mama') {
    $redirectPath = $userType === 'mama' ? "../auth/mama-login.php" : "../auth/staff-login.php";

    // Clear all session data
    $_SESSION = array();

    // Delete session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy session
    session_destroy();

    logSecurityEvent("User logged out successfully. Type: $userType");

    header("Location: $redirectPath?logout=1");
    exit();
}

/**
 * Log security events
 */
function logSecurityEvent($message) {
    $logMessage = date('Y-m-d H:i:s') . " | SECURITY | $message\n";
    error_log($logMessage, 3, __DIR__ . "/../../../logs/security_log.log");
}

/**
 * Configure secure session settings
 * Call this before session_start()
 */
function configureSecureSession() {
    // Prevent JavaScript access to session cookie
    ini_set('session.cookie_httponly', 1);

    // Only send session cookie over HTTPS in production
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }

    // Prevent session fixation
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);

    // Set session name to something less predictable
    session_name('BBSID');

    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
?>