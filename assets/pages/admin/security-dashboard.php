<?php
/**
 * Security Monitoring Dashboard
 *
 * This dashboard provides real-time monitoring of security events,
 * rate limiting statistics, and suspicious activity.
 */

// Use secure session initialization
require_once __DIR__ . '/../shared/session-init.php';
include '../shared/db-access.php';

// Check if user is logged in and is an admin (Sister)
if (!isset($_SESSION["staffEmail"]) || $_SESSION["staffPosition"] != "Sister") {
    header("Location: ../dashboard/staff-dashboard.php");
    exit();
}

// Get statistics for the dashboard
function getSecurityStats($con) {
    $stats = [];

    // Failed login attempts in last 24 hours
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM login_attempts WHERE attempt_time >= NOW() - INTERVAL 24 HOUR");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['failed_attempts_24h'] = $result->fetch_assoc()['count'];
        $stmt->close();
    }

    // Current account lockouts
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM account_lockouts WHERE locked_until > NOW()");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['active_lockouts'] = $result->fetch_assoc()['count'];
        $stmt->close();
    }

    // Security events in last 24 hours
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM security_events WHERE created_at >= NOW() - INTERVAL 24 HOUR");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['security_events_24h'] = $result->fetch_assoc()['count'];
        $stmt->close();
    }

    // Successful logins in last 24 hours
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM login_success_log WHERE login_time >= NOW() - INTERVAL 24 HOUR");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['successful_logins_24h'] = $result->fetch_assoc()['count'];
        $stmt->close();
    }

    return $stats;
}

function getRecentFailedAttempts($con) {
    $stmt = $con->prepare("
        SELECT identifier, attempt_type, COUNT(*) as attempts, MAX(attempt_time) as last_attempt
        FROM login_attempts
        WHERE attempt_time >= NOW() - INTERVAL 1 HOUR
        GROUP BY identifier, attempt_type
        HAVING attempts >= 3
        ORDER BY attempts DESC, last_attempt DESC
        LIMIT 10
    ");

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $attempts = [];
        while ($row = $result->fetch_assoc()) {
            $attempts[] = $row;
        }
        $stmt->close();
        return $attempts;
    }
    return [];
}

function getRecentSecurityEvents($con) {
    $stmt = $con->prepare("
        SELECT event_type, email, ip_address, severity, created_at
        FROM security_events
        WHERE created_at >= NOW() - INTERVAL 24 HOUR
        ORDER BY created_at DESC
        LIMIT 20
    ");

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $stmt->close();
        return $events;
    }
    return [];
}

function getCurrentLockouts($con) {
    $stmt = $con->prepare("
        SELECT email, locked_until, lockout_reason, lockout_count, created_at
        FROM account_lockouts
        WHERE locked_until > NOW()
        ORDER BY locked_until DESC
    ");

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $lockouts = [];
        while ($row = $result->fetch_assoc()) {
            $lockouts[] = $row;
        }
        $stmt->close();
        return $lockouts;
    }
    return [];
}

// Handle unlock account request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unlock_email'])) {
    $unlockEmail = $_POST['unlock_email'];
    $stmt = $con->prepare("DELETE FROM account_lockouts WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $unlockEmail);
        $stmt->execute();
        $stmt->close();

        // Also clear failed attempts for this email
        $stmt = $con->prepare("DELETE FROM login_attempts WHERE identifier = ? AND attempt_type = 'email'");
        if ($stmt) {
            $stmt->bind_param("s", $unlockEmail);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['success_message'] = "Account unlocked successfully: $unlockEmail";
        header("Location: security-dashboard.php");
        exit();
    }
}

