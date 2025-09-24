<?php
/**
 * Rate Limiter Class for BabyBloom System
 *
 * This class handles rate limiting, login lockouts, and security monitoring
 * for the BabyBloom maternity clinic management system.
 */

require_once 'rate-limit-config.php';

class RateLimiter {
    private $con;
    private $config;

    public function __construct($connection) {
        $this->con = $connection;
        $this->config = RateLimitConfig::getConfig();
        $this->cleanupOldAttempts();
    }

    /**
     * Check if email/IP is currently rate limited
     *
     * @param string $email User email address
     * @param string $ip Client IP address
     * @return array Rate limit status and message
     */
    public function isRateLimited($email, $ip) {
        // Check account lockout first
        if ($this->isAccountLocked($email)) {
            $lockInfo = $this->getLockoutInfo($email);
            return [
                'limited' => true,
                'type' => 'account_locked',
                'message' => 'Account temporarily locked until ' . date('H:i', strtotime($lockInfo['locked_until'])) . '. Please try again later.',
                'locked_until' => $lockInfo['locked_until']
            ];
        }

        // Check email-based rate limiting
        $emailAttempts = $this->getAttemptCount($email, 'email');
        if ($emailAttempts >= $this->config['max_email_attempts']) {
            $this->lockAccount($email, 'email_limit');
            $this->logSecurityEvent('rate_limit_exceeded', $email, $ip, [
                'attempt_count' => $emailAttempts,
                'limit_type' => 'email'
            ]);

            return [
                'limited' => true,
                'type' => 'email_limit',
                'message' => 'Too many login attempts. Account locked for ' .
                           RateLimitConfig::getDurationString($this->config['email_lockout_duration']) . '.'
            ];
        }

        // Check IP-based rate limiting
        $ipAttempts = $this->getAttemptCount($ip, 'ip');
        if ($ipAttempts >= $this->config['max_ip_attempts']) {
            $this->logSecurityEvent('rate_limit_exceeded', $email, $ip, [
                'attempt_count' => $ipAttempts,
                'limit_type' => 'ip'
            ]);

            return [
                'limited' => true,
                'type' => 'ip_limit',
                'message' => 'Too many login attempts from this location. Please try again in ' .
                           RateLimitConfig::getDurationString($this->config['ip_lockout_duration']) . '.'
            ];
        }

        return [
            'limited' => false,
            'email_attempts' => $emailAttempts,
            'ip_attempts' => $ipAttempts
        ];
    }

    /**
     * Record a failed login attempt
     *
     * @param string $email User email address
     * @param string $ip Client IP address
     * @param string $userAgent User agent string (optional)
     */
    public function recordFailedAttempt($email, $ip, $userAgent = null) {
        // Record email-based attempt
        $stmt = $this->con->prepare("INSERT INTO login_attempts (identifier, attempt_type, user_agent) VALUES (?, 'email', ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $email, $userAgent);
            $stmt->execute();
            $stmt->close();
        }

        // Record IP-based attempt
        $stmt = $this->con->prepare("INSERT INTO login_attempts (identifier, attempt_type, user_agent) VALUES (?, 'ip', ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $ip, $userAgent);
            $stmt->execute();
            $stmt->close();
        }

        // Check if we should trigger security alerts
        $emailAttempts = $this->getAttemptCount($email, 'email');
        $ipAttempts = $this->getAttemptCount($ip, 'ip');

        if ($emailAttempts >= RateLimitConfig::ALERT_THRESHOLD_EMAIL) {
            $this->triggerSecurityAlert($email, $ip, $emailAttempts, 'email');
        }

        if ($ipAttempts >= RateLimitConfig::ALERT_THRESHOLD_IP) {
            $this->triggerSecurityAlert($email, $ip, $ipAttempts, 'ip');
        }

        error_log("Failed login attempt - Email: $email, IP: $ip, Email attempts: $emailAttempts, IP attempts: $ipAttempts");
    }

