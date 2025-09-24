-- BabyBloom Rate Limiting Database Schema
-- This file contains the SQL commands to create tables for rate limiting and login lockout functionality
USE babybloom;

-- Table to track failed login attempts
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,  -- email or IP address
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attempt_type ENUM('email', 'ip') NOT NULL,
    user_agent TEXT,  -- Optional: track browser/device info
    INDEX idx_identifier_time (identifier, attempt_time),
    INDEX idx_attempt_type_time (attempt_type, attempt_time)
);

-- Table to track account lockouts
CREATE TABLE IF NOT EXISTS account_lockouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    locked_until TIMESTAMP NOT NULL,
    lockout_reason VARCHAR(100) DEFAULT 'multiple_failed_attempts',
    lockout_count INT DEFAULT 1,  -- Track how many times account has been locked
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email_locked (email, locked_until),
    INDEX idx_locked_until (locked_until)
);

-- Table to track successful logins (for monitoring and analytics)
CREATE TABLE IF NOT EXISTS login_success_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),  -- Supports both IPv4 and IPv6
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_id VARCHAR(255),
    INDEX idx_email_time (email, login_time),
    INDEX idx_ip_time (ip_address, login_time)
);

-- Table for security events and alerts
CREATE TABLE IF NOT EXISTS security_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type ENUM('brute_force', 'account_locked', 'suspicious_activity', 'rate_limit_exceeded') NOT NULL,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    event_data JSON,  -- Store additional event details
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type_time (event_type, created_at),
    INDEX idx_severity_time (severity, created_at)
);