<?php
session_start();

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../mw-vaccination-details.php");
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
    $vccName = trim($_POST['vaccine-name'] ?? "");
    $vccDate = $_POST['vaccine-date'] ?? "";
    $vccBatch = trim($_POST['vaccine-batch'] ?? "");
    $momNIC = trim($_POST['mama-nic'] ?? "");
    $appBy = trim($_POST['vaccine-approved'] ?? "");
    $vccBy = trim($_POST['vaccine-doneby'] ?? "");

    // Basic validation
    if (empty($vccName) || empty($vccDate) || empty($vccBatch) || empty($momNIC) || empty($appBy) || empty($vccBy)) {
        $error_message = "Please fill in all required fields.";
        $_SESSION['vaccination_add_error'] = $error_message;
        header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
        exit();
    }

    // Validate date format
    $dateTime = DateTime::createFromFormat('Y-m-d', $vccDate);
    if (!$dateTime || $dateTime->format('Y-m-d') !== $vccDate) {
        $error_message = "Please enter a valid date.";
        $_SESSION['vaccination_add_error'] = $error_message;
        header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
        exit();
    }

    // Check if vaccination record already exists for this NIC and vaccine on the same date
    $checkSql = "SELECT * FROM vaccination_report WHERE NIC = ? AND vaccination = ? AND date = ?";
    $checkStmt = $con->prepare($checkSql);

    if ($checkStmt === false) {
        error_log('Database prepare failed for vaccination check: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['vaccination_add_error'] = $error_message;
        header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
        exit();
    }

    $checkStmt->bind_param("sss", $momNIC, $vccName, $vccDate);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $error_message = "Vaccination record for this vaccine on this date already exists.";
        $_SESSION['vaccination_add_error'] = $error_message;
        $checkStmt->close();
        header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
        exit();
    }

    $checkStmt->close();

    // Insert vaccination report data
    $sql = "INSERT INTO vaccination_report (vaccination, date, batchNo, NIC, approvedBy, vaccinatedBy) VALUES (?,?,?,?,?,?)";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed for vaccination report: ' . $con->error);
        $error_message = "Failed to add vaccination details. Please try again later.";
        $_SESSION['vaccination_add_error'] = $error_message;
        header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
        exit();
    }

    $stmt->bind_param("ssssss", $vccName, $vccDate, $vccBatch, $momNIC, $appBy, $vccBy);

    if (!$stmt->execute()) {
        error_log('Failed to insert vaccination report: ' . $stmt->error);
        $error_message = "Failed to add vaccination details. Please try again later.";
        $_SESSION['vaccination_add_error'] = $error_message;
        $stmt->close();
        header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
        exit();
    }

    $stmt->close();

    // Success
    $_SESSION['vaccination_add_success'] = "Vaccination details added successfully!";
    header("Location: ../mw-vaccination-details.php?id=" . urlencode($momNIC));
    exit();

} catch (Exception $e) {
    error_log('Vaccination add error: ' . $e->getMessage());
    $error_message = "Failed to add vaccination details. Please try again later.";
    $_SESSION['vaccination_add_error'] = $error_message;
    $momNIC = $_POST['mama-nic'] ?? "";
    header("Location: ../mw-vaccination-details.php" . (!empty($momNIC) ? "?id=" . urlencode($momNIC) : ""));
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>