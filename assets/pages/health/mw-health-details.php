<?php
// Use secure session initialization for protected pages
require_once __DIR__ . '/../shared/session-init.php';

include '../shared/db-access.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate required parameters
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Missing required patient ID.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$NIC = $_GET['id'];

// IDOR Protection: Verify patient exists and staff has permission to view
$sql = "SELECT * FROM pregnant_mother WHERE NIC = ?";
$stmt = $con->prepare($sql);
if ($stmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$stmt->bind_param("s", $NIC);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Patient doesn't exist
    error_log("Unauthorized patient health access attempt - NIC: $NIC, Staff: " . $_SESSION["staffEmail"]);
    $_SESSION['error_message'] = "Access denied. Patient not found.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$row = $result->fetch_assoc();
$momFname = $row['firstName'];
$momSname = $row['surname'];
$momAdd = $row['address'];
$momBday = $row['DOB'];
$momBPlace = $row["birthplace"];
$momPhone = $row['phoneNumber'];
$momHealthCond = $row['health_conditions'];
$momAllergies = $row['allergies'];
$momHusName = $row['husbandName'];
$momHusJob = $row['husbandOccupation'];
$momHusDOB = $row["husband_dob"];
$momHusPhone = $row["husband_phone"];
$momHusBPlace = $row["husband_birthplace"];
$momHusHealthCond = $row['husband_healthconditions'];

$stmt->close();
//echo $momFname;

//The following code responsible displaying the data from the health_report
$momBloodGroup = "Not checked";
$momHeight = 0;
$momWeight = 0;

//query to get the latest weight for a given NIC
$healthSql = "SELECT * FROM health_report WHERE NIC = ? ORDER BY HR_ID DESC LIMIT 1";
$healthStmt = $con->prepare($healthSql);
if ($healthStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
} else {
    $healthStmt->bind_param("s", $NIC);
    $healthStmt->execute();
    $healthResult = $healthStmt->get_result();

    if($healthResult){
        while($hrow = mysqli_fetch_assoc($healthResult)){
            $momWeight = $hrow['weight'];
        }
    }
    $healthStmt->close();
}

//query to retrieve height, blood_group, and hub_blood_group from basic_checkups
$bcSql = "SELECT * FROM basic_checkups WHERE NIC = ?";
$bcStmt = $con->prepare($bcSql);
if ($bcStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
} else {
    $bcStmt->bind_param("s", $NIC);
    $bcStmt->execute();
    $bcResult = $bcStmt->get_result();

if($bcResult){
    while($bcrow = mysqli_fetch_assoc($bcResult)){
        $momHeight = $bcrow['height'];
        $momBloodGroup = $bcrow['blood_group'];
        $momHubBGroup = $bcrow['hub_blood_group'];
    }
}
    $bcStmt->close();
}


if($momBloodGroup == NULL || $momHeight == NULL || $momWeight == NULL || $momHubBGroup == NULL){
    $momBMI = "Not measured";
    $momBMIStatus = "Not measured";
    $momHubBGroup = "Not checked";
}
else{
    $momBMI = "Not measured";
    $momHeightM = $momHeight / 100;
    //$momBMI = number_format((50 / (1.5 * 1.5)),1);
    $momBMI = number_format(($momWeight / ($momHeightM * $momHeightM)),1);

    $momBMIStatus = "Not measured";
    if($momBMI < 18.5){
        $momBMIStatus = "Underweight";
    }
    else if($momBMI >= 18.5 && $momBMI <= 24.9){
        $momBMIStatus = "Healthy";
    }
    else if($momBMI >= 25.0 && $momBMI <= 29.9){
        $momBMIStatus = "Overweight";
    }
    else{
        $momBMIStatus = "Obese";
    }
}


//To get mom age
$momNDOB = new DateTime($momBday);

//Getting today date without formatting
$todayDate = new DateTime('today');

$futureDate = clone $todayDate;

//Calculate a date from the today date
$futureDate->modify('+30 days');

//Formatting today date to Year-Month-Date format
$formattedTodayDate = $todayDate->format('Y-m-d');

//Formatting future date to  Year-Month-Date format
$formattedFutureDate = $futureDate->format('Y-m-d');

$momAge = $momNDOB->diff($todayDate)->y;

//To get husband age
$husbandNDOB = new DateTime($momHusDOB);

$momHusAge = $husbandNDOB->diff($todayDate)->y;

//-- Begining of the graph sqls --

$graphSQL = "SELECT date, heartRate, cholesterolLevel, weight FROM health_report WHERE NIC = ?";
$graphStmt = $con->prepare($graphSQL);

// Initialize arrays to store data
$dates = [];
$heartRateData = [];
$cholData = [];
$weightData = [];
$cholesterolData = [];

if ($graphStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
} else {
    $graphStmt->bind_param("s", $NIC);
    $graphStmt->execute();
    $graphResult = $graphStmt->get_result();

    // Fetch each row and store data in arrays
    while ($gRow = mysqli_fetch_assoc($graphResult)) {
        // Store dates in month and date format
        $dates[] = date('M d', strtotime($gRow['date']));
        $heartRateData[] = (int)$gRow['heartRate'];
        $cholesterolData[] = (int)$gRow['cholesterolLevel'];
        $weightData[] = (float)$gRow['weight'];
    }
    $graphStmt->close();
}

// Convert PHP arrays to JSON
$datesJson = json_encode($dates);
$heartRateDataJson = json_encode($heartRateData);
$cholesterolDataJson = json_encode($cholesterolData);
$weightDataJson = json_encode($weightData);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - HEALTH Details</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/highcharts.js"></script>
        <script src="../../js/script.js"></script>
        <script rel="script" src="../../js/jspdf.min.js"></script>
        <script rel="script" src="../../js/html2canvas.min.js"></script>
        <script src="../../js/health-details.js"></script>
    </head>
<body>
    <script type="application/json" id="chart-data">
    {
        "dates": <?php echo $datesJson; ?>,
        "heartRate": <?php echo $heartRateDataJson; ?>,
        "cholesterol": <?php echo $cholesterolDataJson; ?>,
        "weight": <?php echo $weightDataJson; ?>
    }
    </script>
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
                <h2 class="main-header-title">MOTHER HEALTH DETAILS</h2>
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
                <div class="report-row d-flex justify-content-between">
                        <div class="bb-n-btn" id="health-export-btn">Export ></div>
                    </div>
                <div class="d-flex flex-column capture-section" id="capture-section">
                    <div class="report-row d-flex justify-content-between">
                        <p class="row-title">MOTHER BASIC DATA</p>
                    </div>
                    <div class="report-row d-flex">
                        <img src="../../images/midwife-dashboard/mama-img-in-reports.png" alt="Mother image" class="report-mama-image">
                        <div class="d-flex flex-column health-full-width">
                            <div class="d-flex report-row-sub">
                                <div class="row-col d-flex flex-column">
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Full name</h3>
                                        <p class="data-value"><?php echo $momFname; ?> <?php echo $momSname; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Age</h3>
                                        <p class="data-value"><?php echo $momAge; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Birthplace</h3>
                                        <p class="data-value"><?php echo $momBPlace; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Blood group</h3>
                                        <p class="data-value"><?php echo $momBloodGroup; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Special health conditions</h3>
                                        <p class="data-value"><?php echo $momHealthCond; ?></p>
                                    </div>
                                </div>

                                <div class="row-col d-flex flex-column">
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">NIC number</h3>
                                        <p class="data-value"><?php echo $NIC; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Address</h3>
                                        <p class="data-value"><?php echo $momAdd; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Height</h3>
                                        <p class="data-value"><?php echo $momHeight; ?>cm</p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Weight</h3>
                                        <p class="data-value"><?php echo $momWeight; ?>Kg</p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Known allergies</h3>
                                        <p class="data-value"><?php echo $momAllergies; ?></p>
                                    </div>
                                </div>

                                <div class="row-col d-flex flex-column">
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Birthdate</h3>
                                        <p class="data-value"><?php echo $momBday; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">Phone number</h3>
                                        <p class="data-value">0<?php echo $momPhone; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">BMI</h3>
                                        <p class="data-value"><?php echo $momBMI; ?></p>
                                    </div>
                                    <div class="data-row d-flex flex-column">
                                        <h3 class="data-title">BMI status</h3>
                                        <p class="data-value mom-bmi" id="mom-bmi-status"><?php echo $momBMIStatus; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="report-row d-flex">
                        <p class="row-title">HUSBAND DATA</p>
                    </div>
                    <div class="report-row d-flex">
                        <div class="row-col d-flex flex-column">
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's name</h3>
                                <p class="data-value"><?php echo $momHusName; ?></p>
                            </div>
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's occupation</h3>
                                <p class="data-value"><?php echo $momHusJob; ?></p>
                            </div>
                        </div>
                        <div class="row-col d-flex flex-column">
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's birthdate</h3>
                                <p class="data-value"><?php echo $momHusDOB; ?></p>
                            </div>
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's age</h3>
                                <p class="data-value"><?php echo $momHusAge; ?></p>
                            </div>
                        </div>
                        <div class="row-col d-flex flex-column">
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's phone number</h3>
                                <p class="data-value">0<?php echo $momHusPhone; ?></p>
                            </div>
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's birthplace</h3>
                                <p class="data-value"><?php echo $momHusBPlace; ?></p>
                            </div>
                        </div>
                        <div class="row-col d-flex flex-column">
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's blood group</h3>
                                <p class="data-value"><?php echo $momHubBGroup; ?></p>
                            </div>
                            <div class="data-row d-flex flex-column">
                                <h3 class="data-title">Husband's known health conditions</h3>
                                <p class="data-value"><?php echo $momHusHealthCond; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    // query to get data row from basic_checkups table
                    $checkupsSql = "SELECT * FROM basic_checkups WHERE NIC = ?";
                    $checkupStmt = $con->prepare($checkupsSql);
                    $checkupStmt->bind_param("s", $NIC);
                    $checkupStmt->execute();
                    $checkupResult = $checkupStmt->get_result();

                    if(mysqli_num_rows($checkupResult) == 0) {
                        // If no rows are found in basic_checkups table, display the form
                    ?>
                        <div class="report-row d-flex">
                            <p class="row-title">ADD MOTHER AND HUSBAND BASIC DATA</p>
                        </div>
                        <form action="handlers/basic-data-add-handler.php" method="POST" class="basic-checks d-flex flex-column health-form-container">
                            <div class="d-flex flex-row health-form-row">
                                <input type="text" id="mama-nic" name="mama-nic" placeholder="Mother's NIC" value="<?php echo "$NIC" ?>" hidden required>
                                <input type="number" name="mama-height" step="0.1" placeholder="Enter Mother's height in cm. Ex: 170.5cm" required>
                                <select name="mama-blood-group" required>
                                    <option value="" disabled selected>Mother blood group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                                <select name="hub-blood-group" required>
                                    <option value="" disabled selected>Husband blood group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="d-flex flex-row health-form-submit-row">
                                <input type="submit" value="Save" class="bb-a-btn">
                            </div>
                        </form>
                    <?php
                    }
                    ?>
                    <div class="report-row d-flex">
                        <p class="row-title">MOTHER WEIGHT CHART</p>
                    </div>
                    <div class="report-row d-flex">
                        <div id="weightChart" class="health-chart"></div>
                    </div>
                    <div class="report-row d-flex">
                        <p class="row-title">MOTHER HEART RATE CHART</p>
                    </div>
                    <div class="report-row d-flex">
                        <div id="heartRateChart" class="health-chart"></div>
                    </div>
                    <div class="report-row d-flex">
                        <p class="row-title">MOTHER BLOOD CHOLESTEROL CHART</p>
                    </div>
                    <div class="report-row d-flex">
                        <div id="cholChart" class="health-chart"></div>
                    </div>
                    <div class="report-row d-flex">
                        <p class="row-title">MOTHER HEALTH REPORTS</p>
                    </div>
                </div>
                <?php
                if($_SESSION['staffPosition']!="Sister"){
                ?>
                    <div class="report-row d-flex">
                        <button class="add-report-btn" id="add-report-btn">Add new</button>
                    </div>
                <?php
                }
                ?>
                <form action="handlers/health-add-handler.php" method="POST" class="report-row flex-column" id="add-report-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="add-hr-form-row d-flex flex-row justify-content-between">
                        <input type="text" id="mama-nic" name="mama-nic" placeholder="Mother's NIC" value="<?php echo htmlspecialchars($NIC, ENT_QUOTES, 'UTF-8'); ?>" hidden required>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="hr-date">Today date</label>
                            <input type="date" id="hr-date" name="hr-date" placeholder="Today date" value="<?php echo "$formattedTodayDate"?>" required>
                        </div>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="hr-appx-date">Next appointment approximate date</label>
                            <input type="date" id="hr-appx-date" name="hr-appx-date" placeholder="Next appointment approximate date" value="<?php echo "$formattedFutureDate"?>" required>
                        </div>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <input type="number" id="hr-heart-rate" name="hr-heart-rate" placeholder="Mother heart rate" required>
                        <input type="text" id="hr-blood-pss" name="hr-blood-pss" placeholder="Mother blood pressure" required>
                        <input type="number" id="hr-chol-lvl" name="hr-chol-lvl" placeholder="Mother cholesterol level" required>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <textarea id="hr-heart-conclusion" name="hr-heart-conclusion" placeholder="Mother heart rate conclusion" maxlength="1000"></textarea>
                        <textarea id="hr-blood-pss-conclusion" name="hr-blood-pss-conclusion" placeholder="Mother blood pressure conclusion" maxlength="1000"></textarea>
                        <input type="number" id="hr-weight" name="hr-weight" placeholder="Mother weight in Kg" required>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <textarea id="hr-baby-movement" name="hr-baby-movement" placeholder="Baby movement conclusion" maxlength="500"></textarea>
                        <input type="number" id="hr-baby-heart-rate" name="hr-baby-heart-rate" placeholder="Baby heart rate" required>
                        <textarea id="hr-scan-conclusion" name="hr-scan-conclusion" placeholder="Scan conclusion" maxlength="1000"></textarea>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <textarea id="hr-abnormalities" name="hr-abnormalities" placeholder="Any abnormalities?" maxlength="1000"></textarea>
                        <textarea id="hr-spec-instructions" name="hr-spec-instructions" placeholder="Special instructions" maxlength="1000"></textarea>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <div class="frm-close-btn" id="frm-close-btn">Cancel</div>
                        <input type="submit" name="hr-submit" value="Add" class="add-health-record-btn"> 
                    </div>
                </form>
                <table class="table" id="mama-health-records">
                    <thead>
                        <tr>
                            <th class="dd">Report date</th>
                        </tr>
                    </thead>
                    <?php

                        $sql = "SELECT * FROM health_report WHERE NIC = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("s", $NIC);
                        $stmt->execute();
                        $result = $stmt->get_result();
                            if($result){
                                $num = mysqli_num_rows($result);
                                echo "$num results found.";
                                if($num > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo '
                                        <tbody>
                                            <tr class="vaccine-results">
                                                <td>'.$row['date'].'</td>
                                                <td class="table-btn-container d-flex flex-row justify-content-center">
                                                    <a class="mom-list-btn" href="mw-view-health-reports.php?id='.$row["HR_ID"].'&NIC='.$NIC.'">View</a>
                                                    <a class="mom-list-btn-remove" href="delete-health-reports.php?id='.$row["HR_ID"].'&NIC='.$NIC.'">Remove</a>
                                                </td>
                                            </tr>
                                        </tbody>';
                                    }
                                }
                                else{
                                    echo '<h3>Data not found</h3>';
                                }
                            }
/*
                        if(isset($_POST['submit'])){
                            $search = $_POST['vaccine-name'];

                            
                        } */
                    ?>
                </table>
            </div>
            <div class="main-footer d-flex flex-row justify-content-between">
                <a href="../staff/mw-mother-list.php">
                    <button class="main-footer-btn">Return</button>
                </a>
                <a href="mw-vaccination-details.php?id=<?php echo $NIC; ?>">
                    <button class="main-footer-btn">Vaccination Report</button>
                </a>
            </div>
        </main>
    </div>

</body>
</html>
