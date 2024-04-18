<?php
session_start();

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

echo $_SESSION['staffPosition'];

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Midwife Dashboard</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script rel="script" type="text/js" href="../js/bootstrap.min.js"></script>
        <style>
            
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
            <form enctype="multipart/form-data" action="http://api.qrserver.com/v1/read-qr-code/" method="POST">
            <!-- MAX_FILE_SIZE (maximum file size in bytes) must precede the file input field used to upload the QR code image -->
                <input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
            <!-- The "name" of the input field has to be "file" as it is the name of the POST parameter -->
            Choose QR code image to read/scan: <input name="file" type="file" />
                <input type="submit" value="Read QR code" />
            </form>
            <div class="main-header d-flex">
                <h2 class="main-header-title">STAFF DASHBOARD</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../images/midwife-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo $_SESSION['staffFName']; ?> <?php echo $_SESSION['staffSName']; ?></div>
                            <div class="useremail"><?php echo $_SESSION['staffEmail']; ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="staff-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <?php
                if($_SESSION['staffPosition'] == "Midwife") {//Midwife position based dashboard options
                ?>
                    <div class="main-content d-flex">
                        <a href="../pages/mw-mama-registration.php" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/midwife-dashboard/option1.png" class="option-img">
                                <p class="option-name">Pregnant Mother Registration</p>
                            </div>   
                        </a>
                        <a href="../pages/mw-mother-list.php" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/midwife-dashboard/option2.png" class="option-img">
                                <p class="option-name">Pregnant Mother Details</p>
                            </div>   
                        </a>
                        <a href="../pages/supplement-request-status.php" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/midwife-dashboard/option3.png" class="option-img">
                                <p class="option-name">Supplement Request Status</p>
                            </div>   
                        </a>
                    </div>
                <?php
                }else if($_SESSION['staffPosition'] == "Doctor"){//Doctor position based dashboard options
                    ?>
                    <div class="main-content d-flex">
                        <a href="../pages/mw-mother-list.php" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/midwife-dashboard/option2.png" class="option-img">
                                <p class="option-name">Pregnant Mother Details</p>
                            </div>   
                        </a>
                    </div>
                
                <?php
                }else if($_SESSION['staffPosition'] == "Sister"){//Sister aka Incharge position based dashboard options
                    ?>
                    <div class="main-content d-flex">
                        <a href="../pages/staff-management.php" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/incharge-dashboard/option1.png" class="option-img">
                                <p class="option-name">Staff Details</p>
                            </div>   
                        </a>
                        <a href="../pages/mw-mother-list.php" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/incharge-dashboard/option2.png" class="option-img">
                                <p class="option-name">Mothers' Details</p>
                            </div>   
                        </a>
                        <a href="#" class="option">
                            <div class="d-flex flex-column align-items-center">
                                <img src="../images/incharge-dashboard/option3.png" class="option-img">
                                <p class="option-name">Inventory Status</p>
                            </div>   
                        </a>
                    </div>
                <?php
                }
                ?>
            
        </main>
    </div>

    <script>
    </script>
</body>
</html>
