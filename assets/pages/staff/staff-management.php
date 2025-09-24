<?php 
session_start();

include '../shared/db-access.php';

if ($_SESSION["staffPosition"] != "Sister" ) {
    header("Location: ../dashboard/staff-dashboard.php"); // Redirect to staff dashboard if the logged in user isn't a Sister
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Staff management</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../../css/common-variables.css">
        <link rel="stylesheet" type="text/css" href="../../css/staff-pages.css">
        <script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/script.js"></script>
        <script src="../../js/form-toggle.js"></script>
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
                <h2 class="main-header-title">CLINICAL STAFF MANAGEMENT</h2>
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
                <div class="report-row d-flex">
                    <button class="add-report-btn" id="add-report-btn">Add new</button>
                    
                </div>
                <form action="handlers/staff-add-handler.php" method="POST" class="report-row flex-column" id="add-report-form">
                    <div class="add-hr-form-row d-flex flex-row justify-content-between">
                        <select name="staff-position" required>
                            <option value="" disabled selected>Staff Position</option>
                            <option value="Doctor">Doctor</option>
                            <option value="Sister">Sister</option>
                            <option value="Midwife">Midwife</option>
                        </select>
                        <input type="text" id="staff-nic" name="staff-nic" placeholder="NIC of the staff member"required>
                        <div class="hr-frm-date d-flex flex-column">
                            <label for="staff-dob">Date of birth</label>
                            <input type="date" id="staff-dob" name="staff-dob" placeholder="Date of birth" required>
                        </div>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <input type="text" id="fname" name="staff-first-name" placeholder="First name" required>
                        <input type="text" id="mname" name="staff-mid-name" placeholder="Middle name">
                        <input type="text" id="lname" name="staff-last-name" placeholder="Last name" required>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <input type="text" id="address" name="staff-address" placeholder="Home address" required>
                        <select name="staff-gender" required>
                            <option value="" disabled selected>Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <input type="tel" id="phone" name="staff-phone" pattern="[0-9]{10}" placeholder="Enter phone number" required>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <input type="email" id="email" name="staff-email" placeholder="Enter email address" required>
                        <input type="password" id="pwd" name="staff-pwd" placeholder="Enter password" required>
                    </div>
                    <div class="add-hr-form-row d-flex flex-row">
                        <div class="frm-close-btn" id="frm-close-btn">Cancel</div>
                        <input type="submit" name="hr-submit" value="Add staff member" class="add-health-record-btn"> 
                    </div>
                </form>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="dd">NIC</th>
                            <th class="dd">Position</th>
                            <th class="dd">First name</th>
                            <th class="dd">Last name</th>
                            <th class="dd">Phone number</th>
                        </tr>
                    </thead>
                    <?php

                        $sql = "SELECT * FROM staff";
                            $result = mysqli_query($con,$sql);
                            if($result){
                                $num = mysqli_num_rows($result);
                                echo "$num results found.";
                                if($num > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo '
                                        <tbody>
                                            <tr class="vaccine-results">
                                                <td>'.$row['NIC'].'</td>
                                                <td><b>'.$row['position'].'</b></td>
                                                <td>'.$row['firstName'].'</td>
                                                <td>'.$row['surname'].'</td>
                                                <td>0'.$row['phone'].'</td>
                                                <!--
                                                <td class="table-btn-container d-flex flex-row justify-content-center">
                                                    <a class="mom-list-btn" href="staff-view-data.php?id='.$row["staffID"].'">View/Edit</a>
                                                    <a class="mom-list-btn-remove" href="staff-delete-data.php?id='.$row["staffID"].'">Remove</a>
                                                </td>
                                                -->
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
                <a href="../dashboard/staff-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

</body>
</html>
