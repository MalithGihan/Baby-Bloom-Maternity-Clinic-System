<?php
// Use secure session start for login pages
require_once __DIR__ . '/../shared/secure-session-start.php';

// Include centralized logger
require_once __DIR__ . '/../shared/logger.php';

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (isset($_SESSION["staffEmail"])) {
    logToFile("Staff already logged in: {$_SESSION['staffEmail']} - Redirected to dashboard");
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

require_once __DIR__ . '/google-oauth/google-oauth-config.php';
$oauth = new GoogleOAuth();
$googleAuthUrl = $oauth->getAuthUrl('staff');

logToFile("Redirecting staff to Google OAuth for login");

$error_message = $_SESSION['login_error'] ?? "";
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
    logToFile("Staff login failed: $error_message");
}

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff-reset-email'])) {
    if (
        isset($_POST['csrf_token'], $_SESSION['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $resetEmail = $_POST['staff-reset-email'];
        logToFile("Staff password reset requested for: " . $resetEmail);
        // Optional harden: rotate token after successful POST to reduce replay
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else {
        $error_message = 'Invalid request. Please try again.';
        logToFile("Staff password reset CSRF failed");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Staff Login</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../../css/common-variables.css">
        <link rel="stylesheet" type="text/css" href="../../css/login-pages.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
        <script src="../../js/staff-login.js"></script>
    </head>
<body>
    <div class="mama-login-content d-flex">
        <div class="mama-login-logo d-flex flex-column align-items-center justify-content-center">
            <img class="bb-logo" src="../../images/logos/babybloom-main-logo.webp" alt="BabyBloom main logo" id="bb-logo">
            <h1 class="index-title">Baby Bloom</h1>
            <h3 class="index-subtitle"> Maternity Clinic System</h3>
        </div>
        <div class="mama-login-container d-flex flex-column align-items-center justify-content-center">
            <img src="../../images/login-back-btn.png" class="back-btn" id="back-btn">
            <div class="login-btn-container flex-column align-items-center justify-content-center" id="login-btn-container">
                <button class="login-btn" id="login-btn">STAFF LOGIN</button>
            </div>
            <div class="login-container flex-column align-items-center justify-content-center" id="login-container">
                <h3 class="login-title l-title">STAFF LOGIN</h3>

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Google OAuth Button -->
                <a href="<?php echo htmlspecialchars($googleAuthUrl, ENT_QUOTES, 'UTF-8'); ?>" class="google-oauth-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
                        <path fill="#4285F4" d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z"/>
                        <path fill="#34A853" d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2.04a4.8 4.8 0 0 1-7.18-2.53H1.83v2.07A8 8 0 0 0 8.98 17z"/>
                        <path fill="#FBBC05" d="M4.5 10.49a4.8 4.8 0 0 1 0-3.07V5.35H1.83a8 8 0 0 0 0 7.28l2.67-2.14z"/>
                        <path fill="#EA4335" d="M8.98 4.72c1.16 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.35L4.5 7.42a4.77 4.77 0 0 1 4.48-2.7z"/>
                    </svg>
                    Continue with Google
                </a>

                <div class="oauth-divider">
                    <span class="oauth-divider-text">or</span>
                </div>

                <form action="handlers/staff-login-handler.php" method="POST" class="d-flex flex-column align-items-center justify-content-center">
                    <input type="email" class="login-input" id="login-email" name="staff-email" placeholder="Enter your email address" required>
                    <input type="password" class="login-input" id="login-pass" name="staff-password" placeholder="Enter your password" required>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="login-form-btn-group d-flex flex-row">
                        <input type="submit" value="LOGIN" class="login-submit-btn">
                    </div>
                </form>

                <button id="login-reset-btn" class="login-reset-btn">RESET PASSWORD</button>
            </div>

            <div class="login-reset-container flex-column align-items-center justify-content-center" id="login-reset-container">
                <h3 class="pass-reset-title l-title">RESET PASSWORD</h3>
                <form method="post" class="d-flex flex-column align-items-center justify-content-center">
                    <input type="email" class="login-input" id="login-reset-email" name="staff-reset-email" placeholder="Enter your email address">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="submit" value="RESET PASSWORD" class="login-reset-btn-r">
                </form>
            </div>
        </div>
    </div>

</body>
</html>
