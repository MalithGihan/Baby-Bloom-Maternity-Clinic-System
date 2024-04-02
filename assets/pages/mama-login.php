<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Login</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script rel="script" type="text/js" href="../js/bootstrap.min.js"></script>
        <style>
            :root{
            --bg: #EFEBEA;
            --light-txt: #0D4B53;
            --light-txt2:#000000;
            --dark-txt: #86B6BB;
            }
            body{
                margin:0 !important;
                padding:0 !important;
                background-color: var(--bg) !important;
                height:100vh;
            }
            @font-face {
                font-family: 'Inter-Bold'; /* Heading font */
                src: url('../font/Inter-Bold.ttf') format('truetype'); 
                font-weight: 700;
            }
            @font-face {
                font-family: 'Inter-Light'; /* Text font */
                src: url('../font/Inter-Light.ttf') format('truetype'); 
                font-weight: 300;
            }

            /* Tablet media query */
            @media only screen and (min-width:768px){}

            /* Laptop media query */
            @media only screen and (min-width:768px){}

        </style>
    </head>
<body>
    <div class="mama-login-content d-flex">
        <div class="mama-login-logo">
            <img class="bb-logo" src="../images/logos/babybloom-main-logo.webp" alt="BabyBloom main logo">
        </div>
        <div class="mama-login-container">
            <img src="../images/login-back-btn.png" class="back-btn" id="back-btn">
            <div class="login-btn-container d-flex flex-column" id="login-btn-container">
                <a href="../pages/mama-registration.php" class="mama-register-btn">
                    <button class="register-btn">Clinic Registration</button>
                </a>
                <button class="login-btn" id="login-btn">Mama Login</button>
            </div>
            <div class="login-container" id="login-container">
                <h3 class="login-title">MAMA LOGIN</h3>
                <form method="post" class="d-flex flex-column">
                    <input type="email" id="login-email" name="mama-email" placeholder="Enter your email address">
                    <input type="password" id="login-pass" name="mama-password" placeholder="Enter your password">
                    <div class="login-form-btn-group d-flex flex-row">
                        <input type="submit" value="LOGIN">
                        <button id="login-reset-btn" class="login-reset-btn">RESET PASSWORD</button>
                    </div>
                </form>
            </div>
            <div class="login-reset-container" id="login-reset-container">
                <h3 class="pass-reset-title">RESET PASSWORD</h3>
                <form method="post" class="d-flex flex-column">
                    <input type="email" id="login-email" name="mama-reset-email" placeholder="Enter your email address">
                    <input type="submit" value="RESET PASSWORD">
                </form>
            </div>
        </div>
    </div>
</body>
</html>