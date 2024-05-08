<?php 
session_start();

include 'dbaccess.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$currentPageURL = urlencode($_SERVER['REQUEST_URI']);
echo $currentPageURL;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Today Appointments</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/script.js"></script>
        <style>
            :root{
                --bg: #EFEBEA;
                --light-txt: #0D4B53;
                --light-txt2:#000000;
                --dark-txt: #86B6BB;
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

            body{
                margin:0 !important;
                padding:0 !important;
                background-color: var(--bg) !important;
            }
            .reg-btn{
                background-color:var(--light-txt) !important;
                color:var(--bg) !important;
                font-family: 'Inter-Bold' !important;
                align-self:flex-end !important;
                transition:0.6s;
            }
            .reg-btn:hover{
                background-color:var(--dark-txt) !important;
                border:2px solid var(--dark-txt) !important;
                color:var(--bg) !important;
                transition:0.6s;
            }
            .row-title{
                font-family: 'Inter-Bold';
                font-size:1rem;
                color:var(--dark-txt);
            }
            .report-mama-image{
                width:10vw;
                height:10vw;
            }
            .report-row{
                flex-direction: column;
                justify-content:flex-start;
                gap:5rem;
            }
            .report-row-sub{
                justify-content: space-between;
            }
            .data-title{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
            }
            .data-value{
                font-family: 'Inter-Light';
                font-size:1rem;
                color:var(--light-txt);
            }
            .mom-bmi{
                font-family: 'Inter-Bold';
                border-radius:10rem;
            }
            .add-report-btn,#vaccine-search-btn{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                background-color:var(--light-txt);
                color:var(--bg);
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                transition:0.6s;
            }
            .add-report-btn:hover,#vaccine-search-btn:hover{
                background-color:var(--dark-txt);
                transition:0.6s;
            }
            .report-search-continer{
                gap:1rem;
            }
            #vaccine-name-search{
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
            #add-report-form{
                display:none;
            }
            #add-report-form,.add-hr-form-row{
                gap:1rem;
            }
            #add-report-form input,input,select{
                font-family: 'Inter-Light';
                font-size:1rem;
                color:var(--light-txt);
                outline:none;
                background-color:var(--bg);
                border:2px solid var(--light-txt);
                border-radius:10rem;
                width:33%;
                text-align: center;
                padding:0.5rem 0rem;
            }
            #add-report-form textarea{
                font-family: 'Inter-Light';
                font-size:1rem;
                color:var(--light-txt);
                outline:none;
                background-color:var(--bg);
                border:2px solid var(--light-txt);
                border-radius:10rem;
                width:33%;
                text-align: center;
            }
            .hr-frm-date{
                width:33%;
            }
            .basic-checks input,.basic-checks select{
                width:100%;
            }
            .hr-frm-date input{
                width:100% !important;
            }
            .add-health-record-btn{
                font-family: 'Inter-Bold' !important;
                font-size:1rem !important;
                background-color:var(--light-txt) !important;
                color:var(--bg) !important;
                border:0px !important;
                border-radius:10rem !important;
                padding:0.5rem 0rem !important;
                width:20% !important;
                transition:0.6s !important;
            }
            .add-health-record-btn:hover{
                background-color:var(--dark-txt) !important;
                transition:0.6s;
            }
            .frm-close-btn{
                font-family: 'Inter-Bold';
                font-size:1rem;
                background-color:var(--dark-txt);
                color:var(--bg);
                border:0px;
                border-radius:10rem;
                padding:0.5rem 0rem;
                width:20%;
                text-align: center;
                transition:0.6s;
            }
            .frm-close-btn:hover{
                cursor: pointer;
            }
            table{
                border: 0px !important;
            }
            th,td{
                background-color: var(--bg) !important;
            }
            th{
                color:var(--light-txt) !important;
                font-family: 'Inter-Bold';
                font-size:0.8rem;
            }
            td{
                color:var(--light-txt) !important;
                font-family: 'Inter-Light';
                font-size:0.8rem;
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
            .mom-list-btn-remove{
                background-color:#800000;
                color:var(--bg);
                font-family: 'Inter-Bold';
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                text-decoration: none;
                transition:0.6s;
            }
            .mom-list-btn-remove:hover{
                background-color:red;
                color:var(--bg);
                transition:0.6s;
            }
            .mom-list-btn:hover{
                background-color:var(--light-txt);
                color:var(--bg);
                transition:0.6s;
            }
            label{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
            }
            .app-status{
                
            }

            @media only screen and (min-width:768px){
                .report-row{
                    flex-direction: row;
                }
            }

            @media only screen and (min-width:1280px){
                .report-row{
                    justify-content:flex-start;
                    gap:10rem;
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
                <h2 class="main-header-title">TODAY APPOINTMENTS</h2>
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
            <div class="main-content d-flex flex-column">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="dd">NIC</th>
                            <th class="dd">Appointment status</th>
                            <th class="dd">Appointment date</th>
                            <th class="dd">Appointment time</th>
                        </tr>
                    </thead>
                    <?php
                    $today = date('Y-m-d');

                    $sql = "SELECT * FROM appointments WHERE app_date='$today'";
                    $result = mysqli_query($con,$sql);
                    if($result){
                        $num = mysqli_num_rows($result);
                        echo "There are $num appointments in today";
                        if($num > 0){
                            while($row = mysqli_fetch_assoc($result)){
                                if($row['appointment_status'] == 'Booked'){
                                    $mamaNIC = $row['NIC'];
                                    echo '
                                    <tbody>
                                        <tr class="vaccine-results">
                                            <td><a class="mom-list-btn d-flex flex-row justify-content-center" href="mw-health-details.php?id='.$row["NIC"].'"> '.$row["NIC"].' </a></td>
                                            <td><div class="app-status"><b>'.$row['appointment_status'].'</b></div></td>
                                            <td>'.$row['app_date'].'</td>
                                            <td>'.$row['app_time'].'</td>
                                            <td class="table-btn-container d-flex flex-row justify-content-center">
                                                <a class="mom-list-btn" href="appointment-confirm.php?id='.$row["appointment_id"].'">Confirm</a>
                                                <a class="mom-list-btn-remove" href="appointment-delete.php?id='.$row["appointment_id"].'">Remove</a>
                                            </td>
                                        </tr>
                                    </tbody>';
                                }
                                else{
                                    echo '
                                    <tbody>
                                        <tr class="vaccine-results">
                                            <td><a class="mom-list-btn d-flex flex-row justify-content-center" href="mw-health-details.php?id='.$row["NIC"].'"> '.$row["NIC"].' </a></td>
                                            <td><div class="app-status"><b>'.$row['appointment_status'].'</b></div></td>
                                            <td>'.$row['app_date'].'</td>
                                            <td>'.$row['app_time'].'</td>
                                        </tr>
                                    </tbody>';
                                }
                            }
                        }
                        else{
                            //echo '<h3>Currently, there are no appointments for today.</h3>';
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="main-footer d-flex flex-row justify-content-between">
                <a href="../pages/staff-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        

    </script>
</body>
</html>
