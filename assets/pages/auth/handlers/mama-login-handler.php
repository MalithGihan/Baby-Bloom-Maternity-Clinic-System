<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION["mamaEmail"])) {
    header("Location: ../../dashboard/mama-dashboard.php");
    exit();
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../mama-login.php");
    exit();
}

require_once __DIR__ . "/../../shared/db-access.php";

$error_message = "";

try {
    // Validate input
    $mamaEmail = trim($_POST["mama-email"] ?? "");
    $mamaPass  = $_POST["mama-password"] ?? "";


    if ($mamaEmail === "" || $mamaPass === "") {
        $_SESSION['login_error'] = "Please fill in all fields.";
        header("Location: ../mama-login.php");
        exit();
    }

    // Fetch just what we need (order must match bind_result)
    $sql = "SELECT 
                NIC, registered_date, firstName, middleName, surname, DOB, birthplace, LRMP, 
                address, phoneNumber, health_conditions, allergies, rubella_status, maritalStatus, 
                blood_relativity, husbandName, husbandOccupation, husband_phone, husband_dob, 
                husband_birthplace, husband_healthconditions, husband_allergies, email, password, google_id
            FROM pregnant_mother
            WHERE email = ?";

    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $_SESSION['login_error'] = "System error. Please try again later.";
        header("Location: ../mama-login.php");
        exit();
    }

    $stmt->bind_param("s", $mamaEmail);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows === 1) {
        // Bind results
        $stmt->bind_result(
            $mamaNIC, $mamaRegDate, $mamaFname, $mamaMname, $mamaSname,
            $mamaBday, $mamaBplace, $mamaLRMP, $mamaAdd, $mamaPhone,
            $mamaHealthCond, $mamaAllergies, $mamaRubellaState, $mamaMstate,
            $mamaBloodRel, $mamaHubname, $mamaHubocc, $mamaHubPhone,
            $mamaHubDOB, $mamaHubBirthplace, $mamaHubHealthCond,
            $mamaHubAllergies, $mamaGetEmail, $mamaGetPss, $mamaGoogleId
        );
        $stmt->fetch();

        // Note: Accounts can login with either password or Google OAuth
        // To restrict Google-linked accounts to OAuth only, uncomment below:
        // if (!empty($mamaGoogleId)) {
        //     $_SESSION['login_error'] = "This account is linked to Google. Please use 'Continue with Google' to sign in.";
        //     header("Location: ../mama-login.php");
        //     exit();
        // }

        // Password verification
        if (password_verify($mamaPass, $mamaGetPss)) {
            $_SESSION["loggedin"]   = true;
            $_SESSION["NIC"]        = $mamaNIC;
            $_SESSION["mamaEmail"]  = $mamaGetEmail;
            $_SESSION['First_name'] = $mamaFname;
            $_SESSION['Last_name']  = $mamaSname;

            unset($_SESSION['login_error']);

            header("Location: ../../dashboard/mama-dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password. Please try again.";
        }
    } else {
        $_SESSION['login_error'] = "No user with that email address found.";
    }

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['login_error'] = "System error. Please try again later.";
} finally {
    if (isset($stmt)) { $stmt->close(); }
    if (isset($con))  { $con->close(); }
}

// Redirect back to login with error (if any)
header("Location: ../mama-login.php");
exit();
