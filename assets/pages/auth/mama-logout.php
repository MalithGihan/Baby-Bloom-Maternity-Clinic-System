<?php
// Start hardened session (no output before this)
require_once __DIR__ . '/../shared/secure-session-start.php';

// Optionally load helper (incoming branch)
$sessionSecurityPath = __DIR__ . '/../shared/session-security.php';
if (is_file($sessionSecurityPath)) {
    require_once $sessionSecurityPath;
}

// --- Enforce POST-only ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mama-login.php');
    exit();
}

// --- CSRF check ---
$postedToken   = $_POST['csrf_token'] ?? '';
$sessionToken  = $_SESSION['csrf_token'] ?? '';

if (!$postedToken || !$sessionToken || !hash_equals($sessionToken, $postedToken)) {
    // (Optional) you could log this event on the server
    header('Location: mama-login.php');
    exit();
}

// Token is single-use
unset($_SESSION['csrf_token']);

// --- Perform logout ---
// If helper exists and exposes secureLogout(), use it
if (function_exists('secureLogout')) {
    // Expectation: secureLogout() destroys session + expires cookie safely
    secureLogout('mama');
    // secureLogout should handle redirect; if not, fall through to redirect below
} else {
    // Manual logout (current branch behavior)
    session_unset();
    session_destroy();

    // Expire the session cookie on client
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        // Handle PHP 7.3+ samesite if needed, but simple form is fine:
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

// Final redirect (in case helper didn't already redirect)
header('Location: mama-login.php');
exit();
