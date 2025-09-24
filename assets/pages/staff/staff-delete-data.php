<?php
// Use secure session initialization
require_once __DIR__ . '/../shared/session-init.php';

include '../shared/db-access.php';

// Check if user is logged in and is a Sister (admin)
if (!isset($_SESSION["staffEmail"]) || $_SESSION["staffPosition"] != "Sister") {
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

// Validate required parameters
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid staff ID.";
    header("Location: staff-management.php");
    exit();
}

$staffID = (int)$_GET['id'];

// IDOR Protection: Verify staff member exists and get their details
$verifySQL = "SELECT staffID, email, firstName, lastName, staffPosition FROM staff WHERE staffID = ?";
$verifyStmt = $con->prepare($verifySQL);
if ($verifyStmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: staff-management.php");
    exit();
}

$verifyStmt->bind_param("i", $staffID);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows === 0) {
    // Staff member doesn't exist
    error_log("Unauthorized staff deletion attempt - StaffID: $staffID, Admin: " . $_SESSION["staffEmail"]);
    $_SESSION['error_message'] = "Access denied. Staff member not found.";
    header("Location: staff-management.php");
    exit();
}

$staffData = $verifyResult->fetch_assoc();
$verifyStmt->close();

// Prevent self-deletion
if ($staffData['email'] === $_SESSION["staffEmail"]) {
    $_SESSION['error_message'] = "Cannot delete your own account.";
    header("Location: staff-management.php");
    exit();
}

// Additional protection: Prevent deletion of other Sister-level staff by non-super-admin
// This adds an extra layer of protection for administrative accounts
if ($staffData['staffPosition'] === "Sister") {
    // In a full implementation, you might have a super-admin role check here
    $_SESSION['error_message'] = "Cannot delete administrator accounts.";
    header("Location: staff-management.php");
    exit();
}

// Proceed with deletion
$sql = "DELETE FROM staff WHERE staffID = ?";
$stmt = $con->prepare($sql);
if ($stmt === false) {
    error_log('Database prepare failed: ' . $con->error);
    $_SESSION['error_message'] = "System error. Please try again.";
    header("Location: staff-management.php");
    exit();
}

$stmt->bind_param("i", $staffID);
$stmt->execute();

// Check if deletion was successful
if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Staff member removed successfully!";
    error_log("Staff deleted - StaffID: $staffID, Name: " . $staffData['firstName'] . " " . $staffData['lastName'] . ", Admin: " . $_SESSION["staffEmail"]);
} else {
    $_SESSION['error_message'] = "Failed to delete staff member.";
    error_log("Failed to delete staff - StaffID: $staffID, Admin: " . $_SESSION["staffEmail"]);
}

// Close the database connection
$stmt->close();
$con->close();

// Redirect back to staff management
header("Location: staff-management.php");
exit();

?>