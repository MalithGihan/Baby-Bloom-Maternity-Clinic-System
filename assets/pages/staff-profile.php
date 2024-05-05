<?php 
session_start();

include 'dbaccess.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$staffID =  $_SESSION["staffID"];
echo $staffID;

$staffSQL = "SELECT * FROM staff WHERE staffID=$staffID";

$staffResult = mysqli_query($con,$staffSQL);
if($staffResult){
    while($staffRow = mysqli_fetch_assoc($staffResult)){
        $stFname = $staffRow['firstName'];
        $stMname = $staffRow['middleName'];
        $stSname = $staffRow['surname'];
        $stAdd = $staffRow['address'];
        $stDOB = $staffRow['dob'];
        $stNIC = $staffRow['NIC'];
        $stGender = $staffRow['gender'];
        $stPhone = $staffRow['phone'];
        $stPosition = $staffRow['position'];
        $stEmail = $staffRow['email'];
    }
}

echo $stFname;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $sFname = $_POST["staff-first-name"];
    $sMname =  $_POST["staff-mid-name"];
    $sLname = $_POST["staff-last-name"];
    $sAdd = $_POST["staff-address"];
    $sDOB = $_POST["staff-dob"];
    $sNIC = $_POST["staff-nic"];
    $sGender = $_POST["staff-gender"];
    $sPhone = $_POST["staff-phone"];

    $stUsql = "UPDATE staff SET firstName = ?, middleName = ?, surname = ?, address = ?, dob = ?, NIC = ?, gender = ?, phone = ? WHERE staffID = ?";
    $stUstmt = $con->prepare($stUsql);
    $stUstmt->bind_param("sssssssdd",$sFname,$sMname,$sLname,$sAdd,$sDOB,$sNIC,$sGender,$sPhone,$staffID);
    $stUstmt->execute();

    echo '<script>';
    echo 'alert(" Staff member details updated successfully!");';
    echo 'window.location.href="staff-profile.php";';
    //Page redirection after successfull insertion
    echo '</script>';
    exit();

            
    // Close the database connection
    $stmt->close();
    $con->close();
	
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Staff Profile</title>
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
                padding:0.5rem 0.8rem !important;
                width:40% !important;
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
            .staff-account-container{
                flex-direction:column;
            }

            @media only screen and (min-width:768px){
                .report-row{
                    flex-direction: row;
                }
                .staff-account-container{
                    flex-direction:row;
                    justify-content: space-between;
                }
                .staff-acc-mail{
                    flex:25%;
                }
                .staff-repass{
                    flex:75%;
                    flex-direction: row;
                    justify-content: space-between;
                    gap:1rem;
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
                <h2 class="main-header-title">STAFF PROFILE</h2>
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
                    <p class="row-title">YOUR PERSONAL DATA</p>
                </div>
                <div class="report-row" id="staff-detail-container">
                    <div class="d-flex flex-column" style="width:100%;">
                        <div class="d-flex report-row-sub">
                            <div class="row-col d-flex flex-column">
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Full name</h3>
                                    <p class="data-value"><?php echo $stFname; ?> <?php echo $stMname; ?> <?php echo $stSname; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">NIC</h3>
                                    <p class="data-value"><?php echo $stNIC; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Date of birth</h3>
                                    <p class="data-value"><?php echo $stDOB; ?></p>
                                </div>
                            </div>

                            <div class="row-col d-flex flex-column">
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Phone number</h3>
                                    <p class="data-value">0<?php echo $stPhone; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Address</h3>
                                    <p class="data-value"><?php echo $stAdd; ?></p>
                                </div>
                            </div>

                            <div class="row-col d-flex flex-column">
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Position</h3>
                                    <p class="data-value"><?php echo $stPosition; ?></p>
                                </div>
                                <div class="data-row d-flex flex-column">
                                    <h3 class="data-title">Gender</h3>
                                    <p class="data-value"><?php echo $stGender; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="report-row d-flex">
                    <button class="add-report-btn" id="add-report-btn">Edit personal data</button>
                </div>
                <form action="" method="POST" class="report-row flex-column" id="add-report-form">
                    <div class="add-hr-form-row d-flex flex-row justify-content-between">
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">NIC</label>
                            <input type="text" id="staff-nic" name="staff-nic" placeholder="" value="<?php echo $stNIC; ?>" required>
                        </div>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">Date of birth</label>
                            <input type="date" id="staff-dob" name="staff-dob" placeholder="" value="<?php echo $stDOB; ?>" required>
                        </div>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">Phone number</label>
                            <input type="tel" id="phone" name="staff-phone" pattern="[0-9]{10}" placeholder="" value="0<?php echo $stPhone; ?>" required>
                        </div>
                        
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">First name</label>
                            <input type="text" id="fname" name="staff-first-name" placeholder="" value="<?php echo $stFname; ?>" required>
                        </div>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">Middle name</label>
                            <input type="text" id="mname" name="staff-mid-name" placeholder="" value="<?php echo $stMname; ?>" >
                        </div>
                        <div class="hr-frm-date d-flex flex-column">  
                            <label for="staff-dob">Last name</label> 
                            <input type="text" id="lname" name="staff-last-name" placeholder="" value="<?php echo $stSname; ?>" required>
                        </div>
                        
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">Address</label>
                            <input type="text" id="address" name="staff-address" placeholder="" value="<?php echo $stAdd; ?>" required>
                        </div>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">Gender</label>
                            <select name="staff-gender" required>
                                <option value="<?php echo $stGender; ?>" selected><?php echo $stGender; ?></option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <div class="frm-close-btn" id="frm-close-btn">Cancel</div>
                        <input type="submit" name="hr-submit" value="Save details" class="add-health-record-btn"> 
                    </div>
                </form>

                <div class="report-row d-flex">
                    <p class="row-title">YOUR ACCOUNT DATA</p>
                </div>
                <div class="data-row d-flex staff-account-container">
                    <div class="d-flex flex-column staff-acc-mail">
                        <h3 class="data-title">Email address</h3>
                        <p class="data-value"><?php echo $stEmail; ?></p>
                    </div>
                    <form action="staff-pass-update.php" method="POST" class="d-flex staff-repass">
                        <input type="password" name="staff-pass" placeholder="Enter a new password" required>
                        <input type="password" name="staff-repass" placeholder="Reenter the new password" required>
                        <input type="submit" value="Update password" class="add-health-record-btn">
                    </form>
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
        var addRecordBtn = document.getElementById("add-report-btn");
        var hideRecordBtn = document.getElementById("frm-close-btn");
        var recordForm = document.getElementById("add-report-form");
        var stDataContainer = document.getElementById("staff-detail-container");

        addRecordBtn.addEventListener("click",function(){
            stDataContainer.style.display = "none";
            addRecordBtn.style.display = "none";
            recordForm.style.display = "flex";
            console.log("GG");
        })
        hideRecordBtn.addEventListener("click",function(){
            stDataContainer.style.display = "flex";
            addRecordBtn.style.display = "block";
            recordForm.style.display = "none";
        })

    </script>
</body>
</html>
