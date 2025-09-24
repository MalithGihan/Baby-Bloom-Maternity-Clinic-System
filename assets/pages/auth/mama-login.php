<?php
// Use secure session start for login pages
require_once __DIR__ . '/../shared/secure-session-start.php';

// Include centralized logger
require_once __DIR__ . '/../shared/logger.php';

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirect if already logged in
if (isset($_SESSION["mamaEmail"])) {
    header("Location: ../dashboard/mama-dashboard.php");
    exit();
}

// Rate limiting check 
$isLockedOut = false;
$remainingTime = 0;
$lockoutDuration = 30; 

error_log("DEBUG: login_attempts = " . ($_SESSION['login_attempts'] ?? 'not set'));
error_log("DEBUG: last_attempt_time = " . ($_SESSION['last_attempt_time'] ?? 'not set'));

// Check lockout status and handle expiration
if (isset($_SESSION['login_attempts']) && isset($_SESSION['last_attempt_time'])) {
    error_log("DEBUG: login_attempts value = " . $_SESSION['login_attempts']);
    error_log("DEBUG: last_attempt_time value = " . $_SESSION['last_attempt_time']);
    error_log("DEBUG: current time = " . time());
    
    if ($_SESSION['login_attempts'] >= 3) {
        $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];
        error_log("DEBUG: timeSinceLastAttempt = " . $timeSinceLastAttempt);
        error_log("DEBUG: lockoutDuration = " . $lockoutDuration);
        
        if ($timeSinceLastAttempt < $lockoutDuration) {
            $isLockedOut = true;
            $remainingTime = $lockoutDuration - $timeSinceLastAttempt;
            error_log("DEBUG: USER IS LOCKED OUT. Remaining time: " . $remainingTime . " seconds");
        } else {
            // Reset attempts if lockout period has passed
            error_log("DEBUG: Lockout period passed. Resetting attempts completely.");
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_attempt_time']);
            // Also clear any lockout-related error messages
            if (isset($_SESSION['login_error']) && 
                (strpos($_SESSION['login_error'], 'Too many failed') !== false || 
                 strpos($_SESSION['login_error'], 'locked') !== false)) {
                unset($_SESSION['login_error']);
            }
        }
    }
}

// Get error message if exists (and clear it right after)
$error_message = $_SESSION['login_error'] ?? "";
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
    logLoginAttempt($_POST['mama-email'] ?? 'Unknown', 'Failed');
}

// --- Google OAuth (build the auth URL) ---
require_once __DIR__ . "/google-oauth/google-oauth-config.php";

$oauth = new GoogleOAuth();
$googleAuthUrl = $oauth->getAuthUrl('mama');
logLoginAttempt('OAuth', 'Redirected to Google OAuth');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Baby Bloom - Mama Login</title>
  <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
  <link rel="stylesheet" type="text/css" href="../../css/style.css">
  <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../../css/common-variables.css">
  <link rel="stylesheet" type="text/css" href="../../css/login-pages.css">
  <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
  <script src="../../js/mama-login.js"></script>

