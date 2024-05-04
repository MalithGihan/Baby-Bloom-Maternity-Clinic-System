<?php
session_start();
include 'dbaccess.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

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
        $momPhone = $row['phoneNumber'];
        $rubStatus = $row['rubella_status'];
    }
}
//echo $momFname;

// Query to get doctors
$sqlD = "SELECT firstname,surname FROM staff WHERE position = 'Doctor'";
$resultD = $con->query($sqlD);

$doctors = [];
if ($resultD->num_rows > 0) {
  // output data of each row
  while($row = $resultD->fetch_assoc()) {
    $doctors[] = $row["firstname"] . " " . $row["surname"];
  }
} 

// Query to get midwives
$sqlM = "SELECT firstname,surname FROM staff WHERE position = 'Midwife'";
$resultM = $con->query($sqlM);

$midwives = [];
if ($resultM->num_rows > 0) {
  // output data of each row
  while($row = $resultM->fetch_assoc()) {
    $midwives[] = $row["firstname"] . " " . $row["surname"];
  }
}

//To get mom age
$momNDOB = new DateTime($momBday);
$todayDate = new DateTime('today');

$momAge = $momNDOB->diff($todayDate)->y;

//For rubella vaccination status
$rubNStatus = "Data not found";
switch($rubStatus){
    case "Yes":
        $rubNStatus = "Vaccinated";
        break;
    case "No":
        $rubNStatus = "Not vaccinated";
        break;
    default:
        $rubNStatus = "Data not found";
        break;
}

//Query to check the toxoide vaccination status
$sqlTox = "SELECT * FROM vaccination_report WHERE NIC='$NIC' AND vaccination='Toxoide'";
$toxStatus = "Data not found";

$resultTox = mysqli_query($con,$sqlTox);
if(mysqli_num_rows($resultTox) == 0){
    $toxStatus = "Not vaccinated";
}
else{
    $toxStatus = "Vaccinated";
}

//query to retrieve mother blood_group from basic_checkups
$momBloodGroup = "Not checked";

$bcSql = "SELECT * FROM basic_checkups WHERE NIC = '$NIC'";
$bcResult = mysqli_query($con,$bcSql);

