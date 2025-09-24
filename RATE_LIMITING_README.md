# Rate Limiting & Login Lockout Implementation

## Overview

This document outlines the implementation of comprehensive rate limiting and login lockout functionality for the BabyBloom maternity clinic management system. The implementation provides multi-layered protection against brute force attacks, credential stuffing, and other automated attacks.

## Features Implemented

### 🛡️ Core Security Features
- **Email-based Rate Limiting**: Maximum 5 attempts per email within 15 minutes
- **IP-based Rate Limiting**: Maximum 20 attempts per IP within 15 minutes
- **Account Lockout**: Automatic account locking after failed attempts
- **Progressive Delays**: Increasing delays after multiple failed attempts
- **Security Monitoring**: Real-time dashboard for security events
- **Audit Logging**: Comprehensive logging of all security events

### 📊 Monitoring & Management
- **Security Dashboard**: Real-time monitoring interface for administrators
- **Account Management**: Manual unlock capabilities for administrators
- **Event Tracking**: Detailed security event logging and analysis
- **Statistics**: Real-time statistics on login attempts and security events

## Installation

### 1. Database Setup

Run the database installation script to create the required tables:

```bash
cd /path/to/BabyBloom
php database/install_rate_limiting.php
```

This creates the following tables:
- `login_attempts` - Tracks failed login attempts
- `account_lockouts` - Manages account lockout status
- `login_success_log` - Logs successful logins
- `security_events` - Records security events for analysis

### 2. File Structure

The implementation includes these key files:

```
assets/pages/shared/
├── rate-limit-config.php        # Configuration settings
├── rate-limiter.php              # Core rate limiting logic

assets/pages/auth/handlers/
├── staff-login-handler.php       # Updated staff login with rate limiting
├── mama-login-handler.php        # Updated mama login with rate limiting

assets/pages/admin/
├── security-dashboard.php        # Security monitoring dashboard

database/
├── rate_limiting_schema.sql      # Database schema
├── install_rate_limiting.php     # Installation script
```

## Configuration

### Rate Limiting Settings

Edit `assets/pages/shared/rate-limit-config.php` to customize:

```php
class RateLimitConfig {
    const MAX_ATTEMPTS_PER_EMAIL = 5;      // Email-based limit
    const MAX_ATTEMPTS_PER_IP = 20;        // IP-based limit
    const EMAIL_LOCKOUT_DURATION = 900;    // 15 minutes
    const IP_LOCKOUT_DURATION = 3600;      // 1 hour
    const ATTEMPT_WINDOW = 900;            // 15-minute window
    const CAPTCHA_THRESHOLD_EMAIL = 3;     // Show CAPTCHA after 3 attempts
}
```

### Environment-Specific Configuration

The system supports different settings for development vs production:

```php
// Override for development environment
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    $config['max_email_attempts'] = 10;  // More lenient for development
    $config['email_lockout_duration'] = 300; // 5 minutes for dev
}
```

## How It Works

### 1. Login Process Flow

```
User Login Attempt
       ↓
Check Rate Limits
       ↓
Rate Limited? → Yes → Show Error & Log Event
       ↓ No
Apply Progressive Delay
       ↓
Authenticate User
       ↓
Success? → Yes → Clear Attempts & Log Success
       ↓ No
Record Failed Attempt
       ↓
Check Lockout Threshold
       ↓
Lock Account if Needed
```

### 2. Rate Limiting Logic

**Email-based Protection:**
- Tracks attempts per email address
- Locks account after 5 failed attempts
- 15-minute lockout duration
- Progressive delays after 2nd attempt

**IP-based Protection:**
- Tracks attempts per IP address
- Blocks IP after 20 failed attempts
- 1-hour IP ban duration
- Prevents distributed attacks

### 3. Security Event Logging

All security-related events are logged with:
- Timestamp and event type
- User email and IP address
- Severity level (low, medium, high, critical)
- Additional context data (attempt counts, etc.)

## Security Dashboard

Access the security dashboard at: `/assets/pages/admin/security-dashboard.php`

### Features:
- **Real-time Statistics**: Failed attempts, active lockouts, security events
- **Active Lockouts**: View and manually unlock accounts
- **Failed Attempts**: Monitor high-risk IP addresses and email accounts
- **Security Events**: Comprehensive event log with severity levels
- **Manual Unlock**: Administrative override for locked accounts