    /**
     * Record a successful login and clear attempts
     *
     * @param string $email User email address
     * @param string $ip Client IP address
     * @param string $sessionId Session ID (optional)
     * @param string $userAgent User agent string (optional)
     */
    public function recordSuccessfulLogin($email, $ip, $sessionId = null, $userAgent = null) {
        // Log successful login
        $stmt = $this->con->prepare("INSERT INTO login_success_log (email, ip_address, user_agent, session_id) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $email, $ip, $userAgent, $sessionId);
            $stmt->execute();
            $stmt->close();
        }

        // Clear failed attempts
        $this->clearAttempts($email, $ip);

        error_log("Successful login - Email: $email, IP: $ip");
    }

    /**
     * Clear attempts after successful login
     *
     * @param string $email User email address
     * @param string $ip Client IP address
     */
    public function clearAttempts($email, $ip) {
        // Clear email attempts
        $stmt = $this->con->prepare("DELETE FROM login_attempts WHERE identifier = ? AND attempt_type = 'email'");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
        }

        // Clear account lockout
        $stmt = $this->con->prepare("DELETE FROM account_lockouts WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Check if CAPTCHA should be required
     *
     * @param string $email User email address
     * @param string $ip Client IP address
     * @return bool True if CAPTCHA is required
     */
    public function requiresCaptcha($email, $ip) {
        $emailAttempts = $this->getAttemptCount($email, 'email');
        $ipAttempts = $this->getAttemptCount($ip, 'ip');

        return ($emailAttempts >= $this->config['captcha_threshold_email'] ||
                $ipAttempts >= $this->config['captcha_threshold_ip']);
    }

    /**
     * Apply progressive delay based on attempt count
     *
     * @param string $email User email address
     */
    public function applyProgressiveDelay($email) {
        if (!$this->config['enable_progressive_delay']) {
            return;
        }

        $attempts = $this->getAttemptCount($email, 'email');

        if ($attempts >= 2) {
            $delay = min($attempts * 2, RateLimitConfig::MAX_DELAY);
            $delay = max($delay, RateLimitConfig::MIN_DELAY);
            sleep($delay);
        }
    }

    /**
     * Check if account is currently locked
     *
     * @param string $email User email address
     * @return bool True if account is locked
     */
    private function isAccountLocked($email) {
        $stmt = $this->con->prepare("SELECT locked_until FROM account_lockouts WHERE email = ? AND locked_until > NOW()");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $isLocked = $result->num_rows > 0;
        $stmt->close();

        return $isLocked;
    }

    /**
     * Get lockout information for an email
     *
     * @param string $email User email address
     * @return array|null Lockout information or null if not locked
     */
    private function getLockoutInfo($email) {
        $stmt = $this->con->prepare("SELECT locked_until, lockout_reason FROM account_lockouts WHERE email = ? AND locked_until > NOW()");
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $info = $result->fetch_assoc();
            $stmt->close();
            return $info;
        }

        $stmt->close();
        return null;
    }

