<?php
session_start();
include 'dbaccess.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$staffFName = $_POST['staff-first-name']; 
$staffMName = $_POST['staff-mid-name']; 
$staffSName = $_POST['staff-last-name'];
$staffAdd = $_POST['staff-address']; 
$staffDOB = $_POST['staff-dob']; 
$staffNIC = $_POST['staff-nic']; 
$staffGender = $_POST['staff-gender'];
$staffPhone = $_POST['staff-phone']; 
$staffPosition = $_POST['staff-position']; 
$staffEmail = $_POST['staff-email']; 
$staffPass = $_POST['staff-pwd'];
$staffHashPass = password_hash($staffPass, PASSWORD_ARGON2ID);//Hashed password
  

$sql = "INSERT INTO staff (firstName,middleName,surname,address,dob,NIC,gender,phone,position,email,password) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sssssssisss", $staffFName,$staffMName,$staffSName,$staffAdd,$staffDOB,$staffNIC,$staffGender,$staffPhone,$staffPosition,$staffEmail,$staffHashPass);
$stmt->execute();

//echo $NIC;
//echo $vccName;
echo '<script>';
echo 'alert("Staff member added successfully!");';
echo 'window.location.href="staff-management.php";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$stmt->close();
$con->close();
?>