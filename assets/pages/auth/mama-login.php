<?php

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// Redirect if already logged in
if (isset($_SESSION["mamaEmail"])) {
    header("Location: ../dashboard/mama-dashboard.php");
    exit();
}

// Get error message if exists (and clear it right after)
// $error_message = $_SESSION['login_error'] ?? "";
// if (isset($_SESSION['login_error'])) {
//     unset($_SESSION['login_error']);
// }

// --- Google OAuth (build the auth URL) ---
// Adjust the include path below if your shared folder differs.
require_once __DIR__ . "/google-oauth/google-oauth-config.php";

$oauth = new GoogleOAuth();
$googleAuthUrl = $oauth->getAuthUrl('mama'); // keep state = 'mama'
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

  <!-- Minimal inline styles for the Google button; remove if you already styled this in login-pages.css -->
  <style>
    .google-oauth-btn{
      background-color:#fff;
      color:#000;
      padding:0.8rem 2rem;
      font-family:'Inter-Bold';
      font-size:1rem;
      border-radius:10rem;
      border:2px solid #000;
      width:90%;
      transition:0.3s;
      text-decoration:none;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:.5rem;
    }
    .google-oauth-btn:hover{ background-color:#f5f5f5; text-decoration:none; color:#000; }
    .oauth-divider{
      display:flex; align-items:center; text-align:center; margin:1rem 0; width:90%;
    }
    .oauth-divider::before, .oauth-divider::after{
      content:''; flex:1; height:1px; background:var(--light-txt);
    }
    .oauth-divider:not(:empty)::before{ margin-right:.25em; }
    .oauth-divider:not(:empty)::after{ margin-left:.25em; }
    .oauth-divider-text{ font-family:'Inter-Light'; color:var(--light-txt); font-size:.9rem; }
    .hint-note{ font-family:'Inter-Light'; font-size:.85rem; color:#555; margin-top:.5rem; text-align:center; width:90%; }
  </style>
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
          <div class="error-message" style="color:#d32f2f; font-family:'Inter-Bold'; font-size:.9rem; margin:.5rem 0; text-align:center;">
            <?php echo htmlspecialchars($error_message); ?>
          </div>
        <?php endif; ?>

        <!-- Google OAuth Button -->
        <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="google-oauth-btn" aria-label="Continue with Google">
          <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true" focusable="false">
            <path fill="#4285F4" d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z"/>
            <path fill="#34A853" d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2.04a4.8 4.8 0 0 1-7.18-2.53H1.83v2.07A8 8 0 0 0 8.98 17z"/>
            <path fill="#FBBC05" d="M4.5 10.49a4.8 4.8 0 0 1 0-3.07V5.35H1.83a8 8 0 0 0 0 7.28l2.67-2.14z"/>
            <path fill="#EA4335" d="M8.98 4.72c1.16 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.35L4.5 7.42a4.77 4.77 0 0 1 4.48-2.7z"/>
          </svg>
          Continue with Google
        </a>

        <div class="hint-note">If you registered with Google, please use the button above.</div>

        <div class="oauth-divider"><span class="oauth-divider-text">or</span></div>

        <form action="handlers/mama-login-handler.php" method="post" class="d-flex flex-column align-items-center justify-content-center">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input type="email" class="login-input" id="login-email" name="mama-email" placeholder="Enter your email address" required>
          <input type="password" class="login-input" id="login-pass" name="mama-password" placeholder="Enter your password" required>
          <div class="login-form-btn-group d-flex flex-row">
            <input type="submit" value="LOGIN" class="login-submit-btn">
          </div>
        </form>

        <button id="login-reset-btn" class="login-reset-btn">RESET PASSWORD</button>
      </div>

      <div class="login-reset-container flex-column align-items-center justify-content-center" id="login-reset-container">
        <h3 class="pass-reset-title l-title">RESET PASSWORD</h3>
        <form method="post" class="d-flex flex-column align-items-center justify-content-center">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input type="email" class="login-input" id="login-reset-email" name="mama-reset-email" placeholder="Enter your email address">
          <input type="submit" value="RESET PASSWORD" class="login-reset-btn-r">
        </form>
      </div>
    </div>
  </div>

  <script>
    // UI toggles
    var loginBtnContainer = document.getElementById("login-btn-container");
    var loginContainer = document.getElementById("login-container");
    var resetContainer = document.getElementById("login-reset-container");
    var backBtn = document.getElementById("back-btn");
    var logSelectBtn = document.getElementById("login-btn");
    var resetSelectBtn = document.getElementById("login-reset-btn");
    var loginImg = document.getElementById("bb-logo");

    backBtn.addEventListener("click", function(){
      loginBtnContainer.style.display = "flex";
      loginContainer.style.display = "none";
      resetContainer.style.display = "none";
    });

    logSelectBtn.addEventListener("click", function(){
      backBtn.style.display = "block";
      loginBtnContainer.style.display = "none";
      loginContainer.style.display = "flex";
    });

    resetSelectBtn.addEventListener("click", function(){
      loginContainer.style.display = "none";
      resetContainer.style.display = "flex";
    });

    loginImg.addEventListener("click", function(){
      window.location.href = "../../index.php";
    });
  </script>
</body>
</html>
