<?php
// Use secure session start for login handlers
require_once __DIR__ . '/../../shared/secure-session-start.php';

// Include centralized logger
require_once __DIR__ . '/../../shared/logger.php';

// Redirect if already logged in
if (isset($_SESSION["staffEmail"])) {
    logToFile("Staff already logged in, redirecting to staff dashboard: {$_SESSION['staffEmail']}");
    header("Location: ../../dashboard/staff-dashboard.php");
    exit();
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    logToFile("Invalid request method for staff login attempt.");
    header("Location: ../staff-login.php");
    exit();
}

require_once __DIR__ . '/../../shared/db-access.php';

// Initialize error variable
$error_message = "";

try {
    // Get and validate input
    $staffEmail = trim($_POST["staff-email"] ?? "");
    $staffPass  = $_POST["staff-password"] ?? "";

    if ($staffEmail === "" || $staffPass === "") {
        $_SESSION['login_error'] = "Please fill in all fields.";
        logToFile("Failed login attempt: Missing email or password. Email: $staffEmail");
        header("Location: ../staff-login.php");
        exit();
    }

    // Safer: fetch only the fields we actually need, via assoc
    $sql  = "SELECT staffID, NIC, firstName, surname, position, email, password, google_id 
             FROM staff WHERE email = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        logToFile("Database prepare failed for staff login. Error: " . $con->error);
        $_SESSION['login_error'] = "System temporarily unavailable. Please try again later.";
        header("Location: ../staff-login.php");
        exit();
    }

    $stmt->bind_param("s", $staffEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $dbPassword  = $row['password'] ?? '';
        $dbGoogleId  = $row['google_id'] ?? '';

        // If the account is linked to Google and the provided password is wrong,
        // instruct the user to use Google Sign-In (matches your mama flow).
        if (!empty($dbGoogleId) && !password_verify($staffPass, $dbPassword)) {
            $_SESSION['login_error'] = "This account is linked to Google. Please use 'Continue with Google' to login.";
            logToFile("Failed login attempt: Google-linked account with incorrect password. Email: $staffEmail");
            header("Location: ../staff-login.php");
            exit();
        }

        if (password_verify($staffPass, $dbPassword)) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Password correct - create session
            $_SESSION["loggedin"]     = true;
            $_SESSION["staffID"]      = $row['staffID'];
            $_SESSION["staffNIC"]     = $row['NIC'];
            $_SESSION["staffEmail"]   = $row['email'];
            $_SESSION['staffFName']   = $row['firstName'];
            $_SESSION['staffSName']   = $row['surname'];
            $_SESSION['staffPosition']= $row['position'];

            // Add session security metadata
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

            unset($_SESSION['login_error']); // clear any previous errors

            logToFile("Successful login for staff: $staffEmail");

            header("Location: ../../dashboard/staff-dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password. Please try again.";
            logToFile("Failed login attempt: Incorrect password. Email: $staffEmail");
        }
    } else {
        $_SESSION['login_error'] = "No user with that email address found.";
        logToFile("Failed login attempt: No user found with email: $staffEmail");
    }

} catch (Throwable $e) {
    error_log('Staff login error: ' . $e->getMessage());
    logToFile("Exception during staff login: " . $e->getMessage());
    $_SESSION['login_error'] = "System temporarily unavailable. Please try again later.";
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($con) && $con instanceof mysqli) {
        $con->close();
    }
}

// Back to login with message (if any)
header("Location: ../staff-login.php");
exit();
