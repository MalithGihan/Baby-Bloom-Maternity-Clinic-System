<?php
session_start();

include '../shared/db-access.php';

if ($_SESSION["staffPosition"] != "Sister" ) {
    header("Location: ../dashboard/staff-dashboard.php"); // Redirect to staff dashboard if the logged in user isn't a Sister
    exit();
}

$staffID = $_GET['id'];
// TODO: Remove debug statement below
echo $staffID;

$sql = "DELETE FROM staff WHERE staffID=?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $staffID);
$stmt->execute();

//echo $NIC;
//echo $vccName;
echo '<script>';
echo 'alert("Staff member removed successfully!");';
echo 'window.location.href="staff-management.php";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$stmt->close();
$con->close();

?>