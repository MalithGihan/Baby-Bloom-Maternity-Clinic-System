<?php
// Use secure session start for logout
require_once __DIR__ . '/../shared/secure-session-start.php';

// Include session security utilities
require_once __DIR__ . '/../shared/session-security.php';

// Perform secure logout
secureLogout('staff');
?>