if($bcResult){
    while($bcrow = mysqli_fetch_assoc($bcResult)){
        $momBloodGroup = $bcrow['blood_group'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Vaccination Details</title>
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
            .mom-rubella,.mom-toxoide{
                font-family: 'Inter-Bold';
                border-radius:10rem;
                text-align: center;
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
            #add-report-form,.add-vaccine-form-row{
                gap:1rem;
            }
            #add-report-form input, #add-report-form select{
                font-family: 'Inter-Light';
                font-size:1rem;
                color:var(--light-txt);
                outline:none;
                background-color:var(--bg);
                border:2px solid var(--light-txt);
                border-radius:10rem;
                width:50%;
                text-align: center;
                padding:0.5rem 0rem;
            }
            .add-vaccine-record-btn{
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
            .add-vaccine-record-btn:hover{
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
                <h2 class="main-header-title">MOTHER VACCINATION DETAILS</h2>
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
                <div class="report-row d-flex">
                    <p class="row-title">MOTHER BASIC DATA</p>
                </div>
                <div class="report-row d-flex">
                    <img src="../images/midwife-dashboard/mama-img-in-reports.png" alt="Mother image" class="report-mama-image">
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
                            <h3 class="data-title">Rubella vaccination status</h3>
                            <p class="data-value mom-rubella" id="rubella-status"><?php echo $rubNStatus; ?></p>
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
                            <h3 class="data-title">Toxoide vaccination status</h3>
                            <p class="data-value mom-toxoide" id="toxoide-status"><?php echo $toxStatus; ?></p>
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
                            <h3 class="data-title">Blood group</h3>
                            <p class="data-value"><?php echo $momBloodGroup; ?></p>
                        </div>
                    </div>
                </div>
                <?php
                if($momBloodGroup == "A-" || $momBloodGroup == "B-" || $momBloodGroup == "AB-" || $momBloodGroup == "O-") {
                    // If no rows are found in basic_checkups table, display the form
                ?>
                    <div class="report-row d-flex flex-column">
                        <p class="row-title">VACCINE RECOMMENDATION</p>
                        <div class="row-col d-flex flex-column">
                            <div class="data-row d-flex flex-column">
                                <p class="data-value">*<?php echo $momFname; ?> <?php echo $momSname; ?> has <?php echo $momBloodGroup; ?> blood type. So, the RhoGAM vaccine should be given after the first delivery.</p>
                            </div>
                        </div>
                    </div>
                    
                <?php
                }
                ?>
                
                <div class="report-row d-flex">
                    <p class="row-title">MOTHER VACCINATION DATA</p>
                </div>
                
                <?php
                if($_SESSION['staffPosition']!="Sister"){
                ?>
                    <div class="report-row d-flex">
                        <button class="add-report-btn" id="add-report-btn">Add new</button>
                        <!--
                        <form class="report-search-continer d-flex" method="POST">
                            <input type="text" id="vaccine-name-search" name="vaccine-name" placeholder="Search by vaccination name" required>
                            <input type="submit" name="submit" value="Search" id="vaccine-search-btn">
                        </form>
                        -->
                        </div>
                <?php
                }
                ?>
                <form action="vaccination-add.php" method="POST" class="report-row flex-column" id="add-report-form">
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <!-- <input type="text" id="vaccine-name" name="vaccine-name" placeholder="Enter vaccine name" required> -->
                        <select name="vaccine-name">
                                <option value="" id="vaccine-name" disabled selected>Enter vaccination name</option>\
                                <option value="Toxoide">Toxoide</option>
                        </select>
                        <input type="date" id="vaccine-date" name="vaccine-date" placeholder="Enter vaccinated date" required>
                    </div>
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <input type="text" id="vaccine-batch" name="vaccine-batch" placeholder="Enter vaccine batch number" required>
                        <input type="text" id="mama-nic" name="mama-nic" placeholder="Enter mother's NIC" value="<?php echo $NIC; ?>" required>
                    </div>
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <!-- <input type="text" id="vaccine-approved" name="vaccine-approved" placeholder="Who approved the vaccine?" required> -->
                        <select name="vaccine-approved">
                            <option value="" disabled selected>Who approved the vaccine?</option>
                            <?php foreach($doctors as $doctor): ?><!-- Automatically fetching all the doctors names to the list -->
                                <option value="<?= $doctor ?>">Dr. <?= $doctor ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- <input type="text" id="vaccine-doneby" name="vaccine-doneby" placeholder="Who done the vaccination?" required> -->
                        <select name="vaccine-doneby">
                            <option value="" disabled selected>Who done the vaccination?</option>
                            <?php foreach($midwives as $midwife): ?><!-- Automatically fetching all the midwives names to the list -->
                                <option value="<?= $midwife ?>">Ms. <?= $midwife ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <div class="frm-close-btn" id="frm-close-btn">Cancel</div>
                        <input type="submit" name="vcc-submit" value="Add" class="add-vaccine-record-btn"> 
                    </div>
                </form>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="dd">Vaccination name</th>
                            <th class="dd">Vaccinated date</th>
                            <th class="dd">Approved by</th>
                            <th class="dd">Vaccination done by</th>
                        </tr>
                    </thead>
                    <?php

                        $sql = "SELECT * FROM vaccination_report WHERE NIC = '$NIC'";
                            $result = mysqli_query($con,$sql);
                            if($result){
                                $num = mysqli_num_rows($result);
                                if($num > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo '
                                        <tbody>
                                            <tr class="vaccine-results">
                                                <td>'.$row['vaccination'].'</td>
                                                <td>'.$row['date'].' </td>
                                                <td>'.$row['approvedBy'].'</td>
                                                <td>'.$row['vaccinatedBy'].'</td>
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
                <a href="mw-health-details.php?id=<?php echo $NIC; ?>">
                    <button class="main-footer-btn">Health Report</button>
                </a>
                <a href="../pages/mw-mother-list.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        var addRecordBtn = document.getElementById("add-report-btn");
        var hideRecordBtn = document.getElementById("frm-close-btn");
        var recordForm = document.getElementById("add-report-form");

        var rubStatus = document.getElementById("rubella-status");

        //Changing colors of the Rubella vaccination status
        window.addEventListener('load', function() {
            switch(rubStatus.innerHTML){
                case "Vaccinated":
                    rubStatus.style.backgroundColor = "Green";
                    rubStatus.style.padding = "0.5rem 1rem";
                    rubStatus.style.color = "#EFEBEA";
                    break;
                case "Not vaccinated":
                    rubStatus.style.backgroundColor = "Red";
                    rubStatus.style.padding = "0.5rem 1rem";
                    rubStatus.style.color = "#EFEBEA";
                    break;
                default:
                    rubStatus.style.backgroundColor = "#EFEBEA";
                    rubStatus.style.padding = "0rem 0rem";
            }
        });

        var toxStatus = document.getElementById("toxoide-status");

        //Changing colors of the Toxoide vaccination status
        window.addEventListener('load', function() {
            switch(toxStatus.innerHTML){
                case "Vaccinated":
                    toxStatus.style.backgroundColor = "Green";
                    toxStatus.style.padding = "0.5rem 1rem";
                    toxStatus.style.color = "#EFEBEA";
                    break;
                case "Not vaccinated":
                    toxStatus.style.backgroundColor = "Red";
                    toxStatus.style.padding = "0.5rem 1rem";
                    toxStatus.style.color = "#EFEBEA";
                    break;
                default:
                    toxStatus.style.backgroundColor = "#EFEBEA";
                    toxStatus.style.padding = "0rem 0rem";
            }
        });
        
        addRecordBtn.addEventListener("click",function(){
            recordForm.style.display = "flex";
            console.log("GG");
        })
        hideRecordBtn.addEventListener("click",function(){
            recordForm.style.display = "none";
        })

    </script>
</body>
</html>