    /**
     * Lock account for specified duration
     *
     * @param string $email User email address
     * @param string $reason Lockout reason
     */
    private function lockAccount($email, $reason = 'multiple_failed_attempts') {
        // Check if this account has been locked before
        $stmt = $this->con->prepare("SELECT lockout_count FROM account_lockouts WHERE email = ?");
        $previousLockouts = 0;

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $previousLockouts = $result->fetch_assoc()['lockout_count'];
            }
            $stmt->close();
        }

        // Determine lockout duration (escalate for repeat offenders)
        $duration = $this->config['email_lockout_duration'];
        if ($previousLockouts >= RateLimitConfig::MAX_LOCKOUT_COUNT) {
            $duration = RateLimitConfig::ESCALATED_LOCKOUT_DURATION;
        }

        $lockUntil = date('Y-m-d H:i:s', time() + $duration);
        $newLockoutCount = $previousLockouts + 1;

        $stmt = $this->con->prepare("INSERT INTO account_lockouts (email, locked_until, lockout_reason, lockout_count) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE locked_until = ?, lockout_count = lockout_count + 1");
        if ($stmt) {
            $stmt->bind_param("sssis", $email, $lockUntil, $reason, $newLockoutCount, $lockUntil);
            $stmt->execute();
            $stmt->close();
        }

        // Log security event
        $this->logSecurityEvent('account_locked', $email, null, [
            'lockout_reason' => $reason,
            'locked_until' => $lockUntil,
            'lockout_count' => $newLockoutCount
        ]);

        error_log("Account locked - Email: $email until $lockUntil (Reason: $reason, Count: $newLockoutCount)");
    }

    /**
     * Get attempt count within time window
     *
     * @param string $identifier Email or IP address
     * @param string $type 'email' or 'ip'
     * @return int Number of attempts
     */
    private function getAttemptCount($identifier, $type) {
        $windowStart = date('Y-m-d H:i:s', time() - $this->config['attempt_window']);

        $stmt = $this->con->prepare("SELECT COUNT(*) as count FROM login_attempts WHERE identifier = ? AND attempt_type = ? AND attempt_time >= ?");
        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("sss", $identifier, $type, $windowStart);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows > 0 ? $result->fetch_assoc()['count'] : 0;
        $stmt->close();

        return $count;
    }

    /**
     * Clean up old attempt records
     */
    private function cleanupOldAttempts() {
        $cutoff = date('Y-m-d H:i:s', time() - RateLimitConfig::CLEANUP_INTERVAL);

        // Clean old login attempts
        $stmt = $this->con->prepare("DELETE FROM login_attempts WHERE attempt_time < ?");
        if ($stmt) {
            $stmt->bind_param("s", $cutoff);
            $stmt->execute();
            $stmt->close();
        }

        // Clean expired lockouts
        $stmt = $this->con->prepare("DELETE FROM account_lockouts WHERE locked_until < NOW()");
        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }

        // Clean old success logs (keep only last 30 days)
        $successCutoff = date('Y-m-d H:i:s', time() - (30 * 24 * 3600));
        $stmt = $this->con->prepare("DELETE FROM login_success_log WHERE login_time < ?");
        if ($stmt) {
            $stmt->bind_param("s", $successCutoff);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Log security event
     *
     * @param string $eventType Type of security event
     * @param string $email User email (optional)
     * @param string $ip IP address (optional)
     * @param array $eventData Additional event data
     */
    private function logSecurityEvent($eventType, $email = null, $ip = null, $eventData = []) {
        $eventDataJson = json_encode($eventData);
        $severity = $this->getSeverityForEvent($eventType, $eventData);

        $stmt = $this->con->prepare("INSERT INTO security_events (event_type, email, ip_address, event_data, severity) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $eventType, $email, $ip, $eventDataJson, $severity);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Determine severity level for security event
     *
     * @param string $eventType Event type
     * @param array $eventData Event data
     * @return string Severity level
     */
    private function getSeverityForEvent($eventType, $eventData) {
        switch ($eventType) {
            case 'account_locked':
                return isset($eventData['lockout_count']) && $eventData['lockout_count'] > 2 ? 'high' : 'medium';
            case 'rate_limit_exceeded':
                return isset($eventData['attempt_count']) && $eventData['attempt_count'] > 10 ? 'high' : 'medium';
            case 'brute_force':
                return 'high';
            default:
                return 'medium';
        }
    }

    /**
     * Trigger security alert for suspicious activity
     *
     * @param string $email User email
     * @param string $ip IP address
     * @param int $attemptCount Number of attempts
     * @param string $type Type of attempts (email/ip)
     */
    private function triggerSecurityAlert($email, $ip, $attemptCount, $type) {
        $this->logSecurityEvent('brute_force', $email, $ip, [
            'attempt_count' => $attemptCount,
            'attempt_type' => $type,
            'alert_triggered' => true
        ]);

        error_log("SECURITY ALERT: Brute force attack detected - Email: $email, IP: $ip, Type: $type, Count: $attemptCount");

        // Here you could add email/SMS notifications to administrators
        // $this->sendSecurityAlert($email, $ip, $attemptCount, $type);
    }
}
?>