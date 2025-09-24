#!/bin/bash

# Rate Limiting Installation Script for BabyBloom
echo "=================================================="
echo "BabyBloom Rate Limiting Database Installation"
echo "=================================================="

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed or not in PATH"
    exit 1
fi

# Check if MySQL is running
if ! command -v mysql &> /dev/null; then
    echo "Warning: MySQL client not found in PATH"
    echo "Make sure MySQL server is running"
fi

# Navigate to the database directory
cd "$(dirname "$0")"

echo "Running database installation script..."
echo "--------------------------------------"

# Run the PHP installation script
php setup_rate_limiting.php

echo
echo "Installation script completed."
echo "Check the output above for any errors."
echo
echo "Next steps:"
echo "1. Test login functionality"
echo "2. Access security dashboard at /admin/security-dashboard.php"
echo "3. Monitor the logs/system_log.log file for rate limiting events"
echo
echo "For troubleshooting, check:"
echo "- Database connection settings"
echo "- MySQL server status"
echo "- PHP error logs"
echo "=================================================="