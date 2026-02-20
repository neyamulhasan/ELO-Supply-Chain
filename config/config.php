<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'elo_supply_chain');

// Site Configuration
define('SITE_NAME', 'ELO Supply Chain');
define('SITE_URL', 'http://localhost/ELO-Supply-Chain/');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set Default Timezone
date_default_timezone_set('UTC');
?>
