<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . '/../../shared/db-access.php';
include 'google-oauth-config.php';

// --- Helper: decode and validate state ---
function decode_state(string $stateRaw): array|false {
    $decoded = json_decode(base64_decode(strtr($stateRaw, '-_', '+/')), true);
    return is_array($decoded) ? $decoded : false;
}

if (!isset($_GET['code'])) {
    error_log("No authorization code received in callback");

    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        error_log("OAuth error: " . $error);
        echo '<script>alert("OAuth authorization failed: ' . htmlspecialchars($error) . '"); window.location.href="index.php";</script>';
    } else {
        error_log("No code parameter in callback");
        echo '<script>alert("OAuth authorization failed. Please try again."); window.location.href="index.php";</script>';
    }
    exit();
}

// Validate state (CSRF + userType)
$userType = 'mama'; // default
if (!isset($_GET['state'])) {
    error_log("Missing state in OAuth callback");
    echo '<script>alert("Invalid OAuth response (missing state)."); window.location.href="index.php";</script>';
    exit();
}
$state = decode_state($_GET['state']);
if ($state === false || empty($state['csrf']) || empty($_SESSION['oauth_csrf']) || !hash_equals($_SESSION['oauth_csrf'], $state['csrf'])) {
    error_log("Invalid state in OAuth callback");
    echo '<script>alert("Invalid OAuth response (state check failed)."); window.location.href="index.php";</script>';
    exit();
}
if (!empty($state['userType']) && in_array($state['userType'], ['mama', 'staff'], true)) {
    $userType = $state['userType'];
}
// Optional: expire old state (e.g., >10 mins)
if (!empty($state['ts']) && (time() - (int)$state['ts'] > 600)) {
    error_log("Expired state in OAuth callback");
    echo '<script>alert("OAuth session expired. Please try again."); window.location.href="index.php";</script>';
    exit();
}

unset($_SESSION['oauth_csrf']);

// All good—proceed to token + userinfo
try {
    $oauth = new GoogleOAuth();
    $tokenData = $oauth->getAccessToken($_GET['code']);

    if (!$tokenData || !isset($tokenData['access_token'])) {
        error_log("Failed to get access token. Response: " . print_r($tokenData, true));
        echo '<script>alert("Failed to get access token. Please try again."); window.location.href="index.php";</script>';
        exit();
    }

    $userInfo = $oauth->getUserInfo($tokenData['access_token']);

    if (!$userInfo || !isset($userInfo['email'])) {
        error_log("Failed to get user info from Google. Response: " . print_r($userInfo, true));
        echo '<script>alert("Failed to get user information from Google. Please try again."); window.location.href="index.php";</script>';
        exit();
    }

    if ($userType === 'staff') {
        handleStaffOAuth($userInfo, $con);
    } else {
        handleMamaOAuth($userInfo, $con);
    }

} catch (Exception $e) {
    error_log("Exception in OAuth callback: " . $e->getMessage());
    echo '<script>alert("An error occurred during authentication. Please try again."); window.location.href="index.php";</script>';
    exit();
}

function handleStaffOAuth(array $userInfo, mysqli $con): void {
    $sql = "SELECT * FROM staff WHERE email = ? OR google_id = ?";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $con->error);
        die('prepare() failed: ' . htmlspecialchars($con->error));
    }

    $googleId = $userInfo['sub'] ?? $userInfo['id'] ?? '';
    $stmt->bind_param("ss", $userInfo['email'], $googleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $staff = $result->fetch_assoc();

        if (empty($staff['google_id'])) {
            $updateSql = "UPDATE staff SET google_id = ? WHERE email = ?";
            $updateStmt = $con->prepare($updateSql);
            if ($updateStmt) {
                $updateStmt->bind_param("ss", $googleId, $userInfo['email']);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        $_SESSION["loggedin"]    = true;
        $_SESSION["staffID"]     = $staff['staffID'];
        $_SESSION["staffNIC"]    = $staff['NIC'];
        $_SESSION["staffEmail"]  = $staff['email'];
        $_SESSION['staffFName']  = $staff['firstName'];
        $_SESSION['staffSName']  = $staff['surname'];
        $_SESSION['staffPosition']= $staff['position'];

        header("Location: ../../dashboard/staff-dashboard.php");
        exit();
    } else {
        echo '<script>alert("No staff account found with this Google account. Please contact administrator."); window.location.href="staff-login.php";</script>';
        exit();
    }
}

function handleMamaOAuth(array $userInfo, mysqli $con): void {
    $sql = "SELECT * FROM pregnant_mother WHERE email = ? OR google_id = ?";
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($con->error));
    }

    $googleId = $userInfo['sub'] ?? $userInfo['id'] ?? '';
    $stmt->bind_param("ss", $userInfo['email'], $googleId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (empty($user['google_id'])) {
            $updateSql = "UPDATE pregnant_mother SET google_id = ? WHERE email = ?";
            $updateStmt = $con->prepare($updateSql);
            if ($updateStmt) {
                $updateStmt->bind_param("ss", $googleId, $userInfo['email']);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        $_SESSION["loggedin"]   = true;
        $_SESSION["NIC"]        = $user['NIC'];
        $_SESSION["mamaEmail"]  = $user['email'];
        $_SESSION['First_name'] = $user['firstName'];
        $_SESSION['Last_name']  = $user['surname'];

        header("Location: ../../dashboard/mama-dashboard.php");
        exit();
    } else {
        $firstName = $userInfo['given_name'] ?? '';
        $lastName  = $userInfo['family_name'] ?? '';
        if (empty($firstName) && isset($userInfo['name'])) {
            $names = explode(' ', $userInfo['name'], 2);
            $firstName = $names[0] ?? '';
            $lastName  = $names[1] ?? '';
        }

        $_SESSION['google_user_data'] = [
            'googleId'       => $googleId,
            'email'          => $userInfo['email'],
            'firstName'      => $firstName,
            'lastName'       => $lastName,
            'profilePicture' => $userInfo['picture'] ?? null
        ];

        header("Location: ../mama-registration.php?oauth=google");
        exit();
    }
}

$con->close();