### Access Requirements:
- Must be logged in as staff
- Must have "Sister" (admin) role

## Database Schema

### `login_attempts` Table
```sql
- id: Auto-increment primary key
- identifier: Email or IP address
- attempt_time: Timestamp of attempt
- attempt_type: 'email' or 'ip'
- user_agent: Browser/client information
```

### `account_lockouts` Table
```sql
- email: User email address (unique)
- locked_until: Lockout expiration time
- lockout_reason: Reason for lockout
- lockout_count: Number of times account has been locked
```

### `security_events` Table
```sql
- event_type: Type of security event
- email: Associated email (if applicable)
- ip_address: Source IP address
- event_data: JSON data with additional details
- severity: Event severity level
```

## Maintenance

### Automatic Cleanup

The system automatically cleans up old records:
- Login attempts older than 1 hour are removed
- Expired lockouts are automatically cleared
- Success logs older than 30 days are purged

### Manual Maintenance

Run periodic cleanup if needed:
```sql
-- Clean old login attempts
DELETE FROM login_attempts WHERE attempt_time < NOW() - INTERVAL 24 HOUR;

-- Clean expired lockouts
DELETE FROM account_lockouts WHERE locked_until < NOW();

-- Clean old success logs
DELETE FROM login_success_log WHERE login_time < NOW() - INTERVAL 30 DAY;
```

## Security Considerations

### Best Practices Implemented:
- **Generic Error Messages**: Prevent user enumeration
- **Progressive Delays**: Slow down automated attacks
- **Comprehensive Logging**: Track all security events
- **IP and Email Tracking**: Multi-dimensional protection
- **Admin Override**: Manual unlock for legitimate users

### Additional Recommendations:
- Monitor the security dashboard regularly
- Set up email alerts for critical security events
- Review failed login patterns weekly
- Consider implementing CAPTCHA for high-risk scenarios
- Regularly review and adjust rate limiting thresholds

## Troubleshooting

### Common Issues:

**1. Users Getting Locked Out Too Frequently**
- Reduce `MAX_ATTEMPTS_PER_EMAIL` in config
- Increase `EMAIL_LOCKOUT_DURATION` for shorter lockouts
- Check for automated scripts or bots

**2. Legitimate Users Unable to Login**
- Use security dashboard to manually unlock accounts
- Review IP-based blocking for office networks
- Check for shared IP addresses (NAT, proxy)

**3. High False Positive Rate**
- Adjust `ATTEMPT_WINDOW` for longer time windows
- Review `IP_LOCKOUT_DURATION` for office environments
- Consider whitelist for trusted IP ranges

### Log Files:
- Security events: `security_events` table
- Application logs: Check PHP error logs
- Authentication logs: `login_success_log` table

## Testing

### Test Scenarios:
1. **Normal Login**: Verify successful login clears attempts
2. **Failed Password**: Confirm failed attempts are recorded
3. **Account Lockout**: Test automatic lockout after 5 attempts
4. **IP Blocking**: Test IP-based rate limiting
5. **Manual Unlock**: Test administrative unlock feature
6. **Progressive Delays**: Verify delays increase with attempts

### Test Commands:
```bash
# Check current lockouts
mysql> SELECT * FROM account_lockouts WHERE locked_until > NOW();

# Check recent failed attempts
mysql> SELECT * FROM login_attempts WHERE attempt_time > NOW() - INTERVAL 1 HOUR;

# View security events
mysql> SELECT * FROM security_events ORDER BY created_at DESC LIMIT 10;
```

## Performance Impact

The rate limiting system is designed for minimal performance impact:
- Indexed database queries for fast lookups
- Automatic cleanup of old records
- Lightweight in-memory operations
- Optimized database schema

Expected performance impact: < 50ms additional latency per login attempt.

## Compliance & Reporting

The implementation supports compliance requirements by providing:
- Detailed audit trails of all login attempts
- Security event logging with timestamps
- User activity monitoring
- Administrative access controls
- Data retention policies

## Support

For issues or questions:
1. Check the security dashboard for real-time status
2. Review the security events log for detailed information
3. Check application error logs for system-level issues
4. Contact system administrator for account unlock requests

---

**Implementation Date**: 2024-12-26
**Version**: 1.0
**Status**: Production Ready
**Last Updated**: 2024-12-26