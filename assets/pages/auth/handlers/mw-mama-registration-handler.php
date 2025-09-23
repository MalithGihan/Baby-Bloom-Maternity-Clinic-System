<?php
session_start();

function logToFile($message) {
    $logMessage = date('Y-m-d H:i:s') . " | $message\n";
    error_log($logMessage, 3, __DIR__ .  "/../../../logs/system_log.log");
}

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    logToFile("Invalid request method for midwife registration.");
    header("Location: ../mw-mama-registration.php");
    exit();
}

include '../../shared/db-access.php';

// Initialize variables
$error_message = "";
$success_message = "";

try {
    // Get and validate input data
    $mamaFname = trim($_POST["mom-first-name"] ?? "");
    $mamaMname = trim($_POST["mom-mid-name"] ?? "");
    $mamaSname = trim($_POST["mom-last-name"] ?? "");
    $mamaBday = $_POST["mom-bday"] ?? "";
    $mamaBplace = trim($_POST["mom-birthplace"] ?? "");
    $mamaLRMP = $_POST["mom-lrmp"] ?? "";
    $mamaAdd = trim($_POST["mom-address"] ?? "");
    $mamaNIC = trim($_POST["mom-nic"] ?? "");
    $mamaPhone = trim($_POST["mom-phone"] ?? "");
    $mamaHealthCond = trim($_POST["mom-health-conditions"] ?? "");
    $mamaAllergies = trim($_POST["mom-allergies"] ?? "");
    $mamaRubellaStat = $_POST["rbl-status"] ?? "";
    $mamaMstate = $_POST["marital-status"] ?? "";
    $mamaRelativity = $_POST["blood-relativity"] ?? "";
    $mamaHubname = trim($_POST["mom-hub-name"] ?? "");
    $mamaHubocc = trim($_POST["mom-hub-job"] ?? "");
    $mamaHubPhone = trim($_POST["mom-hub-phone"] ?? "");
    $mamaHubDOB = $_POST["mom-hub-bday"] ?? "";
    $mamaHubBirthplace = trim($_POST["mom-hub-birthplace"] ?? "");
    $mamaHubHealthCond = trim($_POST["mama-hub-health-conditions"] ?? "");
    $mamaHubAllergies = trim($_POST["mama-hub-allergies"] ?? "");
    $mamaEmail = trim($_POST["mom-email"] ?? "");
    $mamaPss = $_POST["mom-pwd"] ?? "";
    $mamaRepss = $_POST["mom-repwd"] ?? "";

    // Basic validation
    if (empty($mamaFname) || empty($mamaSname) || empty($mamaBday) ||
        empty($mamaLRMP) || empty($mamaAdd) || empty($mamaNIC) ||
        empty($mamaPhone) || empty($mamaEmail) || empty($mamaPss) || empty($mamaRepss)) {
        $error_message = "Please fill in all required fields.";
        $_SESSION['mw_registration_error'] = $error_message;
        logToFile("Registration error: Missing required fields. Email: $mamaEmail");
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    // Email validation
    if (!filter_var($mamaEmail, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
        $_SESSION['mw_registration_error'] = $error_message;
        logToFile("Registration error: Invalid email format. Email: $mamaEmail");
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    // Password validation
    if ($mamaPss !== $mamaRepss) {
        $error_message = "Passwords do not match!";
        $_SESSION['mw_registration_error'] = $error_message;
        logToFile("Registration error: Passwords do not match. Email: $mamaEmail");
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    if (strlen($mamaPss) < 6) {
        $error_message = "Password must be at least 6 characters long.";
        $_SESSION['mw_registration_error'] = $error_message;
        logToFile("Registration error: Password too short. Email: $mamaEmail");
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    // Check if user already exists
    $presql = "SELECT * FROM pregnant_mother WHERE email = ?";
    $preStmt = $con->prepare($presql);

    if ($preStmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        logToFile("Database prepare failed for checking existing user. Email: $mamaEmail");
        $error_message = "System error. Please try again later.";
        $_SESSION['mw_registration_error'] = $error_message;
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    $preStmt->bind_param("s", $mamaEmail);
    $preStmt->execute();
    $preStmt->store_result();

    if ($preStmt->num_rows > 0) {
        $error_message = "User already registered. Please login using your provided email!";
        $_SESSION['mw_registration_error'] = $error_message;
        logToFile("User already exists: $mamaEmail");
        $preStmt->close();
        header("Location: ../mama-login.php");
        exit();
    }

    $preStmt->close();

    // Start transaction
    $con->autocommit(FALSE);

    // Insert pregnant mother data
    $sql = "INSERT INTO pregnant_mother (NIC, firstName, middleName, surname, DOB, birthplace, LRMP, address, phoneNumber, health_conditions, allergies, rubella_status, maritalStatus, blood_relativity, husbandName, husbandOccupation, husband_phone, husband_dob, husband_birthplace, husband_healthconditions, husband_allergies, email, password) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        $con->rollback();
        error_log('Database prepare failed for pregnant_mother: ' . $con->error);
        logToFile("Database prepare failed for inserting pregnant_mother. Error: " . $con->error);
        $error_message = "Registration failed. Please try again later.";
        $_SESSION['mw_registration_error'] = $error_message;
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    $hashedPassword = password_hash($mamaPss, PASSWORD_ARGON2ID);
    $stmt->bind_param("ssssssssisssssssissssss", $mamaNIC, $mamaFname, $mamaMname,
                     $mamaSname, $mamaBday, $mamaBplace, $mamaLRMP, $mamaAdd, $mamaPhone,
                     $mamaHealthCond, $mamaAllergies, $mamaRubellaStat, $mamaMstate,
                     $mamaRelativity, $mamaHubname, $mamaHubocc, $mamaHubPhone, $mamaHubDOB,
                     $mamaHubBirthplace, $mamaHubHealthCond, $mamaHubAllergies, $mamaEmail,
                     $hashedPassword);

    if (!$stmt->execute()) {
        $con->rollback();
        error_log('Failed to insert pregnant_mother: ' . $stmt->error);
        logToFile("Failed to insert pregnant_mother: " . $stmt->error);
        $error_message = "Registration failed. Please try again later.";
        $_SESSION['mw_registration_error'] = $error_message;
        $stmt->close();
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    $stmt->close();

    // Insert supplement quota
    $quota = 1;
    $sql2 = "INSERT INTO supplement_quota (orderedTimes, NIC) VALUES (?, ?)";
    $stmt2 = $con->prepare($sql2);

    if ($stmt2 === false) {
        $con->rollback();
        error_log('Database prepare failed for supplement_quota: ' . $con->error);
        logToFile("Database prepare failed for supplement_quota: " . $con->error);
        $error_message = "Registration failed. Please try again later.";
        $_SESSION['mw_registration_error'] = $error_message;
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    $stmt2->bind_param("is", $quota, $mamaNIC);

    if (!$stmt2->execute()) {
        $con->rollback();
        error_log('Failed to insert supplement_quota: ' . $stmt2->error);
        logToFile("Failed to insert supplement_quota: " . $stmt2->error);
        $error_message = "Registration failed. Please try again later.";
        $_SESSION['mw_registration_error'] = $error_message;
        $stmt2->close();
        header("Location: ../mw-mama-registration.php");
        exit();
    }

    $stmt2->close();

    // Commit transaction
    $con->commit();

    // Registration successful
    $_SESSION['mw_registration_success'] = "Registration successful! Mother registered successfully.";
    logToFile("Registration successful for mama: $mamaEmail");
    header("Location: ../mw-mama-registration.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con)) {
        $con->rollback();
    }
    logToFile('Midwife registration error: ' . $e->getMessage());
    $error_message = "Registration failed. Please try again later.";
    $_SESSION['mw_registration_error'] = $error_message;
    header("Location: ../mw-mama-registration.php");
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>