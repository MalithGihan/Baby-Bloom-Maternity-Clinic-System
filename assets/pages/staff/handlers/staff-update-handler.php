<?php
session_start();

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../staff-view-data.php");
    exit();
}

include '../../shared/db-access.php';

// Check if user is logged in as staff
if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../../auth/staff-login.php");
    exit();
}

// Initialize variables
$error_message = "";
$success_message = "";

try {
    // Get staff ID from URL parameter or session
    $staffID = $_GET['id'] ?? $_SESSION['staffID'] ?? null;

    if (!$staffID) {
        $error_message = "Staff ID not provided.";
        $_SESSION['staff_update_error'] = $error_message;
        header("Location: ../staff-management.php");
        exit();
    }

    // Get and validate input data
    $staffFName = trim($_POST['staff-first-name'] ?? "");
    $staffMName = trim($_POST['staff-mid-name'] ?? "");
    $staffSName = trim($_POST['staff-last-name'] ?? "");
    $staffAdd = trim($_POST['staff-address'] ?? "");
    $staffDOB = $_POST['staff-dob'] ?? "";
    $staffNIC = trim($_POST['staff-nic'] ?? "");
    $staffGender = $_POST['staff-gender'] ?? "";
    $staffPhone = trim($_POST['staff-phone'] ?? "");
    $staffPosition = $_POST['staff-position'] ?? "";
    $staffEmail = trim($_POST['staff-email'] ?? "");

    // Basic validation
    if (empty($staffFName) || empty($staffSName) || empty($staffAdd) ||
        empty($staffDOB) || empty($staffNIC) || empty($staffGender) ||
        empty($staffPhone) || empty($staffPosition) || empty($staffEmail)) {
        $error_message = "Please fill in all required fields.";
        $_SESSION['staff_update_error'] = $error_message;
        header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
        exit();
    }

    // Email validation
    if (!filter_var($staffEmail, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
        $_SESSION['staff_update_error'] = $error_message;
        header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
        exit();
    }

    // Check if email or NIC is already used by another staff member
    $presql = "SELECT * FROM staff WHERE (email = ? OR NIC = ?) AND staffID != ?";
    $preStmt = $con->prepare($presql);

    if ($preStmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['staff_update_error'] = $error_message;
        header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
        exit();
    }

    $preStmt->bind_param("ssi", $staffEmail, $staffNIC, $staffID);
    $preStmt->execute();
    $preStmt->store_result();

    if ($preStmt->num_rows > 0) {
        $error_message = "Another staff member with this email or NIC already exists.";
        $_SESSION['staff_update_error'] = $error_message;
        $preStmt->close();
        header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
        exit();
    }

    $preStmt->close();

    // Update staff member data
    $sql = "UPDATE staff SET firstName = ?, middleName = ?, surname = ?, address = ?, dob = ?, NIC = ?, gender = ?, phone = ?, position = ?, email = ? WHERE staffID = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed for staff update: ' . $con->error);
        $error_message = "Failed to update staff member. Please try again later.";
        $_SESSION['staff_update_error'] = $error_message;
        header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
        exit();
    }

    $stmt->bind_param("sssssssissi", $staffFName, $staffMName, $staffSName, $staffAdd,
                     $staffDOB, $staffNIC, $staffGender, $staffPhone, $staffPosition,
                     $staffEmail, $staffID);

    if (!$stmt->execute()) {
        error_log('Failed to update staff: ' . $stmt->error);
        $error_message = "Failed to update staff member. Please try again later.";
        $_SESSION['staff_update_error'] = $error_message;
        $stmt->close();
        header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
        exit();
    }

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        // Update session data if updating own profile
        if ($staffID == $_SESSION['staffID']) {
            $_SESSION['staffFName'] = $staffFName;
            $_SESSION['staffSName'] = $staffSName;
            $_SESSION['staffEmail'] = $staffEmail;
            $_SESSION['staffPosition'] = $staffPosition;
        }

        $_SESSION['staff_update_success'] = "Staff member updated successfully!";
    } else {
        $_SESSION['staff_update_success'] = "No changes were made.";
    }

    $stmt->close();

    // Success - redirect back
    header("Location: ../staff-view-data.php?id=" . urlencode($staffID));
    exit();

} catch (Exception $e) {
    error_log('Staff update error: ' . $e->getMessage());
    $error_message = "Failed to update staff member. Please try again later.";
    $_SESSION['staff_update_error'] = $error_message;
    $staffID = $_GET['id'] ?? $_SESSION['staffID'] ?? "";
    header("Location: ../staff-view-data.php" . (!empty($staffID) ? "?id=" . urlencode($staffID) : ""));
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>