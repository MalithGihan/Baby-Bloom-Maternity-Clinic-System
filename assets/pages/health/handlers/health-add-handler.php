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

// CSRF protection
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['health_error'] = "Invalid request. Please try again.";
    header("Location: ../mw-health-details.php");
    exit();
}

// Initialize variables
$error_message = "";
$success_message = "";

try {
    // Get and validate input data
    $NIC = trim($_POST['mama-nic'] ?? "");
    $date = $_POST['hr-date'] ?? "";
    $appx_Next_date = $_POST['hr-appx-date'] ?? "";
    $heartRate = $_POST['hr-heart-rate'] ?? "";
    $bloodPressure = trim($_POST['hr-blood-pss'] ?? "");
    $cholesterolLevel = $_POST['hr-chol-lvl'] ?? "";
    $heartRateConclution = trim($_POST['hr-heart-conclusion'] ?? "");
    $bloodPressureConclution = trim($_POST['hr-blood-pss-conclusion'] ?? "");
    $weight = $_POST['hr-weight'] ?? "";
    $babyMovement = trim($_POST['hr-baby-movement'] ?? "");
    $babyHeartbeat = $_POST['hr-baby-heart-rate'] ?? "";
    $scanConclution = trim($_POST['hr-scan-conclusion'] ?? "");
    $vcabnormalitiescBy = trim($_POST['hr-abnormalities'] ?? "");
    $sprcial_Instruction = trim($_POST['hr-spec-instructions'] ?? "");

    // Basic validation
    if (empty($NIC) || empty($date) || empty($heartRate) || empty($bloodPressure) || empty($weight)) {
        $error_message = "Please fill in all required fields.";
        $_SESSION['health_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    // Validate numeric fields
    if (!is_numeric($heartRate) || !is_numeric($weight) || !is_numeric($cholesterolLevel) || !is_numeric($babyHeartbeat)) {
        $error_message = "Please enter valid numeric values for heart rate, weight, cholesterol level, and baby heartbeat.";
        $_SESSION['health_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    // Convert numeric values
    $heartRate = intval($heartRate);
    $cholesterolLevel = floatval($cholesterolLevel);
    $weight = floatval($weight);
    $babyHeartbeat = intval($babyHeartbeat);

    // Insert health report data
    $sql = "INSERT INTO health_report (date, heartRate, bloodPressure, cholesterolLevel, weight, heartRateConclusion, bloodPressureConclusion, babyMovement, babyHeartbeat, scanConclusion, abnormalities, special_Instruction, appx_Next_date, NIC) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed for health report: ' . $con->error);
        $error_message = "Failed to add health details. Please try again later.";
        $_SESSION['health_add_error'] = $error_message;
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $stmt->bind_param("sisidsssisssss", $date, $heartRate, $bloodPressure, $cholesterolLevel, $weight,
                     $heartRateConclution, $bloodPressureConclution, $babyMovement, $babyHeartbeat,
                     $scanConclution, $vcabnormalitiescBy, $sprcial_Instruction, $appx_Next_date, $NIC);

    if (!$stmt->execute()) {
        error_log('Failed to insert health report: ' . $stmt->error);
        $error_message = "Failed to add health details. Please try again later.";
        $_SESSION['health_add_error'] = $error_message;
        $stmt->close();
        header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
        exit();
    }

    $stmt->close();

    // Rotate CSRF token after successful operation
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Success
    $_SESSION['health_add_success'] = "Health details added successfully!";
    header("Location: ../mw-health-details.php?id=" . urlencode($NIC));
    exit();

} catch (Exception $e) {
    error_log('Health add error: ' . $e->getMessage());
    $error_message = "Failed to add health details. Please try again later.";
    $_SESSION['health_add_error'] = $error_message;
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