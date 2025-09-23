<?php
session_start();

function logLogoutEvent($message) {
    $logMessage = date('Y-m-d H:i:s') . " | $message\n";
    error_log($logMessage, 3, __DIR__ . "/../../logs/system_log.log");
}

// Clear session data
session_unset();
session_destroy();

logLogoutEvent('Staff user logged out');

// Redirect to login page
header("Location: staff-login.php");
exit();
?>