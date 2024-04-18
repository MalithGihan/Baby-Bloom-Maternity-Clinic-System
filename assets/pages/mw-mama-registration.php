<?php
session_start();

include 'dbaccess.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Getting the entered email and password

    $mamaFname = $_POST["mom-first-name"];
    $mamaMname  = $_POST["mom-mid-name"];
    $mamaSname  = $_POST["mom-last-name"];
    $mamaBday  = $_POST["mom-bday"];
    $mamaBplace = $_POST["mom-birthplace"];
    $mamaLRMP  = $_POST["mom-lrmp"];
    $mamaAdd  = $_POST["mom-address"];
    $mamaNIC  = $_POST["mom-nic"];//This is the primary key
    $mamaPhone  = $_POST["mom-phone"];
    $mamaHealthCond = $_POST["mom-health-conditions"];
    $mamaAllergies = $_POST["mom-allergies"];
    $mamaRubellaStat = $_POST["rbl-status"];
    $mamaMstate  = $_POST["marital-status"];
    $mamaRelativity = $_POST["blood-relativity"];
    $mamaHubname  = $_POST["mom-hub-name"];
    $mamaHubocc  = $_POST["mom-hub-job"];
    $mamaHubPhone = $_POST["mom-hub-phone"];
    $mamaHubDOB = $_POST["mom-hub-bday"];
    $mamaHubBirthplace = $_POST["mom-hub-birthplace"];
    $mamaHubHealthCond = $_POST["mama-hub-health-conditions"];
    $mamaHubAllergies = $_POST["mama-hub-allergies"];
    $mamaEmail  = $_POST["mom-email"];
    $mamaPss  = $_POST["mom-pwd"];
    $mamaRepss  = $_POST["mom-repwd"];

    $quota = 1;

    $presql = "SELECT * FROM pregnant_mother WHERE email = ?";
    $preStmt = $con->prepare($presql);

    if ($preStmt === false) {
        die('prepare() failed: ' . htmlspecialchars($con->error));
    }

    $preStmt->bind_param("s",$mamaEmail);
    $preStmt->execute();
    $preStmt->store_result(); 

    // Check if a user with the provided email exists
    if ($preStmt->num_rows === 1) {
        echo '<script>';
        echo 'alert("User already registered. Please login using your provided email!");';
        echo 'window.location.href="mama-login.php";';
        echo '</script>';
    }
    else{
        if($mamaPss==$mamaRepss){
            //echo "pss matched";
            //echo $_SESSION["mamaFname"];
    
            $sql = "INSERT INTO pregnant_mother (NIC, firstName, middleName, surname, DOB, birthplace, LRMP, address, phoneNumber, health_conditions, allergies, rubella_status, maritalStatus, blood_relativity, husbandName, husbandOccupation, husband_phone, husband_dob, husband_birthplace, husband_healthconditions, husband_allergies, email, password) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssssssssisssssssissssss",$mamaNIC,$mamaFname,$mamaMname,$mamaSname,$mamaBday,$mamaBplace,$mamaLRMP,$mamaAdd,$mamaPhone,$mamaHealthCond,$mamaAllergies,$mamaRubellaStat,$mamaMstate,$mamaRelativity,$mamaHubname,$mamaHubocc,$mamaHubPhone,$mamaHubDOB,$mamaHubBirthplace,$mamaHubHealthCond,$mamaHubAllergies,$mamaEmail,password_hash($mamaPss, PASSWORD_ARGON2ID));
            $stmt->execute();
    
            $sql2 = "INSERT INTO supplement_quota (orderedTimes,NIC) VALUES (?,?)";
            $stmt2 = $con->prepare($sql2);
            $stmt2->bind_param("is",$quota,$mamaNIC);
            $stmt2->execute();
        }
        else{
            echo '<script>';
            echo 'alert("Passwords are not matching!");';
            echo 'window.location.href="mama-registration.php";';
            echo '</script>';
        }
    }

