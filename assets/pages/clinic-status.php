<?php
session_start();

if ($_SESSION["staffPosition"] != "Sister" ) {
    header("Location: staff-dashboard.php"); // Redirect to staff dashboard if the logged in user isn't a Sister
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Clinic Status</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script rel="script" src="../js/bootstrap.min.js"></script>
        <script src="../js/highcharts.js"></script>
        <style>
            .status-option-container{
                flex:20%;
            }
            .status-data-container{
                flex:80%;
            }
            .status-option{
                text-align: center;
                padding: 0.8rem 1.5rem;
                margin:0.5rem 0rem;
            }
            .status-data-container{
                border: 2px solid var(--light-txt);
                border-radius: 2rem;
                padding: 1rem;
            }
            .status-title{
                font-family: 'Inter-Bold';
                font-size:1.5rem;
                color:var(--light-txt);
            }
            @media only screen and (min-width:768px){
                .status-data-container{
                    border: 2px solid var(--light-txt);
                    border-radius: 2rem;
                    padding: 2rem;
                }
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
                <h2 class="main-header-title">CLINIC STATUS</h2>
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
            <div class="main-content d-flex">
                <div class="status-option-container d-flex flex-column">
                    <div class="status-option bb-n-btn" id="staff-btn">Staff Status</div>
                    <div class="status-option bb-n-btn" id="mothers-btn">Mothers Status</div>
                </div>
                <div class="status-data-container d-flex flex-column">
                    <div class="staff-status-container d-flex flex-column" id="staff-container">
                        <h3 class="status-title">Staff Status</h3>
                    </div>
                    <div class="mother-status-container d-flex flex-column" id="mother-container">
                        <h3 class="status-title">Mothers Status</h3>
                    </div>
                </div>
            </div>
            <div class="main-footer d-flex flex-row justify-content-between">
                <a href="../pages/staff-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        var stfBtn = document.getElementById("staff-btn");
        var momBtn = document.getElementById("mothers-btn");

        var stfContainer = document.getElementById("staff-container");
        var momContainer = document.getElementById("mother-container");
    </script>
</body>
</html>
