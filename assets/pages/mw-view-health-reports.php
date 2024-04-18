<?php 
session_start();

include 'dbaccess.php';

$NIC = $_GET['NIC'];
$HR_ID = $_GET['id'];

echo $NIC;
echo"<br>";
echo $HR_ID;

$sql = "SELECT * FROM health_report WHERE HR_ID='$HR_ID'";

$result = mysqli_query($con,$sql);
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $hrDate = $row['date'];
        $hrHeartRate = $row['heartRate'];
        $hrBPressure = $row['bloodPressure'];
        $hrChLevel = $row['cholesterolLevel'];
        $hrWeight = $row['weight'];
        $hrHRConclusion = $row['heartRateConclusion'];
        $hrBPConclusion = $row['bloodPressureConclusion'];
        $hrBMove = $row['babyMovement'];
        $hrBHRate = $row['babyHeartbeat'];
        $hrScConclusion = $row['scanConclusion'];
        $hrAbnorms = $row['abnormalities'];
        $hrSpIns = $row['special_Instruction'];
        $hrNxtDate = $row['appx_Next_date'];
    }
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - HEALTH Details</title>
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
                <h2 class="main-header-title">MOTHER HEALTH REPORT</h2>
                
            </div>
            <div class="main-content d-flex flex-column">
                <div class="report-row d-flex flex-row justify-content-start">
                    <p class="row-title">REPORT DATE :</p>
                    <p class="row-title"><?php echo $hrDate; ?></p>
                    <p class="row-title">NEXT APPOINTMENT DATE :</p>
                    <p class="row-title"><?php echo $hrNxtDate; ?></p>
                </div>
                <div class="report-row d-flex">
                    <!-- <img src="../images/midwife-dashboard/mama-img-in-reports.png" alt="Mother image" class="report-mama-image"> -->
                    <div class="d-flex flex-column" style="width:100%;">
                        <div class="d-flex report-row-sub">
                            <div class="row-col d-flex flex-column">
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Heart Rate</h3>
                                    <p class="data-value"><?php echo $hrHeartRate; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Blood Pressure</h3>
                                    <p class="data-value"><?php echo $hrBPressure; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Cholesterol Level</h3>
                                    <p class="data-value"><?php echo $hrChLevel; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Weight</h3>
                                    <p class="data-value"><?php echo $hrWeight; ?></p>
                                </div>
                            </div>

                            <div class="row-col d-flex flex-column">
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Heart Rate Conclusion</h3>
                                    <p class="data-value"><?php echo $hrHRConclusion; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Blood Pressure Conclusion</h3>
                                    <p class="data-value"><?php echo $hrBPConclusion; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Baby Heart Rate</h3>
                                    <p class="data-value"><?php echo $hrBHRate; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Baby Movement</h3>
                                    <p class="data-value"><?php echo $hrBMove; ?></p>
                                </div>
                            </div>

                            <div class="row-col d-flex flex-column">
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Scan Conclusion</h3>
                                    <p class="data-value"><?php echo $hrScConclusion; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Abnormalities</h3>
                                    <p class="data-value"><?php echo $hrAbnorms; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Special Instructions</h3>
                                    <p class="data-value"><?php echo $hrSpIns; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-footer d-flex flex-row justify-content-between">
                <a href="mw-health-details.php?id=<?php echo $NIC; ?>">
                    <button class="main-footer-btn">Return</button>
                </a>
                <a href="../pages/staff-dashboard.php">
                    <button class="main-footer-btn">Dashboard</button>
                </a>
            </div>
        </main>
    </div>

    <script>

    </script>
</body>
</html>
