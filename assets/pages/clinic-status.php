<?php
session_start();

if ($_SESSION["staffPosition"] != "Sister" ) {
    header("Location: staff-dashboard.php"); // Redirect to staff dashboard if the logged in user isn't a Sister
    exit();
}

include 'dbaccess.php';

//Staff distribution cahrt php code

$staffSQL = "SELECT position, COUNT(*) AS count FROM staff GROUP BY position";
$staffResult = mysqli_query($con, $staffSQL);

$chartData = array();

while ($staffRow = mysqli_fetch_assoc($staffResult)) {
    $position = $staffRow['position'];
    $count = (int)$staffRow['count'];

    //$chartData[] = array('name' => $position, 'y' => $count);
    $chartData[] = array('name' => $position . ' (' . $count . ')', 'y' => $count);
}
//---------------------------------------


//Moms rubella chart php code

$momRSQL = "SELECT rubella_status, COUNT(*) AS count FROM pregnant_mother GROUP BY rubella_status";
$momRResult = mysqli_query($con, $momRSQL);

$rubellachartData = array();

while ($rubellarow = mysqli_fetch_assoc($momRResult)) {
    $rubellaStatus = ($rubellarow['rubella_status'] == 'Yes') ? 'Vaccinated' : 'Not Vaccinated';
    $rubellacount = (int)$rubellarow['count'];

    // Add data to the Highcharts formatted array
    $rubellachartData[] = array('name' => $rubellaStatus . ' (' . $rubellacount . ')', 'y' => $rubellacount);
}
//---------------------------------------


//Moms toxoide status chart php code

// Get the count of all pregnant mothers
$sqlPregnant = "SELECT COUNT(*) AS total FROM pregnant_mother";
$resultPregnant = mysqli_query($con, $sqlPregnant);
$rowPregnant = mysqli_fetch_assoc($resultPregnant);
$totalPregnant = (int)$rowPregnant['total'];

// Get the count of Toxoide vaccinated mothers
$sqlToxoide = "SELECT COUNT(*) AS toxoide_count FROM vaccination_report WHERE vaccination = 'Toxoide'";
$resultToxoide = mysqli_query($con, $sqlToxoide);
$rowToxoide = mysqli_fetch_assoc($resultToxoide);
$toxoideCount = (int)$rowToxoide['toxoide_count'];

// Calculate the count of non-Toxoide vaccinated mothers
$nonToxoideCount = $totalPregnant - $toxoideCount;

$toxchartData = array(
    array('name' => 'Toxoide Vaccinated' . ' (' . $toxoideCount . ')', 'y' => $toxoideCount),
    array('name' => 'Non-Toxoide Vaccinated' . ' (' . $nonToxoideCount . ')', 'y' => $nonToxoideCount)
);
//---------------------------------------


//Moms blood groups chart php code

$bgSQL = "SELECT blood_group, COUNT(*) AS count FROM basic_checkups WHERE blood_group IS NOT NULL GROUP BY blood_group";
$bgResult = mysqli_query($con, $bgSQL);

$bgchartData = array();

while ($bgRow = mysqli_fetch_assoc($bgResult)) {
    $bloodGroup = $bgRow['blood_group'];
    $bgcount = (int)$bgRow['count'];

    $bgchartData[] = array('name' => $bloodGroup . ' (' . $bgcount . ')', 'y' => $bgcount);
}
//---------------------------------------