</head>
<body>
  <div class="mama-login-content d-flex">
    <div class="mama-login-logo d-flex flex-column align-items-center justify-content-center">
      <img class="bb-logo" src="../../images/logos/babybloom-main-logo.webp" alt="BabyBloom main logo" id="bb-logo">
      <h1 class="index-title">Baby Bloom</h1>
      <h3 class="index-subtitle">Maternity Clinic System</h3>
    </div>

    <div class="mama-login-container d-flex flex-column align-items-center justify-content-center">
      <img src="../../images/login-back-btn.png" class="back-btn" id="back-btn">

      <div class="login-btn-container flex-column align-items-center justify-content-center" id="login-btn-container">
        <a href="mama-registration.php" class="mama-register-btn d-flex flex-row justify-content-center">
          <button class="register-btn">CLINIC REGISTRATION</button>
        </a>
        <button class="login-btn" id="login-btn">MAMA LOGIN</button>
      </div>

      <div class="login-container flex-column align-items-center justify-content-center" id="login-container">
        <h3 class="login-title l-title">MAMA LOGIN</h3>

        <?php if (!empty($error_message)): ?>
          <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
          </div>
        <?php endif; ?>

        <?php if ($isLockedOut): ?>
          <div class="error-message" id="lockout-message">
            Too many failed login attempts. Please try again in <span id="countdown"><?php echo $remainingTime; ?></span> seconds.
          </div>
        <?php endif; ?>

        <!-- Google OAuth Button -->
        <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="google-oauth-btn" aria-label="Continue with Google" <?php echo $isLockedOut ? 'style="pointer-events: none; opacity: 0.5;"' : ''; ?>>
          <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true" focusable="false">
            <path fill="#4285F4" d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z"/>
            <path fill="#34A853" d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2.04a4.8 4.8 0 0 1-7.18-2.53H1.83v2.07A8 8 0 0 0 8.98 17z"/>
            <path fill="#FBBC05" d="M4.5 10.49a4.8 4.8 0 0 1 0-3.07V5.35H1.83a8 8 0 0 0 7.28l2.67-2.14z"/>
            <path fill="#EA4335" d="M8.98 4.72c1.16 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.35L4.5 7.42a4.77 4.77 0 0 1 4.48-2.7z"/>
          </svg>
          Continue with Google
        </a>

        <div class="hint-note">If you registered with Google, please use the button above.</div>

        <div class="oauth-divider"><span class="oauth-divider-text">or</span></div>

        <form action="handlers/mama-login-handler.php" method="post" class="d-flex flex-column align-items-center justify-content-center" id="login-form">
          <input type="email" class="login-input" id="login-email" name="mama-email" placeholder="Enter your email address" required <?php echo $isLockedOut ? 'disabled' : ''; ?>>
          <input type="password" class="login-input" id="login-pass" name="mama-password" placeholder="Enter your password" required <?php echo $isLockedOut ? 'disabled' : ''; ?>>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <div class="login-form-btn-group d-flex flex-row">
            <input type="submit" value="LOGIN" class="login-submit-btn" id="login-submit-btn" <?php echo $isLockedOut ? 'disabled' : ''; ?>>
          </div>
        </form>

        <button id="login-reset-btn" class="login-reset-btn" <?php echo $isLockedOut ? 'disabled' : ''; ?>>RESET PASSWORD</button>
      </div>

      <div class="login-reset-container flex-column align-items-center justify-content-center" id="login-reset-container">
        <h3 class="pass-reset-title l-title">RESET PASSWORD</h3>
        <form method="post" action="handlers/password-reset-handler.php" class="d-flex flex-column align-items-center justify-content-center">
          <input type="email" class="login-input" id="login-reset-email" name="mama-reset-email" placeholder="Enter your email address" required>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input type="submit" value="RESET PASSWORD" class="login-reset-btn-r">
        </form>
      </div>
    </div>
  </div>

  <?php if ($isLockedOut): ?>
  <script>
    // Disable form elements and show countdown
    document.addEventListener('DOMContentLoaded', function() {
      const countdownElement = document.getElementById('countdown');
      let timeLeft = <?php echo $remainingTime; ?>;
      
      // Update countdown immediately
      countdownElement.textContent = timeLeft;
      
      const interval = setInterval(function() {
        timeLeft--;
        countdownElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
          clearInterval(interval);
          countdownElement.textContent = '0';
          
          // Show refreshing message
          const lockoutMessage = document.getElementById('lockout-message');
          if (lockoutMessage) {
            lockoutMessage.innerHTML = 'Lockout expired. Refreshing page...';
            lockoutMessage.style.color = 'green';
          }
          
          // Refresh the page after a short delay
          setTimeout(function() {
            window.location.reload();
          }, 1000);
        }
      }, 1000);
    });
  </script>
  <?php endif; ?>

</body>
</html>