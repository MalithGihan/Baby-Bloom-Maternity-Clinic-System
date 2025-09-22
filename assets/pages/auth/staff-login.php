<?php
session_start();

// Generate CSRF token if missing
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirect if already logged in
if (isset($_SESSION["staffEmail"])) {
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

// Get error message if exists
$error_message = $_SESSION['login_error'] ?? "";
// Clear the error after displaying
if(isset($_SESSION['login_error'])){
    unset($_SESSION['login_error']);
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
                    <div class="error-message" style="color: #d32f2f; font-family: 'Inter-Bold'; font-size: 0.9rem; margin: 0.5rem 0; text-align: center;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <form action="handlers/staff-login-handler.php" method="POST" class="d-flex flex-column align-items-center justify-content-center">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="email" class="login-input" id="login-email" name="staff-email" placeholder="Enter your email address" required>
                    <input type="password" class="login-input" id="login-pass" name="staff-password" placeholder="Enter your password" required>
                    <div class="login-form-btn-group d-flex flex-row">
                        <input type="submit" value="LOGIN" class="login-submit-btn">
                        
                    </div>
                </form>
                <button id="login-reset-btn" class="login-reset-btn">RESET PASSWORD</button>
            </div>
            <div class="login-reset-container flex-column align-items-center justify-content-center" id="login-reset-container">
                <h3 class="pass-reset-title l-title">RESET PASSWORD</h3>
                <form action="handlers/staff-reset-handler.php" method="post" class="d-flex flex-column align-items-center justify-content-center">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="email" class="login-input" id="login-reset-email" name="staff-reset-email" placeholder="Enter your email address" required>
                    <input type="submit" value="RESET PASSWORD" class="login-reset-btn-r">
                </form>
            </div>
        </div>
    </div>

    <script>
        // TODO: Remove debug statement below
        console.log("GG WP");
        var loginBtnContainer = document.getElementById("login-btn-container");
        var loginContainer = document.getElementById("login-container");
        var resetContainer = document.getElementById("login-reset-container");

        var backBtn = document.getElementById("back-btn");
        var logSelectBtn = document.getElementById("login-btn");
        var resetSelectBtn = document.getElementById("login-reset-btn");


        backBtn.addEventListener("click",function(){
            loginBtnContainer.style.display = "flex";
            loginContainer.style.display = "none";
            resetContainer.style.display = "none";
        })

        logSelectBtn.addEventListener("click",function(){
            backBtn.style.display = "block";
            loginBtnContainer.style.display = "none";
            loginContainer.style.display = "flex";
        })

        resetSelectBtn.addEventListener("click",function(){
            loginContainer.style.display = "none";
            resetContainer.style.display = "flex";
        })

        var loginImg = document.getElementById("bb-logo");

        loginImg.addEventListener("click",function(){
            window.location.href="../../index.php";
        })
        
    </script>
</body>
</html>
