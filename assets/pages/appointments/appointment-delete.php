<?php
// Use secure session initialization
require_once __DIR__ . '/../shared/session-init.php';

include '../shared/db-access.php';

// Check if user is logged in as staff
if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php");
    exit();
}

// Validate required parameters
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid appointment ID.";
    header("Location: appointments-list.php");
    exit();
}

$AppID = (int)$_GET['id'];

// IDOR Protection: Verify appointment exists before deletion
// Note: In a full implementation, you'd also verify staff has permission to delete this specific appointment
$verifySQL = "SELECT appointment_id, mama_email FROM appointments WHERE appointment_id = ?";
$verifyStmt = $con->prepare($verifySQL);
if ($verifyStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: appointments-list.php");
    exit();
}

$verifyStmt->bind_param("i", $AppID);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows === 0) {
    // Appointment doesn't exist
    error_log("Unauthorized appointment deletion attempt - AppID: $AppID, Staff: " . $_SESSION["staffEmail"]);
    $_SESSION['error_message'] = "Access denied. Appointment not found.";
    header("Location: appointments-list.php");
    exit();
}

$appointmentData = $verifyResult->fetch_assoc();
$verifyStmt->close();

// Proceed with deletion
$sql = "DELETE FROM appointments WHERE appointment_id = ?";
$stmt = $con->prepare($sql);
if ($stmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: appointments-list.php");
    exit();
}

$stmt->bind_param("i", $AppID);
$stmt->execute();

// Check if deletion was successful
if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Appointment deleted successfully!";
    error_log("Appointment deleted - AppID: $AppID, Patient: " . $appointmentData['mama_email'] . ", Staff: " . $_SESSION["staffEmail"]);
} else {
    $_SESSION['error_message'] = "Failed to delete appointment.";
    error_log("Failed to delete appointment - AppID: $AppID, Staff: " . $_SESSION["staffEmail"]);
}

// Close the database connection
$stmt->close();
$con->close();

// Redirect back to appointments list
header("Location: appointments-list.php");
exit();
?>