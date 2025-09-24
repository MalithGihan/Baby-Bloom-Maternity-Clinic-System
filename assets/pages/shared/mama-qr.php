<?php
// Use secure session initialization
require_once __DIR__ . '/session-init.php';

//Adding php qr code api file
include('../../qr-api/phpqrcode/qrlib.php');

// Validate session first
if (!isset($_SESSION["mamaEmail"]) || !isset($_SESSION["NIC"])) {
    header("Location: ../auth/mama-login.php");
    exit();
}

$NIC = $_SESSION["NIC"];

// Validate and sanitize NIC input to prevent path traversal
function validateNIC($nic) {
    // Remove any potential path traversal characters
    $nic = str_replace(['/', '\\', '..', '.', '<', '>', ':', '"', '|', '?', '*'], '', $nic);

    // Only allow alphanumeric characters and hyphens for NIC
    $nic = preg_replace('/[^a-zA-Z0-9\-]/', '', $nic);

    // Limit length to prevent excessively long filenames
    $nic = substr($nic, 0, 20);

    // Ensure NIC is not empty after sanitization
    if (empty($nic)) {
        return false;
    }

    return $nic;
}

$sanitizedNIC = validateNIC($NIC);

if ($sanitizedNIC === false) {
    error_log("Invalid NIC detected for QR generation: " . $NIC);
    $_SESSION['error_message'] = "Invalid NIC format.";
    header("Location: ../dashboard/mama-dashboard.php");
    exit();
}

// Use correct path to assets/images/mama-qr-codes/
$baseDir = dirname(__DIR__, 2) . '/images/mama-qr-codes/';
$fileName = $sanitizedNIC . '_qr.png';
$filePath = $baseDir . $fileName;

// Ensure the QR codes directory exists
if (!is_dir($baseDir)) {
    if (!mkdir($baseDir, 0755, true)) {
        error_log("Failed to create QR codes directory: " . $baseDir);
        $_SESSION['error_message'] = "System error. Please try again.";
        header("Location: ../dashboard/mama-dashboard.php");
        exit();
    }
}

// Validate that the final path is within the expected directory
$realBasePath = realpath($baseDir);
$realFilePath = realpath(dirname($filePath)) . '/' . basename($filePath);

if ($realBasePath === false || strpos($realFilePath, $realBasePath) !== 0) {
    error_log("Path traversal attempt detected: " . $filePath);
    $_SESSION['error_message'] = "Invalid file path.";
    header("Location: ../dashboard/mama-dashboard.php");
    exit();
}

// Always use online QR generation since it doesn't require server-side dependencies
$useOnlineQR = true;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama QR Code</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
    </head>
<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
            <div class="d-flex flex-column">
                <h1 class="common-title">BabyBloom</h1>
                <h3 class="common-description">Maternity Clinic System</h3>
            </div>
        </header>
        <main>
            <div class="main-header d-flex">
                <h2 class="main-header-title">YOUR QR CODE</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../../images/mama-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo htmlspecialchars($_SESSION['First_name'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($_SESSION['Last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="useremail"><?php echo htmlspecialchars($_SESSION['mamaEmail'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="../auth/mama-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex">
                <div class="d-flex flex-column align-items-center">
                    <?php
                        // Use online QR generation (reliable and no server dependencies)
                        echo '<img src="https://api.qrserver.com/v1/create-qr-code/?data='.urlencode($sanitizedNIC).'&bgcolor=EFEBEA&size=200x200" class="option-img" alt="QR Code for '.htmlspecialchars($sanitizedNIC, ENT_QUOTES, 'UTF-8').'" />';
                    ?>
                    <p class="option-name">Present this QR code when needed.</p>
                </div>  
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../dashboard/mama-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

</body>
</html>