$stats = getSecurityStats($con);
$recentAttempts = getRecentFailedAttempts($con);
$securityEvents = getRecentSecurityEvents($con);
$currentLockouts = getCurrentLockouts($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baby Bloom - Security Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
    <link rel="stylesheet" type="text/css" href="../../css/style.css">
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <style>
        :root{
            --bg: #EFEBEA;
            --light-txt: #0D4B53;
            --light-txt2:#000000;
            --dark-txt: #86B6BB;
            --danger: #dc3545;
            --warning: #ffc107;
            --success: #28a745;
        }

        .security-dashboard {
            padding: 20px;
            background-color: var(--bg);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--light-txt);
        }

        .stat-label {
            color: var(--dark-txt);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .danger { color: var(--danger) !important; }
        .warning { color: var(--warning) !important; }
        .success { color: var(--success) !important; }

        .section-title {
            color: var(--light-txt);
            font-size: 1.5rem;
            font-weight: bold;
            margin: 30px 0 15px 0;
            border-bottom: 2px solid var(--dark-txt);
            padding-bottom: 10px;
        }

        .data-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .data-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: var(--light-txt);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        .severity-high { color: var(--danger); font-weight: bold; }
        .severity-medium { color: var(--warning); }
        .severity-low { color: var(--dark-txt); }

        .unlock-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .unlock-btn:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .refresh-info {
            text-align: center;
            color: var(--dark-txt);
            font-size: 0.9rem;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
            <div class="d-flex flex-column">
                <h1 class="common-title">BabyBloom</h1>
                <h3 class="common-description">Security Monitoring Dashboard</h3>
            </div>
        </header>

        <main class="security-dashboard">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <!-- Statistics Overview -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-number <?php echo $stats['failed_attempts_24h'] > 50 ? 'danger' : ($stats['failed_attempts_24h'] > 20 ? 'warning' : ''); ?>">
                        <?php echo $stats['failed_attempts_24h']; ?>
                    </div>
                    <div class="stat-label">Failed Login Attempts (24h)</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number <?php echo $stats['active_lockouts'] > 5 ? 'danger' : ($stats['active_lockouts'] > 0 ? 'warning' : 'success'); ?>">
                        <?php echo $stats['active_lockouts']; ?>
                    </div>
                    <div class="stat-label">Active Account Lockouts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number <?php echo $stats['security_events_24h'] > 20 ? 'danger' : ($stats['security_events_24h'] > 10 ? 'warning' : ''); ?>">
                        <?php echo $stats['security_events_24h']; ?>
                    </div>
                    <div class="stat-label">Security Events (24h)</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number success">
                        <?php echo $stats['successful_logins_24h']; ?>
                    </div>
                    <div class="stat-label">Successful Logins (24h)</div>
                </div>
            </div>

            <!-- Active Account Lockouts -->
            <?php if (!empty($currentLockouts)): ?>
            <h2 class="section-title">🔒 Active Account Lockouts</h2>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Locked Until</th>
                            <th>Reason</th>
                            <th>Lockout Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($currentLockouts as $lockout): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lockout['email']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($lockout['locked_until'])); ?></td>
                            <td><?php echo htmlspecialchars($lockout['lockout_reason']); ?></td>
                            <td><?php echo $lockout['lockout_count']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to unlock this account?')">
                                    <input type="hidden" name="unlock_email" value="<?php echo htmlspecialchars($lockout['email']); ?>">
                                    <button type="submit" class="unlock-btn">Unlock</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Recent Failed Attempts -->
            <?php if (!empty($recentAttempts)): ?>
            <h2 class="section-title">⚠️ High-Risk Failed Login Attempts (Last Hour)</h2>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Identifier</th>
                            <th>Type</th>
                            <th>Attempts</th>
                            <th>Last Attempt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAttempts as $attempt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attempt['identifier']); ?></td>
                            <td><?php echo ucfirst($attempt['attempt_type']); ?></td>
                            <td class="<?php echo $attempt['attempts'] >= 10 ? 'danger' : 'warning'; ?>">
                                <?php echo $attempt['attempts']; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($attempt['last_attempt'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Recent Security Events -->
            <h2 class="section-title">🛡️ Recent Security Events (Last 24 Hours)</h2>
            <div class="data-table">
                <table>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Event Type</th>
                            <th>Email</th>
                            <th>IP Address</th>
                            <th>Severity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($securityEvents)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:20px; color:#666;">
                                No security events in the last 24 hours
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($securityEvents as $event): ?>
                            <tr>
                                <td><?php echo date('m-d H:i', strtotime($event['created_at'])); ?></td>
                                <td><?php echo str_replace('_', ' ', ucwords($event['event_type'], '_')); ?></td>
                                <td><?php echo htmlspecialchars($event['email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($event['ip_address'] ?? 'N/A'); ?></td>
                                <td class="severity-<?php echo $event['severity']; ?>">
                                    <?php echo ucfirst($event['severity']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="refresh-info">
                Dashboard last updated: <?php echo date('Y-m-d H:i:s'); ?> |
                <a href="security-dashboard.php" style="color: var(--light-txt);">Refresh</a>
            </div>

            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../dashboard/staff-dashboard.php">
                    <button class="main-footer-btn">Back to Dashboard</button>
                </a>
            </div>
        </main>
    </div>
</body>
</html>

<?php $con->close(); ?>