<?php
// Use secure session initialization
require_once __DIR__ . '/../shared/session-init.php';

include '../shared/db-access.php';

// Check if user is logged in and is a Sister
if (!isset($_SESSION["staffEmail"]) || $_SESSION["staffPosition"] != "Sister") {
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

// Validate required parameters
if (!isset($_GET['id']) || !isset($_GET['NIC'])) {
    $_SESSION['error_message'] = "Missing required parameters.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$HR_ID = $_GET['id'];
$NIC = $_GET['NIC'];

// IDOR Protection: Verify the health report belongs to the specified patient
$verifySQL = "SELECT HR_ID, NIC FROM health_report WHERE HR_ID = ? AND NIC = ?";
$verifyStmt = $con->prepare($verifySQL);
if ($verifyStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$verifyStmt->bind_param("is", $HR_ID, $NIC);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows === 0) {
    // Health report doesn't exist or doesn't belong to the specified patient
    error_log("Unauthorized health report deletion attempt - HR_ID: $HR_ID, NIC: $NIC, Staff: " . $_SESSION["staffEmail"]);
    $_SESSION['error_message'] = "Access denied. Invalid health report.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$verifyStmt->close();

// Proceed with deletion - now we know the health report exists and belongs to the patient
$sql = "DELETE FROM health_report WHERE HR_ID = ? AND NIC = ?";
$stmt = $con->prepare($sql);
if ($stmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

$stmt->bind_param("is", $HR_ID, $NIC);
$stmt->execute();

// Check if deletion was successful
if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Health report removed successfully!";
    error_log("Health report deleted - HR_ID: $HR_ID, NIC: $NIC, Staff: " . $_SESSION["staffEmail"]);
} else {
    $_SESSION['error_message'] = "Failed to delete health report.";
    error_log("Failed to delete health report - HR_ID: $HR_ID, NIC: $NIC, Staff: " . $_SESSION["staffEmail"]);
}

// Close the database connection
$stmt->close();
$con->close();

// Redirect back to health details page
header("Location: mw-health-details.php?id=" . urlencode($NIC) . "#mama-health-records");
exit();

?>