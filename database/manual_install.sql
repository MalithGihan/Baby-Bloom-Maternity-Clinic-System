-- Manual Installation SQL for Rate Limiting Tables
-- Run these commands as MySQL root user or a user with CREATE privileges

USE babybloom;

-- Create login_attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attempt_type ENUM('email', 'ip') NOT NULL,
    user_agent TEXT,
    INDEX idx_identifier_time (identifier, attempt_time),
    INDEX idx_attempt_type_time (attempt_type, attempt_time)
);

-- Create account_lockouts table
CREATE TABLE IF NOT EXISTS account_lockouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    locked_until TIMESTAMP NOT NULL,
    lockout_reason VARCHAR(100) DEFAULT 'multiple_failed_attempts',
    lockout_count INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email_locked (email, locked_until),
    INDEX idx_locked_until (locked_until)
);

-- Create login_success_log table
CREATE TABLE IF NOT EXISTS login_success_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_id VARCHAR(255),
    INDEX idx_email_time (email, login_time),
    INDEX idx_ip_time (ip_address, login_time)
);

-- Create security_events table
CREATE TABLE IF NOT EXISTS security_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type ENUM('brute_force', 'account_locked', 'suspicious_activity', 'rate_limit_exceeded') NOT NULL,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    event_data JSON,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type_time (event_type, created_at),
    INDEX idx_severity_time (severity, created_at)
);

-- Grant necessary permissions to babybloom user
GRANT SELECT, INSERT, UPDATE, DELETE ON babybloom.login_attempts TO 'babybloom'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON babybloom.account_lockouts TO 'babybloom'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON babybloom.login_success_log TO 'babybloom'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON babybloom.security_events TO 'babybloom'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify tables were created
SHOW TABLES LIKE '%login%';
SHOW TABLES LIKE '%lockout%';
SHOW TABLES LIKE '%security%';

-- Test basic functionality
INSERT INTO login_attempts (identifier, attempt_type) VALUES ('test@example.com', 'email');
SELECT * FROM login_attempts WHERE identifier = 'test@example.com';
DELETE FROM login_attempts WHERE identifier = 'test@example.com';

-- Show table structures
DESCRIBE login_attempts;
DESCRIBE account_lockouts;
DESCRIBE login_success_log;
DESCRIBE security_events;