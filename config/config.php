<?php

if (defined('CONFIG_LOADED')) return;
define('CONFIG_LOADED', true);

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'tientt010');
define('DB_PASS', getenv('DB_PASS') ?: 'tldtt010');
define('DB_NAME', getenv('DB_NAME') ?: 'little_bone');

if (!defined('SITE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];

    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;

    define('SITE_URL', rtrim($protocol . $host . $basePath, '/'));
}

if (!defined('DEBUG_MODE')) define('DEBUG_MODE', true);

define('SESSION_LIFETIME', 3600);

ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

define('GOOGLE_MAPS_API_KEY', 'AIzaSyAqVSDO6n55RRMcjtm5RkKnz9SLN3RUBaI');
