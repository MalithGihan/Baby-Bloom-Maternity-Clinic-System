<?php
session_start();
include '../shared/db-access.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointments-list.php");
    exit();
}

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    echo '<script>alert("Invalid request. Please try again."); window.location.href="appointments-list.php";</script>';
    exit();
}

$AppID = (isset($_POST['id']) && ctype_digit((string)$_POST['id'])) ? (int)$_POST['id'] : null;
if ($AppID === null) {
    echo '<script>alert("Invalid appointment ID."); window.location.href="appointments-list.php";</script>';
    exit();
}

$sql = "UPDATE appointments SET appointment_status='Confirmed' WHERE appointment_id=?";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    echo '<script>alert("System error. Please try again."); window.location.href="appointments-list.php";</script>';
    exit();
}
$stmt->bind_param("i", $AppID);

// Execute the SQL statement
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    // Rotate CSRF token after success
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $stmt->close(); $con->close();
    echo '<script>alert("Appointment status updated successfully!"); window.location.href="appointments-list.php";</script>';
} else {
    $stmt->close(); $con->close();
    echo '<script>alert("Failed to update appointment status!"); window.location.href="appointments-list.php";</script>';
}
exit();

// Close the database connection
$stmt->close();
$con->close();
?>