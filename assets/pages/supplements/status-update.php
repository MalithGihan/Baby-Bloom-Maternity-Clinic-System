<?php
session_start();
include '../shared/db-access.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$SR_ID = $_GET['id'];

echo $SR_ID;

$sql = "UPDATE supplement_request SET status='Confirmed' WHERE SR_ID=?";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $SR_ID);

// Execute the SQL statement
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo '<script>';
    echo 'alert("Supplement request status updated successfully!");';
    echo 'window.location.href="supplement-request-status.php";';
    echo '</script>';
} else {
    echo '<script>';
    echo 'alert("Failed to update supplement request status!");';
    echo 'window.location.href="supplement-request-status.php";';
    echo '</script>';
}

exit();
       
// Close the database connection
$stmt->close();
$con->close();
?>