mysqli_close($con);

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
        <script rel="script" type="text/js" src="../js/script.js"></script>
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
            .status-sub-title{
                font-family: 'Inter-Bold';
                font-size:1rem;
                color:var(--light-txt);
            }
            .stat-charts{
                margin:1rem 0rem;
            }
            .clinic-export-btns{
                width:25%;
                align-self: center;
                margin:1rem 0rem;
            }
            .stat-logo{
                width:4rem;
            }
            .moms-stat-row{
                margin:2rem 0rem;
            }

            @media only screen and (min-width:768px){
                .status-data-container{
                    border: 2px solid var(--light-txt);
                    border-radius: 2rem;
                    padding: 2rem;
                }
                .moms-stat-row{
                    flex-direction: row;
                    justify-content: space-between;
                }
                .moms-chart{
                    flex:50%;
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
                        <!-- The below div will export as a pdf when clicking the export btn -->
                        <div class="" id="staff-stats-capture">
                            <div class="d-flex flex-row justify-content-between align-items-center">
                                <h3 class="status-title">Staff Distribution</h3>
                                <img src="../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo stat-logo">
                            </div>
                            <div class="stat-charts" id="staff-chart-container" style="width: 100%; height: 50vh;"></div>
                        </div>
                        <hr>
                        <button class="bb-a-btn clinic-export-btns" id="staff-report-btn">Export ></button>
                    </div>
                    <div class="mother-status-container d-flex flex-column" id="mother-container">
                        <div class="" id="mom-stats-capture">
                            <div class="d-flex flex-row justify-content-between align-items-center">
                                <h3 class="status-title">Registered Mothers' Statistics</h3>
                                <img src="../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo stat-logo">
                            </div>
                            <div class="d-flex flex-column">
                                <div class="d-flex moms-stat-row">
                                    <div class="moms-chart">
                                        <h4 class="status-sub-title">Moms Blood Groups Distribution</h4>
                                        <div class="stat-charts" id="mom-bg-chart-container" style="width: 100%; height: 50vh;"></div>
                                    </div>

                                    <div class="moms-chart">
                                        <h4 class="status-sub-title">Toxoid Vaccination Status</h4>
                                        <div class="stat-charts" id="mom-toxoid-chart-container" style="width: 100%; height: 50vh;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="d-flex moms-stat-row">
                                    <div class="moms-chart">
                                        <h4 class="status-sub-title">Rubella Vaccination Status</h4>
                                        <div class="stat-charts" id="mom-rubella-chart-container" style="width: 100%; height: 50vh;"></div>
                                    </div>

                                    <div class="moms-chart">
                                        <h4 class="status-sub-title">RhoGAM Vaccination Status</h4>
                                        <div class="stat-charts" id="mom-rgm-chart-container" style="width: 100%; height: 50vh;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button class="bb-a-btn clinic-export-btns" id="staff-report-btn">Export ></button>
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
        //These codes are responsible for appear/dissappear the clinic status containers when clicking btns.
        var stfBtn = document.getElementById("staff-btn");
        var momBtn = document.getElementById("mothers-btn");

        var stfContainer = document.getElementById("staff-container");
        var momContainer = document.getElementById("mother-container");



        Highcharts.chart('staff-chart-container', {
            chart: {
                type: 'pie',
                backgroundColor: '#EFEBEA'
            },
            title: {
                text: ''
            },
            series: [{
                name: 'Positions',
                data: <?php echo json_encode($chartData); ?>
            }],
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            }
        });

        Highcharts.chart('mom-rubella-chart-container', {
            chart: {
                type: 'pie',
                backgroundColor: '#EFEBEA'
            },
            title: {
                text: ''
            },
            series: [{
                name: 'Vaccination Status',
                data: <?php echo json_encode($rubellachartData); ?>
            }],
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            }
        });

        Highcharts.chart('mom-toxoid-chart-container', {
            chart: {
                type: 'pie',
                backgroundColor: '#EFEBEA'
            },
            title: {
                text: ''
            },
            series: [{
                name: 'Vaccination Status',
                data: <?php echo json_encode($toxchartData); ?>
            }],
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            }
        });

        Highcharts.chart('mom-bg-chart-container', {
            chart: {
                type: 'pie',
                backgroundColor: '#EFEBEA'
            },
            title: {
                text: ''
            },
            series: [{
                name: 'Blood Groups',
                data: <?php echo json_encode($bgchartData); ?>
            }],
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            }
        });

        //These codes responsible for exporting the reports
        var staffDisBtn = document.getElementById("staff-report-btn");

    </script>
</body>
</html>
