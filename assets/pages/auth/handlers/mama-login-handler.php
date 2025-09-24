<?php
// Use secure session start for login pages
require_once __DIR__ . '/../../shared/secure-session-start.php';

// Include centralized logger and database connection
require_once __DIR__ . '/../../shared/logger.php';
require_once __DIR__ . "/../../shared/db-access.php";

// CSRF validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['login_error'] = "Invalid request. Please try again.";
    header("Location: ../mama-login.php");
    exit();
}

// Rate limiting check - USE 30 SECONDS FOR TESTING
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

$lockoutDuration = 30; // 30 seconds for testing

error_log("HANDLER DEBUG: Current login_attempts = " . $_SESSION['login_attempts']);
error_log("HANDLER DEBUG: Current last_attempt_time = " . $_SESSION['last_attempt_time']);

// Check if user is locked out and handle lockout expiration
if ($_SESSION['login_attempts'] >= 3) {
    $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];
    error_log("HANDLER DEBUG: timeSinceLastAttempt = " . $timeSinceLastAttempt);
    
    if ($timeSinceLastAttempt < $lockoutDuration) {
        // Still locked out
        $remainingTime = $lockoutDuration - $timeSinceLastAttempt;
        $_SESSION['login_error'] = "Too many failed login attempts. Please try again in " . $remainingTime . " seconds.";
        error_log("HANDLER DEBUG: User is locked out. Redirecting.");
        header("Location: ../mama-login.php");
        exit();
    } else {
        // Lockout period has passed, reset attempts completely
        error_log("HANDLER DEBUG: Lockout period passed. Resetting attempts completely.");
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['last_attempt_time']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['mama-email'] ?? '');
    $password = $_POST['mama-password'] ?? '';

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
        $_SESSION['login_error'] = "Please enter both email and password.";
        logToFile("Failed login attempt: Empty fields. Email: $email");
        error_log("HANDLER DEBUG: Empty fields. Attempts now: " . $_SESSION['login_attempts']);
        header("Location: ../mama-login.php");
        exit();
    }

    try {
        // Fetch user from the database
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
            logToFile("Database prepare failed: " . $con->error);
            $_SESSION['login_error'] = "System error. Please try again later.";
            header("Location: ../mama-login.php");
            exit();
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // Bind results
            $stmt->bind_result( $mamaNIC, $mamaRegDate, $mamaFname, $mamaMname, $mamaSname,
            $mamaBday, $mamaBplace, $mamaLRMP, $mamaAdd, $mamaPhone,
            $mamaHealthCond, $mamaAllergies, $mamaRubellaState, $mamaMstate,
            $mamaBloodRel, $mamaHubname, $mamaHubocc, $mamaHubPhone,
            $mamaHubDOB, $mamaHubBirthplace, $mamaHubHealthCond,
            $mamaHubAllergies, $mamaGetEmail, $mamaGetPss, $mamaGoogleId);
            $stmt->fetch();

            if (!empty($mamaGoogleId)) {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                $_SESSION['login_error'] = "This account is linked to Google. Please use 'Continue with Google' to sign in.";
                logToFile("Failed login attempt: Google-linked account. Email: $email");
                error_log("HANDLER DEBUG: Google account. Attempts now: " . $_SESSION['login_attempts']);
                header("Location: ../mama-login.php");
                exit();
            }

            if (password_verify($password, $mamaGetPss)) {
                unset($_SESSION['login_attempts']);
                unset($_SESSION['last_attempt_time']);
                unset($_SESSION['login_error']); 

                session_regenerate_id(true);

                $_SESSION["loggedin"] = true;
                $_SESSION["NIC"] = $mamaNIC;
                $_SESSION["mamaEmail"] = $mamaGetEmail;
                $_SESSION['First_name'] = $mamaFname;
                $_SESSION['Last_name'] = $mamaSname;
                
                // Add session security metadata
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

                logToFile("Successful login for mama: $mamaGetEmail");
                error_log("HANDLER DEBUG: Login successful. Clearing all attempt data.");

                header("Location: ../../dashboard/mama-dashboard.php");
                exit();
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                
                $attemptsLeft = 3 - $_SESSION['login_attempts'];
                if ($attemptsLeft > 0) {
                    $_SESSION['login_error'] = "Invalid email or password. {$attemptsLeft} attempts remaining.";
                }
                
                logToFile("Failed login attempt: Incorrect password. Email: $email");
                error_log("HANDLER DEBUG: Wrong password. Attempts now: " . $_SESSION['login_attempts']);
                header("Location: ../mama-login.php");
                exit();
            }
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            
            $attemptsLeft = 3 - $_SESSION['login_attempts'];
            if ($attemptsLeft > 0) {
                $_SESSION['login_error'] = "Invalid email or password. {$attemptsLeft} attempts remaining.";
            } else {
                $_SESSION['login_error'] = "Too many failed attempts. Account locked for 30 seconds.";
            }
            
            logToFile("Failed login attempt: No user found with email: $email");
            error_log("HANDLER DEBUG: User not found. Attempts now: " . $_SESSION['login_attempts']);
            header("Location: ../mama-login.php");
            exit();
        }

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = "System error. Please try again later.";
        logToFile("Login system error for email: $email - " . $e->getMessage());
        header("Location: ../mama-login.php");
        exit();
    }
} else {
    // Redirect back to login with error (if any)
    header("Location: ../mama-login.php");
    exit();
}
?>
