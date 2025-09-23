<?php
session_start();
include '../shared/db-access.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

// Validate and sanitize input
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<script>';
    echo 'alert("Invalid appointment ID");';
    echo 'window.location.href="appointments-list.php";';
    echo '</script>';
    exit();
}

$AppID = (int)$_GET['id'];

$sql = "DELETE FROM appointments WHERE appointment_id=?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $AppID);

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
    echo 'window.location.href="appointments-list.php";';
    echo '</script>';
}

exit();
       
// Close the database connection
$stmt->close();
$con->close();
?>