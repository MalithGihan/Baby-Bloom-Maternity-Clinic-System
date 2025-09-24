<?php
/**
 * Standalone Database Installation Script for Rate Limiting
 *
 * This script creates the necessary database tables for rate limiting functionality.
 * Run this script once to set up the rate limiting infrastructure.
 */

// Database configuration
$host = 'localhost';
$username = 'babybloom';
$password = 'strong_pass';
$database = 'babybloom';

echo "Installing Rate Limiting Database Schema...\n";
echo "==========================================\n\n";
echo "Connecting to database: $database@$host\n";

// Create connection
$con = new mysqli($host, $username, $password, $database);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error . "\n");
}

echo "✓ Database connection successful\n\n";

// Define SQL commands directly
$sqlCommands = [
    "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(255) NOT NULL,
        attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        attempt_type ENUM('email', 'ip') NOT NULL,
        user_agent TEXT,
        INDEX idx_identifier_time (identifier, attempt_time),
        INDEX idx_attempt_type_time (attempt_type, attempt_time)
    )",

    "CREATE TABLE IF NOT EXISTS account_lockouts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        locked_until TIMESTAMP NOT NULL,
        lockout_reason VARCHAR(100) DEFAULT 'multiple_failed_attempts',
        lockout_count INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email_locked (email, locked_until),
        INDEX idx_locked_until (locked_until)
    )",

    "CREATE TABLE IF NOT EXISTS login_success_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        session_id VARCHAR(255),
        INDEX idx_email_time (email, login_time),
        INDEX idx_ip_time (ip_address, login_time)
    )",

    "CREATE TABLE IF NOT EXISTS security_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_type ENUM('brute_force', 'account_locked', 'suspicious_activity', 'rate_limit_exceeded') NOT NULL,
        email VARCHAR(255),
        ip_address VARCHAR(45),
        event_data JSON,
        severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_event_type_time (event_type, created_at),
        INDEX idx_severity_time (severity, created_at)
    )"
];

$successCount = 0;
$errorCount = 0;

foreach ($sqlCommands as $index => $command) {
    $tableNumber = $index + 1;
    $tableName = '';

    // Extract table name for better output
    if (preg_match('/CREATE TABLE.*?`?(\w+)`?\s*\(/i', $command, $matches)) {
        $tableName = $matches[1];
    }

    echo "Creating table $tableNumber" . ($tableName ? " ($tableName)" : "") . "...\n";

    if ($con->query($command) === TRUE) {
        $successCount++;
        echo "✓ Success\n\n";
    } else {
        $errorCount++;
        echo "✗ Error: " . $con->error . "\n\n";
    }
}

echo "Installation Summary:\n";
echo "====================\n";
echo "Successfully executed: $successCount commands\n";
echo "Failed: $errorCount commands\n\n";

if ($errorCount === 0) {
    echo "✓ Rate limiting database schema installed successfully!\n\n";

    // Verify tables were created
    echo "Verifying table creation:\n";
    echo "========================\n";
    $tables = ['login_attempts', 'account_lockouts', 'login_success_log', 'security_events'];

    foreach ($tables as $table) {
        $result = $con->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' created successfully\n";

            // Show table structure
            $structResult = $con->query("DESCRIBE $table");
            if ($structResult) {
                echo "  Columns: ";
                $columns = [];
                while ($row = $structResult->fetch_assoc()) {
                    $columns[] = $row['Field'];
                }
                echo implode(', ', $columns) . "\n";
            }
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }

    echo "\nTesting database functionality:\n";
    echo "==============================\n";

    // Test insert into login_attempts
    $testResult = $con->query("INSERT INTO login_attempts (identifier, attempt_type) VALUES ('test@example.com', 'email')");
    if ($testResult) {
        echo "✓ Test insert successful\n";
        // Clean up test data
        $con->query("DELETE FROM login_attempts WHERE identifier = 'test@example.com'");
        echo "✓ Test data cleaned up\n";
    } else {
        echo "✗ Test insert failed: " . $con->error . "\n";
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✓ INSTALLATION COMPLETE!\n";
    echo str_repeat("=", 50) . "\n";
    echo "You can now use the rate limiting features.\n";
    echo "Access the security dashboard at:\n";
    echo "/assets/pages/admin/security-dashboard.php\n";

} else {
    echo "⚠ Installation completed with errors.\n";
    echo "Please check the error messages above and fix any issues.\n";
}

$con->close();
?>