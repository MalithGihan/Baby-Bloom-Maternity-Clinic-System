<?php
// Hardened session start (no output before this)
require_once __DIR__ . '/../shared/secure-session-start.php';

// Optionally load helper from incoming branch
$sessionSecurityPath = __DIR__ . '/../shared/session-security.php';
if (is_file($sessionSecurityPath)) {
    require_once $sessionSecurityPath;
}

// --- Enforce POST-only ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff-login.php');
    exit();
}

// --- CSRF check ---
$postedToken  = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';

if (!$postedToken || !$sessionToken || !hash_equals($sessionToken, $postedToken)) {
    // (Optional) add a flash message here if you use one
    header('Location: staff-login.php');
    exit();
}

// Single-use token
unset($_SESSION['csrf_token']);

// --- Perform logout ---
// Prefer helper if available; otherwise, manual fallback
if (function_exists('secureLogout')) {
    // Expected to destroy session and expire cookie
    secureLogout('staff');
} else {
    // Manual logout
    session_unset();
    session_destroy();

    // Expire the session cookie on the client
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
}

// Final redirect (in case helper didn't redirect)
header('Location: staff-login.php');
exit();
