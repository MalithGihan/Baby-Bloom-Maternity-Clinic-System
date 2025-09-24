<?php
/**
 * Rate Limiting Configuration for BabyBloom System
 *
 * This file contains all configuration settings for rate limiting and login lockout functionality.
 * Adjust these values based on your security requirements and user patterns.
 */

class RateLimitConfig {
    // Maximum failed attempts before triggering lockouts
    const MAX_ATTEMPTS_PER_EMAIL = 5;      // Max attempts per email address
    const MAX_ATTEMPTS_PER_IP = 20;        // Max attempts per IP address

    // Lockout durations (in seconds)
    const EMAIL_LOCKOUT_DURATION = 900;    // 15 minutes for email-based lockout
    const IP_LOCKOUT_DURATION = 3600;      // 1 hour for IP-based lockout

    // Time windows
    const ATTEMPT_WINDOW = 900;            // 15 minutes - window to count attempts
    const CLEANUP_INTERVAL = 3600;         // 1 hour - how often to clean old records

    // Progressive delays
    const ENABLE_PROGRESSIVE_DELAY = true;
    const MIN_DELAY = 1;                   // Minimum delay in seconds
    const MAX_DELAY = 10;                  // Maximum delay in seconds

    // CAPTCHA settings
    const CAPTCHA_THRESHOLD_EMAIL = 3;     // Show CAPTCHA after 3 email attempts
    const CAPTCHA_THRESHOLD_IP = 10;       // Show CAPTCHA after 10 IP attempts

    // Security alert thresholds
    const ALERT_THRESHOLD_EMAIL = 3;       // Send alert after 3 failed attempts
    const ALERT_THRESHOLD_IP = 15;         // Send alert after 15 IP attempts

    // Account lockout escalation
    const MAX_LOCKOUT_COUNT = 3;           // After 3 lockouts, require admin unlock
    const ESCALATED_LOCKOUT_DURATION = 7200; // 2 hours for repeated lockouts

    // Environment-specific settings
    public static function getConfig() {
        // You can adjust these based on environment (dev/prod)
        $config = [
            'max_email_attempts' => self::MAX_ATTEMPTS_PER_EMAIL,
            'max_ip_attempts' => self::MAX_ATTEMPTS_PER_IP,
            'email_lockout_duration' => self::EMAIL_LOCKOUT_DURATION,
            'ip_lockout_duration' => self::IP_LOCKOUT_DURATION,
            'attempt_window' => self::ATTEMPT_WINDOW,
            'enable_progressive_delay' => self::ENABLE_PROGRESSIVE_DELAY,
            'captcha_threshold_email' => self::CAPTCHA_THRESHOLD_EMAIL,
            'captcha_threshold_ip' => self::CAPTCHA_THRESHOLD_IP
        ];

        // Override for development environment
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $config['max_email_attempts'] = 10;  // More lenient for development
            $config['email_lockout_duration'] = 300; // 5 minutes for dev
        }

        return $config;
    }

    /**
     * Get human-readable duration string
     */
    public static function getDurationString($seconds) {
        if ($seconds < 60) {
            return $seconds . ' second' . ($seconds != 1 ? 's' : '');
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' minute' . ($minutes != 1 ? 's' : '');
        } else {
            $hours = floor($seconds / 3600);
            return $hours . ' hour' . ($hours != 1 ? 's' : '');
        }
    }
}
?>