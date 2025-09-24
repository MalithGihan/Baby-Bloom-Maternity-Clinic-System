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

        // Try multiple possible log locations
        $possiblePaths = [
            dirname(__DIR__, 3) . '/logs/system_log.log',  // Project root
            '/tmp/babybloom_system_log.log',                // Temporary directory
            dirname(__DIR__) . '/logs/system_log.log'       // Shared directory
        ];

        self::$logFile = null;

        foreach ($possiblePaths as $path) {
            $logDir = dirname($path);

            // Try to create directory if it doesn't exist
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }

            // Test if we can write to this location
            if (is_dir($logDir) && is_writable($logDir)) {
                if (!file_exists($path)) {
                    @touch($path);
                    @chmod($path, 0666);
                }

                if (file_exists($path) && is_writable($path)) {
                    self::$logFile = $path;
                    break;
                }
            }
        }

        // If no writable path found, fall back to system error log
        if (self::$logFile === null) {
            self::$logFile = false; // Will trigger system error log fallback
        }

        self::$initialized = true;
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

            // If we have a valid log file, try to write to it
            if (self::$logFile !== false) {
                $success = @error_log($formattedMessage, 3, self::$logFile);

                // If successful, we're done
                if ($success) {
                    return;
                }
            }

            // If file logging failed or no valid file, log to system error log
            @error_log("BabyBloom [$level]: $message");

        } catch (Exception $e) {
            // Logging should never break the application
            // Silently fail or log to system log if possible
            @error_log("BabyBloom Logger error: " . $e->getMessage());
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

    /**
     * Get the current log file path (for debugging)
     */
    public static function getLogFile() {
        self::init();
        return self::$logFile;
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