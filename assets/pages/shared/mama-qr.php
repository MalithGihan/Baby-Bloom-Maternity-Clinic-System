<?php
session_start();

//Adding php qr code api file
include('../qr-api/phpqrcode/qrlib.php');

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
    echo '<script>alert("Invalid NIC format."); window.location.href="../dashboard/mama-dashboard.php";</script>';
    exit();
}

// Use absolute path and ensure directory exists
$baseDir = dirname(__DIR__) . '/images/mama-qr-codes/';
$fileName = $sanitizedNIC . '_qr.png';
$filePath = $baseDir . $fileName;

// Ensure the QR codes directory exists
if (!is_dir($baseDir)) {
    if (!mkdir($baseDir, 0755, true)) {
        error_log("Failed to create QR codes directory: " . $baseDir);
        echo '<script>alert("System error. Please try again."); window.location.href="../dashboard/mama-dashboard.php";</script>';
        exit();
    }
}

// Validate that the final path is within the expected directory
$realBasePath = realpath($baseDir);
$realFilePath = realpath(dirname($filePath)) . '/' . basename($filePath);

if ($realBasePath === false || strpos($realFilePath, $realBasePath) !== 0) {
    error_log("Path traversal attempt detected: " . $filePath);
    echo '<script>alert("Invalid file path."); window.location.href="../dashboard/mama-dashboard.php";</script>';
    exit();
}

try {
    QRcode::png($sanitizedNIC, $filePath, QR_ECLEVEL_L, 8);
} catch (Exception $e) {
    error_log("QR code generation failed: " . $e->getMessage());
    echo '<script>alert("Failed to generate QR code."); window.location.href="../dashboard/mama-dashboard.php";</script>';
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama QR Code</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script rel="script" type="text/js" src="../js/bootstrap.min.js"></script>
        <style>
            .option-name{
                font-family: 'Inter-Bold';
                color:var(--light-txt);
                margin-top:1rem !important;
            }
        </style>
    </head>
<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
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
                        <img src="../images/mama-image.png" alt="User profile image" class="usr-image">
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
                    <!-- <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo $NIC ?>&bgcolor=EFEBEA" class="option-img"> -->
                    <?php
                        //Below code will dynamically change the qr code based on the mother's NIC
                        echo '<img src="../images/mama-qr-codes/'.htmlspecialchars($sanitizedNIC, ENT_QUOTES, 'UTF-8').'_qr.png" class="option-img" alt="QR Code for '.htmlspecialchars($sanitizedNIC, ENT_QUOTES, 'UTF-8').'" />';
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

    <script>
    </script>
</body>
</html>
