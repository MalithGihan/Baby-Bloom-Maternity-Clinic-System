<?php
session_start();

// Log function for password update process
function logToFile($message) {
    $logMessage = date('Y-m-d H:i:s') . " | $message\n";
    error_log($logMessage, 3, __DIR__ . "/../../../logs/system_log.log");
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    logToFile("Invalid request method for password update.");
    header("Location: ../staff-profile.php");
    exit();
}

include '../../shared/db-access.php';

// Check if user is logged in as staff
if (!isset($_SESSION["staffEmail"])) {
    logToFile("Unauthorized access attempt to staff profile by user not logged in.");
    header("Location: ../../auth/staff-login.php");
    exit();
}

// Initialize variables
$error_message = "";
$success_message = "";

try {
    // Get and validate input data
    $staffPass = $_POST['staff-pass'] ?? "";
    $staffRePass = $_POST['staff-repass'] ?? "";
    $staffID = $_SESSION["staffID"];

    // Basic validation
    if (empty($staffPass) || empty($staffRePass)) {
        $error_message = "Please fill in all fields.";
        $_SESSION['password_update_error'] = $error_message;
        logToFile("Password update failed: Missing fields. StaffID: $staffID");
        header("Location: ../staff-profile.php#password-reset");
        exit();
    }

    // Password validation
    if (strlen($staffPass) < 6) {
        $error_message = "Password must be at least 6 characters long.";
        $_SESSION['password_update_error'] = $error_message;
        logToFile("Password update failed: Password too short. StaffID: $staffID");
        header("Location: ../staff-profile.php#password-reset");
        exit();
    }

    // Check if passwords match
    if ($staffPass !== $staffRePass) {
        $error_message = "New password and re-entered password do not match. Please check both passwords again.";
        $_SESSION['password_update_error'] = $error_message;
        logToFile("Password update failed: Password mismatch. StaffID: $staffID");
        header("Location: ../staff-profile.php#password-reset");
        exit();
    }

    // Hash the new password
    $staffHashPass = password_hash($staffPass, PASSWORD_ARGON2ID);

    // Update password in database
    $sql = "UPDATE staff SET password = ? WHERE staffID = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        logToFile("Database prepare failed for password update. StaffID: $staffID, Error: " . $con->error);
        error_log('Database prepare failed for password update: ' . $con->error);
        $error_message = "Failed to update password. Please try again later.";
        $_SESSION['password_update_error'] = $error_message;
        header("Location: ../staff-profile.php#password-reset");
        exit();
    }

    $stmt->bind_param("si", $staffHashPass, $staffID);

    if (!$stmt->execute()) {
        logToFile("Failed to update password. StaffID: $staffID, Error: " . $stmt->error);
        error_log('Failed to update password: ' . $stmt->error);
        $error_message = "Failed to update password. Please try again later.";
        $_SESSION['password_update_error'] = $error_message;
        $stmt->close();
        header("Location: ../staff-profile.php#password-reset");
        exit();
    }

    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        $_SESSION['password_update_success'] = "Your password has been updated successfully!";
        logToFile("Password updated successfully. StaffID: $staffID");
        header("Location: ../staff-profile.php");
        exit();
    } else {
        $error_message = "Failed to update password. Please contact the system administrator.";
        $_SESSION['password_update_error'] = $error_message;
        logToFile("Password update failed. No rows affected. StaffID: $staffID");
        header("Location: ../staff-profile.php#password-reset");
        exit();
    }

    $stmt->close();

} catch (Exception $e) {
    logToFile("Exception occurred during password update. StaffID: $staffID, Error: " . $e->getMessage());
    error_log('Password update error: ' . $e->getMessage());
    $error_message = "Failed to update password. Please try again later.";
    $_SESSION['password_update_error'] = $error_message;
    header("Location: ../staff-profile.php#password-reset");
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>
