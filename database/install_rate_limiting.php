<?php
/**
 * Database Installation Script for Rate Limiting
 *
 * This script creates the necessary database tables for rate limiting functionality.
 * Run this script once to set up the rate limiting infrastructure.
 */

// Load environment variables and database configuration
require_once __DIR__ . '/../assets/pages/shared/db-access.php';

echo "Installing Rate Limiting Database Schema...\n";
echo "==========================================\n\n";

// Read and execute SQL file
$sqlFile = __DIR__ . '/rate_limiting_schema.sql';

if (!file_exists($sqlFile)) {
    die("Error: SQL schema file not found at: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    die("Error: Could not read SQL schema file\n");
}

// Split SQL commands by semicolon (simple approach)
$commands = explode(';', $sql);
$successCount = 0;
$errorCount = 0;

foreach ($commands as $command) {
    $command = trim($command);

    // Skip empty commands
    if (empty($command) || $command === '') {
        continue;
    }

    // Skip comments
    if (strpos($command, '--') === 0) {
        continue;
    }

    echo "Executing: " . substr($command, 0, 60) . "...\n";

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
    echo "✓ Rate limiting database schema installed successfully!\n";

    // Verify tables were created
    echo "\nVerifying table creation:\n";
    $tables = ['login_attempts', 'account_lockouts', 'login_success_log', 'security_events'];

    foreach ($tables as $table) {
        $result = $con->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "✓ Table '$table' created successfully\n";
        } else {
            echo "✗ Table '$table' not found\n";
        }
    }

    // Insert some initial test data for demonstration
    echo "\nInserting test configuration data...\n";

    // You can add any initial configuration or test data here if needed
    echo "✓ Installation complete!\n";

} else {
    echo "⚠ Installation completed with errors. Please check the error messages above.\n";
}

$con->close();
?>