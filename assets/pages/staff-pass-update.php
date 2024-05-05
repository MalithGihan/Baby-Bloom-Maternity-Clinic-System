<?php
session_start();
include 'dbaccess.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$staffID =  $_SESSION["staffID"];

$pass = $_POST['staff-pass']; 
$repass = $_POST['staff-repass'];
$staffHashPass = password_hash($pass, PASSWORD_ARGON2ID);//Hashed staff password

if($pass==$repass){
    $sql = "UPDATE staff SET password=? WHERE staffID=?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss",$staffHashPass ,$staffID);

    // Execute the SQL statement
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        echo '<script>';
        echo 'alert("Your password updated successfully!");';
        echo 'window.location.href="staff-profile.php";';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'alert("Failed to update your password. Please contact the system administrator!");';
        echo 'window.location.href="staff-profile.php#password-reset";';
        echo '</script>';
    }
}
else{
    echo '<script>';
    echo 'alert("New password and the re entered new password do not match. Check your both passwords again!");';
    echo 'window.location.href="staff-profile.php#password-reset";';
    echo '</script>';
}

exit();
       
// Close the database connection
$stmt->close();
$con->close();
?>