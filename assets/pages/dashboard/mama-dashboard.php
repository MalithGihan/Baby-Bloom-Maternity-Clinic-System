<?php
require_once __DIR__ . '/../shared/bootstrap.php';
session_start();

if (!isset($_SESSION["mamaEmail"])) {
    header("Location: ../auth/mama-login.php"); // Redirect to pregnant mother login page
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Dashboard</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../../css/common-variables.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
    </head>
<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
            <div class="d-flex flex-column">
                <h1 class="common-title">BabyBloom</h1>
                <h3 class="common-description">Maternity Clinic System</h3>
            </div>
        </header>
        <main>
            <div class="main-header d-flex">
                <h2 class="main-header-title">DASHBOARD</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../../images/mama-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo $_SESSION['First_name']; ?> <?php echo $_SESSION['Last_name']; ?></div>
                            <div class="useremail"><?php echo $_SESSION['mamaEmail']; ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="../auth/mama-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex">
                <a href="../appointments/appointment.php" class="option">
                    <div class="d-flex flex-column align-items-center">
                        <img src="../../images/mama-dashbaord/option1.png" class="option-img">
                        <p class="option-name">Book Appointment</p>
                    </div>   
                </a>
                <a href="../supplements/mama-order-supplement.php" class="option">
                    <div class="d-flex flex-column align-items-center">
                        <img src="../../images/mama-dashbaord/option2.png" class="option-img">
                        <p class="option-name">Order Supplement</p>
                    </div>   
                </a>
                <a href="../shared/mama-qr.php" class="option">
                    <div class="d-flex flex-column align-items-center">
                        <img src="../../images/mama-dashbaord/option3.png" class="option-img">
                        <p class="option-name">View QR Code</p>
                    </div>   
                </a>
            </div>
        </main>
    </div>

    <script>
    </script>
</body>
</html>
