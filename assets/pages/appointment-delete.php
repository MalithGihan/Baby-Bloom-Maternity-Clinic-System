<?php
session_start();
include 'dbaccess.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$AppID = $_GET['id'];

echo $AppID;

$sql = "DELETE FROM appointments WHERE appointment_id=?";

$stmt = $con->prepare($sql);
$stmt->bind_param("d", $AppID);

// Execute the SQL statement
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo '<script>';
    echo 'alert("Appointment deleted successfully!");';
    echo 'window.location.href="appointments-list.php";';
    echo '</script>';
} else {
    echo '<script>';
    echo 'alert("Failed to delete appointment");';
    echo 'window.location.href="appointments-list.php.php";';
    echo '</script>';
}

exit();
       
// Close the database connection
$stmt->close();
$con->close();
?>