<?php
/**
 * Global configuration.
 * DB credentials come from environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASS)
 * or from config/config.local.php (copy config.local.example.php — never commit it).
 */

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require_once $localConfig;
}

if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: '');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: '');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

if (DB_NAME === '' || DB_USER === '') {
    http_response_code(500);
    exit('Database is not configured. Set DB_* environment variables or create config/config.local.php (see config/config.local.example.php).');
}

define('SITE_URL', getenv('SITE_URL') ?: '');

// Session hardening
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

date_default_timezone_set('Australia/Brisbane');
