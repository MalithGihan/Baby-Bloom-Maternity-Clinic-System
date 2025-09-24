<?php
// Use secure session initialization for protected pages
require_once __DIR__ . '/../shared/session-init.php';
include '../shared/db-access.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - REGISTERED MOTHERS</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
        <script rel="script" type="text/js" src="../../js/script.js"></script>
        <style>
            :root{
                --bg: #EFEBEA;
                --light-txt: #0D4B53;
                --light-txt2:#000000;
                --dark-txt: #86B6BB;
            }
            @font-face {
                font-family: 'Inter-Bold'; /* Heading font */
                src: url('../../font/Inter-Bold.ttf') format('truetype'); 
                font-weight: 700;
            }
            @font-face {
                font-family: 'Inter-Light'; /* Text font */
                src: url('../../font/Inter-Light.ttf') format('truetype'); 
                font-weight: 300;
            }

            body{
                margin:0 !important;
                padding:0 !important;
                background-color: var(--bg) !important;
            }
            .report-row{
                justify-content: space-between;
            }
            .scan-qr-btn,#mom-search-btn{
                font-family: 'Inter-Bold';
                font-size:1rem;
                background-color:var(--light-txt);
                color:var(--bg);
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                transition:0.6s;
            }
            .scan-qr-btn:hover,#mom-search-btn:hover{
                background-color:var(--dark-txt);
                transition:0.6s;
            }
            .bb-n-btn{
                background-color: var(--dark-txt);
                color: var(--bg);
                font-family: 'Inter-Bold';
                font-size: 1rem;
                border:0px !important;
                outline:none !important;
                border-radius: 10rem;
                padding: 0.5rem 1.5rem;
                transition: 0.6s;
            }
            .bb-n-btn:hover{
                background-color: var(--light-txt) !important;
                transition: 0.6s;
            }
            .mom-search-continer{
                gap:1rem;
            }
            #mom-nic-search{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
                outline:none;
                background-color:var(--bg);
                border:2px solid var(--light-txt);
                border-radius:10rem;
                width:30vw;
                text-align: center;
            }
            .mom-list-btn{
                background-color:var(--dark-txt);
                color:var(--bg);
                font-family: 'Inter-Bold';
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                text-decoration: none;
                transition:0.6s;
            }
            .mom-list-btn:hover{
                background-color:var(--light-txt);
                color:var(--bg);
                transition:0.6s;
            }
            th,td{
                background-color:var(--bg) !important;
            }
            td{
                color:var(--light-txt);
                font-family: 'Inter-Light';
            }
            .table-btn-container{
                gap:1rem;
            }

        </style>
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
                <h2 class="main-header-title">SUPPLEMENT REQUESTS</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../../images/midwife-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo $_SESSION['staffFName']; ?> <?php echo $_SESSION['staffSName']; ?></div>
                            <div class="useremail"><?php echo $_SESSION['staffEmail']; ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="../auth/staff-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex flex-column">
                <div class="report-row d-flex justify-content-end">
                    <!-- <button class="scan-qr-btn" id="scan-qr-btn">Scan QR</button> -->
                    <form class="mom-search-continer d-flex" method="POST">
                        <input type="text" id="mom-nic-search" name="order-search" placeholder="Enter Mother NIC">
                        <input type="submit" name="submit" value="Search" id="mom-search-btn">
                        <input type="submit" name="clear" value="Clear Search" class="bb-n-btn" id="clear-results-btn">
                    </form>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="dd">Date</th>
                            <th class="dd">NIC</th>
                            <th class="dd">Delivery Type</th>
                            <th class="dd">Delivery Status</th>
                        </tr>
                    </thead>
                    <?php
                        
                        if(isset($_POST['submit'])){
                            $search = $_POST['order-search'];

                            //This conditions will check the entered value is NULL or not
                            if($search==""){
                                echo '<script>';
                                echo 'alert("Enter mother NIC number to view orders list!!!");';
                                echo 'window.location.href="supplement-request-status.php";';
                                echo '</script>';
                            }
                            else{
                                $sql = "SELECT * FROM supplement_request WHERE NIC = ? ORDER BY ordered_date DESC";
                                $stmt = $con->prepare($sql);
                                if ($stmt === false) {
                                    echo '<script>alert("System error. Please try again."); window.location.href="supplement-request-status.php";</script>';
                                    exit();
                                }
                                $stmt->bind_param("s", $search);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if($result){
                                    $num = mysqli_num_rows($result);
                                    echo "$num results found.";
                                    if($num > 0){
                                        while($row = mysqli_fetch_assoc($result)){
                                            if ($row['status'] == 'Pending') {//This condition will control the button appearance depend on the status.
                                                echo '
                                                <tbody">
                                                    <tr class="vaccine-results">
                                                        <td>'.htmlspecialchars($row['ordered_date'], ENT_QUOTES, 'UTF-8').'</td>
                                                        <td>'.htmlspecialchars($row['NIC'], ENT_QUOTES, 'UTF-8').'</td>
                                                        <td>'.htmlspecialchars($row['delivery'], ENT_QUOTES, 'UTF-8').' </td>
                                                        <td class="order-status" id="order-status"><b>'.htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8').'</b></td>
                                                        <td class="table-btn-container d-flex flex-row justify-content-center">
                                                            <a class="mom-list-btn" href="status-update.php?id='.urlencode($row["SR_ID"]).'">Confirm Delivery/Pickup</a>
                                                        </td>
                                                    </tr>
                                                </tbody>';
                                            }else {
                                                echo '
                                                <tbody>
                                                    <tr class="vaccine-results">
                                                        <td>'.htmlspecialchars($row['ordered_date'], ENT_QUOTES, 'UTF-8').'</td>
                                                        <td>'.htmlspecialchars($row['NIC'], ENT_QUOTES, 'UTF-8').'</td>
                                                        <td>'.htmlspecialchars($row['delivery'], ENT_QUOTES, 'UTF-8').' </td>
                                                        <td class="order-status" id="order-status"><b>'.htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8').'</b></td>
                                                    </tr>
                                                </tbody>';
                                            }
                                            
                                        }
                                    }
                                    else{
                                        echo '<h3>Data not found</h3>';
                                    }
                                    $stmt->close();
                                }
                            }
                        }
                        else if(!isset($_POST['submit'])){
                            $sql = "SELECT * FROM supplement_request";
                            $result = mysqli_query($con,$sql);
                            if($result){
                                $num = mysqli_num_rows($result);
                                echo "$num results found.";
                                if($num > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        if ($row['status'] == 'Pending') {//This condition will control the button appearance depend on the status.
                                            echo '
                                            <tbody">
                                                <tr class="vaccine-results">
                                                    <td>'.htmlspecialchars($row['ordered_date'], ENT_QUOTES, 'UTF-8').'</td>
                                                    <td>'.htmlspecialchars($row['NIC'], ENT_QUOTES, 'UTF-8').'</td>
                                                    <td>'.htmlspecialchars($row['delivery'], ENT_QUOTES, 'UTF-8').' </td>
                                                    <td class="order-status" id="order-status"><b>'.htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8').'</b></td>
                                                    <td class="table-btn-container d-flex flex-row justify-content-center">
                                                        <a class="mom-list-btn" href="status-update.php?id='.urlencode($row["SR_ID"]).'">Confirm Delivery/Pickup</a>
                                                    </td>
                                                </tr>
                                            </tbody>';
                                        }else {
                                            echo '
                                            <tbody>
                                                <tr class="vaccine-results">
                                                    <td>'.htmlspecialchars($row['ordered_date'], ENT_QUOTES, 'UTF-8').'</td>
                                                    <td>'.htmlspecialchars($row['NIC'], ENT_QUOTES, 'UTF-8').'</td>
                                                    <td>'.htmlspecialchars($row['delivery'], ENT_QUOTES, 'UTF-8').' </td>
                                                    <td class="order-status" id="order-status"><b>'.htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8').'</b></td>
                                                </tr>
                                            </tbody>';
                                        }
                                        
                                    }
                                }
                                else{
                                    echo '<h3>Data not found</h3>';
                                }
                            }
                        }
                        else if(isset($_POST['clear'])){
                            // Redirect to the same page without any POST data
                            header("Location: ".$_SERVER['PHP_SELF']);
                            exit;
                        }

                        
/*
                        if(isset($_POST['submit'])){
                            $search = $_POST['vaccine-name'];

                            
                        } */
                    ?>
                </table>
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../dashboard/staff-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        
    </script>
</body>
</html>