echo '<script>';
echo 'alert("Registration success!");';
echo 'window.location.href="mama-login.php";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$preStmt->close();
$stmt->close();
$stmt2->close();
$con->close();
	
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Registration</title>
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
            .frm-row{
                justify-content: space-between;
            }
            .frm-col{
                width:50%;
            }
            input,select,textarea{
                outline:0px;
                border:2px solid var(--light-txt);
                border-radius:10rem;
                background-color:var(--bg);
                text-align:center;
                width:30%;
                margin:1rem 0rem;
                padding:0.5rem 0rem;
            }
            textarea{
                resize: none;
                height:8rem;
                width:40%;
                border-radius:3rem !important;
            }
            .frm-dt-row{
                justify-content: space-between !important;
            }
            .frm-dt-l-col{
                align-items: flex-start;
            }
            .frm-dt-r-col{
                align-items:flex-end;
            }
            #birthday,#lrmp{
                margin-top:0px !important;
                width:70%
            }
            .reg-btn{
                background-color:var(--light-txt);
                color:var(--bg);
                font-family: 'Inter-Bold';
                align-self:flex-end;
                transition:0.6s;
            }
            .reg-btn:hover{
                background-color:var(--dark-txt);
                border:2px solid var(--dark-txt);
                color:var(--bg);
                transition:0.6s;
            }
            .frm-section-title{
                font-family: 'Inter-Bold';
                font-size:1.5rem;
                color:var(--light-txt);
            }
            label{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
            }
            .frm-footer-btn-row{
                align-items: center;
            }
            .frm-footer-btn-row a{
                text-decoration: none;
            }
            .mama-hub-data-row{
                display:none;
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
                <h2 class="main-header-title">MOTHER REGISTRATION</h2>
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
            <form method="POST" class="d-flex flex-column">
                <div class="frm-section">
                    <p class="frm-section-title">Mother Basic Details</p>
                    <div class="frm-row d-flex">
                        <input type="text" id="fname" name="mom-first-name" placeholder="First name" required>
                        <input type="text" id="mname" name="mom-mid-name" placeholder="Middle name">
                        <input type="text" id="lname" name="mom-last-name" placeholder="Last name" required>
                    </div>
                    <div class="frm-row frm-dt-row d-flex">
                        <div class="frm-col frm-dt-l-col d-flex flex-column">
                            <label for="birthday">Birthdate</label>
                            <input type="date" id="birthday" name="mom-bday" placeholder="Birthday" required>
                        </div>
                        <input type="text" id="mom-birthplace" name="mom-birthplace" placeholder="Mother's birthplace">
                        <div class="frm-col frm-dt-r-col d-flex flex-column">
                            <label for="birthday" class="lrmp-label">Last Regualar Menstrual Period</label>
                            <input type="date" id="lrmp" name="mom-lrmp" placeholder="Last Regualar Menstrual Period" required>
                        </div>
                    </div>
                    <div class="frm-row d-flex">
                        <input type="text" id="address" name="mom-address" placeholder="Home address" required>
                        <input type="text" id="nic" name="mom-nic" placeholder="N I C" required>
                        <input type="tel" id="phone" name="mom-phone" pattern="[0-9]{10}" placeholder="Enter phone number" required>
                    </div>
                    <hr>
                    <p class="frm-section-title">Mother Health Background Details</p>
                    <div class="frm-row d-flex">
                        <textarea id="" name="mom-health-conditions" placeholder="Mother known health conditions" maxlength="1000"></textarea>
                        <textarea id="" name="mom-allergies" placeholder="Mother known allergies" maxlength="1000"></textarea>
                    </div>
                    <!-- Rubella vaccination status option -->
                    <div class="frm-row d-flex" style="justify-content:flex-start !important; align-items:center !important;"> 
                        <p>Have vaccinated against Rubella?</p>
                        <input type="radio" id="rbl-state-yes" name="rbl-status" value="Yes" style="width:10% !important;" required>
                        <label for="rbl-status" style="width:0% !important;">Yes</label><br>
                        <input type="radio" id="rbl-state-no" name="rbl-status" value="No" style="width:10% !important;" required>
                        <label for="rbl-status" style="width:0% !important;">No</label><br>
                    </div>
                    <hr>
                    <div class="frm-row d-flex">
                        <select name="marital-status" id="mar-status" required>
                            <option value="" disabled selected>Marital status</option>
                            <option value="Married">Married</option>
                            <option value="Unmarried">Unmarried</option>
                        </select>
                    </div>
                    <div class="frm-row" id="blood-rel-input" style="justify-content:flex-start !important; align-items:center !important; display:none;"> 
                        <p>Blood relative marriage?</p>
                        <input type="radio" id="blood-rel-yes" name="blood-relativity" value="Yes" style="width:10% !important;" required>
                        <label for="blood-relativity" style="width:0% !important;">Yes</label><br>
                        <input type="radio" id="blood-rel-no" name="blood-relativity" value="No" style="width:10% !important;" required>
                        <label for="blood-relativity" style="width:0% !important;">No</label><br>
                    </div>
                    <p class="frm-section-title" id="mama-hub-title" style="display:none;">Husband Details</p>
                    <div class="mama-hub-data-row flex-column frm-row" id="mama-hub-data-row">
                        <div class="d-flex frm-row">
                            <input type="text" id="hub-name" name="mom-hub-name" placeholder="Husband's name">
                            <input type="text" id="hub-job" name="mom-hub-job" placeholder="Husband's occupation">
                            <input type="tel" id="phone" name="mom-hub-phone" pattern="[0-9]{10}" placeholder="Enter husband's phone number" required>
                        </div>
                        <div class="d-flex frm-row">
                            <div class="frm-col frm-dt-l-col d-flex flex-column">
                                <label for="birthday">Husband's Birthdate</label>
                                <input type="date" id="hub-birthday" name="mom-hub-bday" placeholder="Husband's Birthday" required>
                            </div>
                            <input type="text" id="hub-birthplace" name="mom-hub-birthplace" placeholder="Husband's birthplace">
                        </div>
                        <div class="d-flex frm-row">
                            <textarea id="" name="mama-hub-health-conditions" placeholder="Husband's known health conditions" maxlength="1000"></textarea>
                            <textarea id="" name="mama-hub-allergies" placeholder="Husband's known allergies" maxlength="1000"></textarea>
                        </div>
                    </div>
                    <hr>
                    <p class="frm-section-title">Mother Account Details</p>
                    <div class="frm-row d-flex">
                        <input type="email" id="email" name="mom-email" placeholder="Enter your email" required>
                        <input type="password" id="pwd" name="mom-pwd" placeholder="Enter password" required>
                        <input type="password" id="repwd" name="mom-repwd" placeholder="Re enter password" required>
                    </div>
                    <div class="frm-row d-flex flex-row frm-footer-btn-row">
                        <a href="../pages/staff-dashboard.php">
                            <div class="reg-return-btn main-footer-btn">Return</div>
                        </a>
                        <input type="submit" value="Register" class="reg-btn">
                    </div>
                </div>
                
            </form>
            
        </main>
    </div>

    <script>
        var marriedStatus = document.getElementById("mar-status");
        var bloodRelStatus = document.getElementById("blood-rel-input");
        var hubDataRow = document.getElementById("mama-hub-data-row");
        var hubTitle = document.getElementById("mama-hub-title");

        marriedStatus.addEventListener("change",function(){//The if statement will trigger when the select element value changed
            var marValue = marriedStatus.value;
            if(marValue == "Married"){
                hubDataRow.style.display = "flex";
                hubTitle.style.display = "flex";
                bloodRelStatus.style.display = "flex";
            }
            else if(marValue == "Unmarried"){
                hubDataRow.style.display = "none";
                hubTitle.style.display = "none";
                bloodRelStatus.style.display = "none";
            }
        })
    </script>
</body>
</html>
