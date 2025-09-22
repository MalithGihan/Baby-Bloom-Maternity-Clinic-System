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

// CSRF protection
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    echo '<script>alert("Invalid request. Please try again."); window.location.href="appointments-list.php";</script>';
    exit();
}

// Validate & cast ID
$AppID = (isset($_POST['id']) && ctype_digit((string)$_POST['id'])) ? (int)$_POST['id'] : null;
if ($AppID === null) {
    echo '<script>alert("Invalid appointment ID."); window.location.href="appointments-list.php";</script>';
    exit();
}


$sql = "DELETE FROM appointments WHERE appointment_id=?";

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
    // Rotate token after success
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $stmt->close(); $con->close();
    echo '<script>alert("Appointment deleted successfully!"); window.location.href="appointments-list.php";</script>';
} else {
    $stmt->close(); $con->close();
    echo '<script>alert("Failed to delete appointment"); window.location.href="appointments-list.php";</script>';
}
exit();
?>