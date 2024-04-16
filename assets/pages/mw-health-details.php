<?php 
session_start();

include 'dbaccess.php';

$NIC = $_GET['id'];

echo $NIC;

$sql = "SELECT * FROM pregnant_mother WHERE NIC='$NIC'";

$result = mysqli_query($con,$sql);
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $momFname = $row['firstName'];
        $momSname = $row['surname'];
        $momAdd = $row['address'];
        $momBday = $row['DOB'];
        $momBPlace = $row["birthplace"];
        $momPhone = $row['phoneNumber'];
        $momHusName = $row['husbandName'];
        $momHusJob = $row['husbandOccupation'];
        $momHusDOB = $row["husband_dob"];
        $momHusPhone = $row["husband_phone"];
        $momHusBPlace = $row["husband_birthplace"];
    }
}
//echo $momFname;

//The following code responsible displaying the data from the health_report
$momBloodGroup = "Not checked";
$momHeight = 0;
$momWeight = 0;

//query to get the latest weight for a given NIC
$healthSql = "SELECT * FROM health_report WHERE NIC = '$NIC' ORDER BY HR_ID DESC LIMIT 1";
$healthResult = mysqli_query($con,$healthSql);

if($healthResult){
    while($hrow = mysqli_fetch_assoc($healthResult)){
        $momWeight = $hrow['weight'];
    }
}

//query to retrieve height, blood_group, and hub_blood_group from basic_checkups
$bcSql = "SELECT * FROM basic_checkups WHERE NIC = '$NIC'";
$bcResult = mysqli_query($con,$bcSql);

if($bcResult){
    while($bcrow = mysqli_fetch_assoc($bcResult)){
        $momHeight = $bcrow['height'];
        $momBloodGroup = $bcrow['blood_group'];
        $momHubBGroup = $bcrow['hub_blood_group'];
    }
}


if($momBloodGroup == NULL || $momHeight == NULL || $momWeight == NULL){
    $momBMI = "Not measured";
    $momBMIStatus = "Not measured";
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
$todayDate = new DateTime('today');

$momAge = $momNDOB->diff($todayDate)->y;

//To get husband age
$husbandNDOB = new DateTime($momHusDOB);

$momHusAge = $husbandNDOB->diff($todayDate)->y;


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
                    gap:15rem;
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
                <h2 class="main-header-title">MOTHER HEALTH DETAILS</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../images/midwife-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username">Jenny Doe</div>
                            <div class="useremail">jennydoe@gmail.com</div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="##">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex flex-column">
                <div class="report-row d-flex">
                    <p class="row-title">MOTHER BASIC DATA</p>
                </div>
                <div class="report-row d-flex">
                    <img src="../images/midwife-dashboard/mama-img-in-reports.png" alt="Mother image" class="report-mama-image">
                    <div class="d-flex flex-column" style="width:100%;">
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
                </div>
                
                <?php
                // query to get data row from basic_checkups table
                $checkupsSql = "SELECT * FROM basic_checkups WHERE NIC='$NIC'";
                $checkupResult = mysqli_query($con, $checkupsSql);

                if(mysqli_num_rows($checkupResult) == 0) {
                    // If no rows are found in basic_checkups table, display the form
                ?>
                    <div class="report-row d-flex">
                        <p class="row-title">ADD MOTHER AND HUSBAND BASIC DATA</p>
                    </div>
                    <form action="basic-data-add.php" method="POST" class="basic-checks d-flex flex-column" style="justify-content:flex-start;gap:2rem;">
                        <div class="d-flex flex-row" style="width:100% !important; gap:1rem;">
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
                        <div class="d-flex flex-row" style="width:25%;">
                            <input type="submit" value="Save" class="bb-a-btn">
                        </div>
                    </form>
                <?php
                }
                ?>
                <div class="report-row d-flex">
                    <p class="row-title">MOTHER HEALTH DATA</p>
                </div>
                <div class="report-row d-flex">
                    <button class="add-report-btn" id="add-report-btn">Add new</button>
                    
                </div>
                <form action="health-add.php" method="POST" class="report-row flex-column" id="add-report-form">
                    <div class="add-hr-form-row d-flex flex-row">
                        <input type="text" id="mama-nic" name="mama-nic" placeholder="Mother's NIC" value="<?php echo "$NIC" ?>" required>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="hr-date">Today date</label>
                            <input type="date" id="hr-date" name="hr-date" placeholder="Today date" required>
                        </div>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="hr-appx-date">Next appointment approximate date</label>
                            <input type="date" id="hr-appx-date" name="hr-appx-date" placeholder="Next appointment approximate date" required>
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
                        <input type="text" id="blood-grp" name="blood-grp" placeholder="Mother blood group" required>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <input type="number" id="hr-height" name="hr-height" placeholder="Mother height in cm" required>
                        <input type="number" id="hr-weight" name="hr-weight" placeholder="Mother weight in Kg" required>
                        <input type="text" id="hr-allergy" name="hr-allergy" placeholder="Mother allergies" required>
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
                <table class="table">
                    <?php

                        $sql = "SELECT * FROM health_report WHERE NIC = '$NIC'";
                            $result = mysqli_query($con,$sql);
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
                                                    <a class="mom-list-btn" href="mw-view-health-reports.php?id='.$row["HR_ID"].'">View</a>
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
                <a href="../pages/mw-mother-list.php">
                    <button class="main-footer-btn">Return</button>
                </a>
                <a href="../pages/midwife-dashboard.php">
                    <button class="main-footer-btn">Dashboard</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        var addRecordBtn = document.getElementById("add-report-btn");
        var hideRecordBtn = document.getElementById("frm-close-btn");
        var recordForm = document.getElementById("add-report-form");

        addRecordBtn.addEventListener("click",function(){
            recordForm.style.display = "flex";
            console.log("GG");
        })
        hideRecordBtn.addEventListener("click",function(){
            recordForm.style.display = "none";
        })

        var BMIStatus = document.getElementById("mom-bmi-status");
        console.log(BMIStatus.innerHTML);

        //Changing colors of the BMI status
        window.onload = function() {
            switch(BMIStatus.innerHTML){
                case "Underweight":
                    BMIStatus.style.backgroundColor = "Orange";
                    BMIStatus.style.padding = "0.5rem 1rem";
                    BMIStatus.style.color = "#EFEBEA";
                    break;
                case "healthy":
                    BMIStatus.style.backgroundColor = "Green";
                    BMIStatus.style.padding = "0.5rem 1rem";
                    BMIStatus.style.color = "#EFEBEA";
                    break;
                case "Overweight":
                    BMIStatus.style.backgroundColor = "Orange";
                    BMIStatus.style.padding = "0.5rem 1rem";
                    BMIStatus.style.color = "#EFEBEA";
                    break;
                case "Obese":
                    BMIStatus.style.backgroundColor = "Red";
                    BMIStatus.style.padding = "0.5rem 1rem";
                    BMIStatus.style.color = "#EFEBEA";
                    break;
                default:
                    BMIStatus.style.backgroundColor = "#EFEBEA";
                    BMIStatus.style.padding = "0rem 0rem";
            }
        };

        

    </script>
</body>
</html>
