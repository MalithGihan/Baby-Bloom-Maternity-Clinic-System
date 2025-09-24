<?php
session_start();

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../mw-health-details.php");
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
    $NIC = trim($_POST['mama-nic'] ?? "");
    $mamaHeight = $_POST['mama-height'] ?? "";
    $mamaBloodGroup = trim($_POST['mama-blood-group'] ?? "");
    $mamaHubBloodGroup = trim($_POST['hub-blood-group'] ?? "");

    // Basic validation
    if (empty($NIC) || empty($mamaHeight) || empty($mamaBloodGroup)) {
        $error_message = "Please fill in all required fields (NIC, height, and blood group).";
        $_SESSION['basic_data_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    // Validate height is numeric and reasonable
    if (!is_numeric($mamaHeight) || floatval($mamaHeight) <= 0 || floatval($mamaHeight) > 300) {
        $error_message = "Please enter a valid height in centimeters.";
        $_SESSION['basic_data_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $mamaHeight = floatval($mamaHeight);

    // Validate blood group format
    $validBloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    if (!in_array($mamaBloodGroup, $validBloodGroups)) {
        $error_message = "Please select a valid blood group.";
        $_SESSION['basic_data_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    // Validate husband blood group if provided
    if (!empty($mamaHubBloodGroup) && !in_array($mamaHubBloodGroup, $validBloodGroups)) {
        $error_message = "Please select a valid blood group for husband.";
        $_SESSION['basic_data_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    // Check if basic checkup record already exists for this NIC
    $checkSql = "SELECT * FROM basic_checkups WHERE NIC = ?";
    $checkStmt = $con->prepare($checkSql);

    if ($checkStmt === false) {
        error_log('Database prepare failed for basic checkup: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['basic_data_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $checkStmt->bind_param("s", $NIC);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $error_message = "Basic health data for this mother already exists. Please update the existing record instead.";
        $_SESSION['basic_data_add_error'] = $error_message;
        $checkStmt->close();
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $checkStmt->close();

    // Insert basic checkup data
    $sql = "INSERT INTO basic_checkups (height, blood_group, hub_blood_group, NIC) VALUES (?,?,?,?)";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed for basic checkups: ' . $con->error);
        $error_message = "Failed to add basic health details. Please try again later.";
        $_SESSION['basic_data_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $stmt->bind_param("dsss", $mamaHeight, $mamaBloodGroup, $mamaHubBloodGroup, $NIC);

    if (!$stmt->execute()) {
        error_log('Failed to insert basic checkups: ' . $stmt->error);
        $error_message = "Failed to add basic health details. Please try again later.";
        $_SESSION['basic_data_add_error'] = $error_message;
        $stmt->close();
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $stmt->close();

    // Success
    $_SESSION['basic_data_add_success'] = "Basic health details added successfully!";
    header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
    exit();

} catch (Exception $e) {
    error_log('Basic data add error: ' . $e->getMessage());
    $error_message = "Failed to add basic health details. Please try again later.";
    $_SESSION['basic_data_add_error'] = $error_message;
    $NIC = $_POST['mama-nic'] ?? "";
    header("Location: ../mw-health-details.php" . (!empty($NIC) ? "?id=" . urlencode($NIC) : ""));
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>