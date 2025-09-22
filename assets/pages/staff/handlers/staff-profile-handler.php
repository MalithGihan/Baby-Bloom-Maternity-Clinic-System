<?php
session_start();

// Check if user is logged in as staff
if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../../auth/staff-login.php");
    exit();
}

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../staff-profile.php");
    exit();
}

include '../../shared/db-access.php';

// Initialize variables
$error_message = "";
$success_message = "";

try {
    $staffID = $_SESSION["staffID"];

    // Get and validate input data
    $sFname = trim($_POST["staff-first-name"] ?? "");
    $sMname = trim($_POST["staff-mid-name"] ?? "");
    $sLname = trim($_POST["staff-last-name"] ?? "");
    $sAdd = trim($_POST["staff-address"] ?? "");
    $sDOB = $_POST["staff-dob"] ?? "";
    $sNIC = trim($_POST["staff-nic"] ?? "");
    $sGender = $_POST["staff-gender"] ?? "";
    $sPhone = trim($_POST["staff-phone"] ?? "");

    // Basic validation
    if (empty($sFname) || empty($sLname) || empty($sAdd) ||
        empty($sDOB) || empty($sNIC) || empty($sGender) || empty($sPhone)) {
        $error_message = "Please fill in all required fields.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    // Validate date format
    $dobDate = DateTime::createFromFormat('Y-m-d', $sDOB);
    if (!$dobDate || $dobDate->format('Y-m-d') !== $sDOB) {
        $error_message = "Please enter a valid date of birth.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    // Validate age (must be at least 18)
    $today = new DateTime();
    $age = $today->diff($dobDate)->y;
    if ($age < 18) {
        $error_message = "Staff member must be at least 18 years old.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    // Validate gender
    $validGenders = ['Male', 'Female', 'Other'];
    if (!in_array($sGender, $validGenders)) {
        $error_message = "Please select a valid gender.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    // Validate phone number (basic validation)
    if (!preg_match('/^[0-9]{10}$/', $sPhone)) {
        $error_message = "Please enter a valid 10-digit phone number.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    // Check if NIC is already used by another staff member
    $nicCheckSql = "SELECT staffID FROM staff WHERE NIC = ? AND staffID != ?";
    $nicCheckStmt = $con->prepare($nicCheckSql);

    if ($nicCheckStmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    $nicCheckStmt->bind_param("si", $sNIC, $staffID);
    $nicCheckStmt->execute();
    $nicResult = $nicCheckStmt->get_result();

    if ($nicResult->num_rows > 0) {
        $error_message = "This NIC is already registered to another staff member.";
        $_SESSION['staff_profile_error'] = $error_message;
        $nicCheckStmt->close();
        header("Location: ../staff-profile.php");
        exit();
    }

    $nicCheckStmt->close();

    // Update staff profile
    $stUsql = "UPDATE staff SET firstName = ?, middleName = ?, surname = ?, address = ?, dob = ?, NIC = ?, gender = ?, phone = ? WHERE staffID = ?";
    $stUstmt = $con->prepare($stUsql);

    if ($stUstmt === false) {
        error_log('Database prepare failed for staff update: ' . $con->error);
        $error_message = "Update failed. Please try again later.";
        $_SESSION['staff_profile_error'] = $error_message;
        header("Location: ../staff-profile.php");
        exit();
    }

    $stUstmt->bind_param("ssssssssi", $sFname, $sMname, $sLname, $sAdd, $sDOB, $sNIC, $sGender, $sPhone, $staffID);

    if (!$stUstmt->execute()) {
        error_log('Failed to update staff profile: ' . $stUstmt->error);
        $error_message = "Update failed. Please try again later.";
        $_SESSION['staff_profile_error'] = $error_message;
        $stUstmt->close();
        header("Location: ../staff-profile.php");
        exit();
    }

    $stUstmt->close();

    // Update session data
    $_SESSION['staffFName'] = $sFname;
    $_SESSION['staffSName'] = $sLname;

    // Success message
    $_SESSION['staff_profile_success'] = "Profile updated successfully!";
    header("Location: ../staff-profile.php");
    exit();

} catch (Exception $e) {
    error_log('Staff profile update error: ' . $e->getMessage());
    $error_message = "Update failed. Please try again later.";
    $_SESSION['staff_profile_error'] = $error_message;
    header("Location: ../staff-profile.php");
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>