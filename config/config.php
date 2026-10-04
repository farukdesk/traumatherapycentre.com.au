<?php
/**
 * Global configuration.
 * Copy this file's DB_* values to match your environment (or set env vars).
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'pmrebigpond_TT202634');
define('DB_USER', getenv('DB_USER') ?: 'pmrebigpond_tt9384');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '={#n07W7nwr$KX&Q');
define('DB_CHARSET', 'utf8mb4');

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
