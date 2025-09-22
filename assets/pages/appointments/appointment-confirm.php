<?php
session_start();
include '../shared/db-access.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$AppID = $_GET['id'];

echo $AppID;

$sql = "UPDATE appointments SET appointment_status='Confirmed' WHERE appointment_id=?";

$stmt = $con->prepare($sql);
$stmt->bind_param("d", $AppID);

// Execute the SQL statement
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo '<script>';
    echo 'alert("Appointment status updated successfully!");';
    echo 'window.location.href="appointments-list.php";';
    echo '</script>';
} else {
    echo '<script>';
    echo 'alert("Failed to update appointment status!");';
    echo 'window.location.href="appointments-list.php";';
    echo '</script>';
}

exit();
       
// Close the database connection
$stmt->close();
$con->close();
?>