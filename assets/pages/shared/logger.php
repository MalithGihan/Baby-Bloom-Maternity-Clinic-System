<?php
/**
 * Centralized Logging Utility for BabyBloom
 * Provides fail-safe logging that never blocks application functionality
 */

class Logger {
    private static $logFile;
    private static $initialized = false;

    /**
     * Initialize the logger with the correct path
     */
    private static function init() {
        if (self::$initialized) {
            return;
        }

        // Calculate path to project root logs directory
        self::$logFile = dirname(__DIR__, 3) . '/logs/system_log.log';
        self::$initialized = true;

        // Ensure logs directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        // Ensure log file exists and is writable
        if (!file_exists(self::$logFile)) {
            @touch(self::$logFile);
            @chmod(self::$logFile, 0666);
        }
    }

    /**
     * Log a message - fail-safe, never throws exceptions
     * @param string $message The message to log
     * @param string $level Log level (INFO, WARN, ERROR)
     */
    public static function log($message, $level = 'INFO') {
        try {
            self::init();

            $timestamp = date('Y-m-d H:i:s');
            $formattedMessage = "[$timestamp] [$level] $message\n";

            // Attempt to write to log file
            $success = @error_log($formattedMessage, 3, self::$logFile);

            // If file logging fails, log to system error log as fallback
            if (!$success) {
                @error_log("BabyBloom: $message");
            }

        } catch (Exception $e) {
            // Logging should never break the application
            // Silently fail or log to system log if possible
            @error_log("Logger error: " . $e->getMessage());
        }
    }

    /**
     * Log an informational message
     */
    public static function info($message) {
        self::log($message, 'INFO');
    }

    /**
     * Log a warning message
     */
    public static function warn($message) {
        self::log($message, 'WARN');
    }

    /**
     * Log an error message
     */
    public static function error($message) {
        self::log($message, 'ERROR');
    }

    /**
     * Log a login attempt
     */
    public static function loginAttempt($email, $status, $userType = 'user') {
        self::log("Login attempt - Email: $email, Status: $status, Type: $userType", 'INFO');
    }

    /**
     * Log a registration event
     */
    public static function registration($email, $status, $userType = 'user') {
        self::log("Registration - Email: $email, Status: $status, Type: $userType", 'INFO');
    }
}

// Convenience functions for backward compatibility
function logToFile($message) {
    Logger::info($message);
}

function logLoginAttempt($email, $status) {
    Logger::loginAttempt($email, $status);
}

function logRegistrationEvent($message) {
    Logger::info($message);
}
?>