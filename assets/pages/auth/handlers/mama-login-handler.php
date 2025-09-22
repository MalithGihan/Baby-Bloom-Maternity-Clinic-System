<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION["mamaEmail"])){
    header("Location: ../dashboard/mama-dashboard.php");
    exit();
}

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../mama-login.php");
    exit();
}

include '../../shared/db-access.php';

// Initialize error variable
$error_message = "";

try {
    // Get and validate input
    $mamaEmail = trim($_POST["mama-email"] ?? "");
    $mamaPass = $_POST["mama-password"] ?? "";

    if (empty($mamaEmail) || empty($mamaPass)) {
        $error_message = "Please fill in all fields.";
        $_SESSION['login_error'] = $error_message;
        header("Location: ../mama-login.php");
        exit();
    }

    // Prepare and execute query
    $sql = "SELECT * FROM pregnant_mother WHERE email = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['login_error'] = $error_message;
        header("Location: ../mama-login.php");
        exit();
    }

    $stmt->bind_param("s", $mamaEmail);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        // Bind result variables
        $stmt->bind_result($mamaNIC, $mamaRegDate, $mamaFname, $mamaMname, $mamaSname,
                          $mamaBday, $mamaBplace, $mamaLRMP, $mamaAdd, $mamaPhone,
                          $mamaHealthCond, $mamaAllergies, $mamaRubellaState, $mamaMstate,
                          $mamaBloodRel, $mamaHubname, $mamaHubocc, $mamaHubPhone,
                          $mamaHubDOB, $mamaHubBirthplace, $mamaHubHealthCond,
                          $mamaHubAllergies, $mamaGetEmail, $mamaGetPss);
        $stmt->fetch();

        // Verify password
        if (password_verify($mamaPass, $mamaGetPss)) {
            // Password correct - create session
            $_SESSION["loggedin"] = true;
            $_SESSION["NIC"] = $mamaNIC;
            $_SESSION["mamaEmail"] = $mamaGetEmail;
            $_SESSION['First_name'] = $mamaFname;
            $_SESSION['Last_name'] = $mamaSname;

            // Clear any previous errors
            unset($_SESSION['login_error']);

            // Redirect to dashboard
            header("Location: ../dashboard/mama-dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "No user with that email address found.";
    }

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
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

header("Location: ../mama-login.php");
exit();
?>