<?php
session_start();

//Adding php qr code api file
include('../qr-api/phpqrcode/qrlib.php');

$NIC = $_SESSION["NIC"];
// TODO: Remove debug statement below
echo $NIC;

if (!isset($_SESSION["mamaEmail"])) {
    header("Location: ../auth/mama-login.php"); // Redirect to pregnant mother login page
    exit();
}

$filePath = '../images/mama-qr-codes/'.$NIC.'_qr.png';

QRcode::png($NIC, $filePath, QR_ECLEVEL_L, 8);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama QR Code</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script rel="script" type="text/js" src="../js/bootstrap.min.js"></script>
        <style>
            .option-name{
                font-family: 'Inter-Bold';
                color:var(--light-txt);
                margin-top:1rem !important;
            }
        </style>
    </head>
<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
            <div class="d-flex flex-column">
                <h1 class="common-title">BabyBloom</h1>
                <h3 class="common-description">Maternity Clinic System</h3>
            </div>
        </header>
        <main>
            <div class="main-header d-flex">
                <h2 class="main-header-title">YOUR QR CODE</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../images/mama-image.png" alt="User profile image" class="usr-image">
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
                <div class="d-flex flex-column align-items-center">
                    <!-- <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $NIC ?>&bgcolor=EFEBEA" class="option-img"> -->
                    <?php
                        //Below code will dynamically change the qr code based on the mother's NIC
                        echo '<img src="../images/mama-qr-codes/'.$NIC.'_qr.png" class="option-img" />';
                    ?>
                    <p class="option-name">Present this QR code when needed.</p>
                </div>  
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../dashboard/mama-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
    </script>
</body>
</html>
