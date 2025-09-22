<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION["staffEmail"])) {
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../staff-login.php");
    exit();
}

include '../../shared/db-access.php';

// Initialize error variable
$error_message = "";

try {
    // Get and validate input
    $staffEmail = trim($_POST["staff-email"] ?? "");
    $staffPass = $_POST["staff-password"] ?? "";

    if (empty($staffEmail) || empty($staffPass)) {
        $error_message = "Please fill in all fields.";
        $_SESSION['login_error'] = $error_message;
        header("Location: ../staff-login.php");
        exit();
    }

    // Prepare and execute query
    $sql = "SELECT * FROM staff WHERE email = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['login_error'] = $error_message;
        header("Location: ../staff-login.php");
        exit();
    }

    $stmt->bind_param("s", $staffEmail);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        // Bind result variables
        $stmt->bind_result($staffID, $staffFname, $staffMname, $staffSname, $staffAdd,
                          $staffDOB, $staffNIC, $staffGender, $staffPhone, $staffPosition,
                          $staffGetEmail, $staffGetPss);
        $stmt->fetch();

        // Verify password
        if (password_verify($staffPass, $staffGetPss)) {
            // Password correct - create session
            $_SESSION["loggedin"] = true;
            $_SESSION["staffID"] = $staffID;
            $_SESSION["staffNIC"] = $staffNIC;
            $_SESSION["staffEmail"] = $staffGetEmail;
            $_SESSION['staffFName'] = $staffFname;
            $_SESSION['staffSName'] = $staffSname;
            $_SESSION['staffPosition'] = $staffPosition;

            // Clear any previous errors
            unset($_SESSION['login_error']);

            // Redirect to dashboard
            header("Location: ../dashboard/staff-dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "No user with that email address found.";
    }

} catch (Exception $e) {
    error_log('Staff login error: ' . $e->getMessage());
    $error_message = "System error. Please try again later.";
} finally {
    // Clean up database resources
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($con)) {
        $con->close();
    }
}

// Store error and redirect back to login page
if (!empty($error_message)) {
    $_SESSION['login_error'] = $error_message;
}

header("Location: ../staff-login.php");
exit();
?>