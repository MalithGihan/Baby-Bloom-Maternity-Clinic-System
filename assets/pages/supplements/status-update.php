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
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid supplement request ID.";
    header("Location: supplement-request-status.php");
    exit();
}

$SR_ID = $_GET['id'];

// IDOR Protection: Verify supplement request exists before updating
$verifySQL = "SELECT SR_ID, NIC FROM supplement_request WHERE SR_ID = ?";
$verifyStmt = $con->prepare($verifySQL);
if ($verifyStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: supplement-request-status.php");
    exit();
}

$verifyStmt->bind_param("s", $SR_ID);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows === 0) {
    // Supplement request doesn't exist
    error_log("Unauthorized supplement status update attempt - SR_ID: $SR_ID, Staff: " . $_SESSION["staffEmail"]);
    $_SESSION['error_message'] = "Access denied. Supplement request not found.";
    header("Location: supplement-request-status.php");
    exit();
}

$requestData = $verifyResult->fetch_assoc();
$verifyStmt->close();

// Proceed with status update
$sql = "UPDATE supplement_request SET status='Confirmed' WHERE SR_ID = ?";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: supplement-request-status.php");
    exit();
}

$stmt->bind_param("s", $SR_ID);
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Supplement request status updated successfully!";
    error_log("Supplement status updated - SR_ID: $SR_ID, Patient: " . $requestData['NIC'] . ", Staff: " . $_SESSION["staffEmail"]);
} else {
    $_SESSION['error_message'] = "Failed to update supplement request status.";
    error_log("Failed to update supplement status - SR_ID: $SR_ID, Staff: " . $_SESSION["staffEmail"]);
}

// Close the database connection
$stmt->close();
$con->close();

header("Location: supplement-request-status.php");
exit();
?>