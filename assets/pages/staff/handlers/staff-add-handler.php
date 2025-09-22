<?php
session_start();

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../staff-management.php");
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
    $staffPass = $_POST['staff-pwd'] ?? "";

    // Basic validation
    if (empty($staffFName) || empty($staffSName) || empty($staffAdd) ||
        empty($staffDOB) || empty($staffNIC) || empty($staffGender) ||
        empty($staffPhone) || empty($staffPosition) || empty($staffEmail) || empty($staffPass)) {
        $error_message = "Please fill in all required fields.";
        $_SESSION['staff_add_error'] = $error_message;
        header("Location: ../staff-management.php");
        exit();
    }

    // Email validation
    if (!filter_var($staffEmail, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
        $_SESSION['staff_add_error'] = $error_message;
        header("Location: ../staff-management.php");
        exit();
    }

    // Password validation
    if (strlen($staffPass) < 6) {
        $error_message = "Password must be at least 6 characters long.";
        $_SESSION['staff_add_error'] = $error_message;
        header("Location: ../staff-management.php");
        exit();
    }

    // Check if staff member already exists
    $presql = "SELECT * FROM staff WHERE email = ? OR NIC = ?";
    $preStmt = $con->prepare($presql);

    if ($preStmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['staff_add_error'] = $error_message;
        header("Location: ../staff-management.php");
        exit();
    }

    $preStmt->bind_param("ss", $staffEmail, $staffNIC);
    $preStmt->execute();
    $preStmt->store_result();

    if ($preStmt->num_rows > 0) {
        $error_message = "Staff member with this email or NIC already exists.";
        $_SESSION['staff_add_error'] = $error_message;
        $preStmt->close();
        header("Location: ../staff-management.php");
        exit();
    }

    $preStmt->close();

    // Hash password
    $staffHashPass = password_hash($staffPass, PASSWORD_ARGON2ID);

    // Insert staff member data
    $sql = "INSERT INTO staff (firstName, middleName, surname, address, dob, NIC, gender, phone, position, email, password) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed for staff: ' . $con->error);
        $error_message = "Failed to add staff member. Please try again later.";
        $_SESSION['staff_add_error'] = $error_message;
        header("Location: ../staff-management.php");
        exit();
    }

    $stmt->bind_param("sssssssisss", $staffFName, $staffMName, $staffSName, $staffAdd,
                     $staffDOB, $staffNIC, $staffGender, $staffPhone, $staffPosition,
                     $staffEmail, $staffHashPass);

    if (!$stmt->execute()) {
        error_log('Failed to insert staff: ' . $stmt->error);
        $error_message = "Failed to add staff member. Please try again later.";
        $_SESSION['staff_add_error'] = $error_message;
        $stmt->close();
        header("Location: ../staff-management.php");
        exit();
    }

    $stmt->close();

    // Success
    $_SESSION['staff_add_success'] = "Staff member added successfully!";
    header("Location: ../staff-management.php");
    exit();

} catch (Exception $e) {
    error_log('Staff add error: ' . $e->getMessage());
    $error_message = "Failed to add staff member. Please try again later.";
    $_SESSION['staff_add_error'] = $error_message;
    header("Location: ../staff-management.php");
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>