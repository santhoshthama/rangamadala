<?php 

$serverName = $_SERVER['SERVER_NAME'] ?? '';
$isCli = PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
$localHosts = ['localhost', '127.0.0.1', '::1'];
$isLocalHost = in_array(strtolower((string)$serverName), $localHosts, true);

if ($isCli || $isLocalHost) 
{
    // Local development configuration
    // define('DB_NAME', 'rangamandala_db');
    // define('DB_HOST', 'mysql-rangamandala.alwaysdata.net');
    // define('DB_USER', '445039_santosh');
    // define('DB_PASSWORD', 'sana@2003');
    // define('DB_PORT', '3306');

    define('DB_NAME', 'rangamandala_db');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_PORT', '3306');

    define('DB_DRIVER', '');
    define('DB_CHARSET', 'utf8mb4');
    define("APPROOT", dirname(dirname(__FILE__)));

    if ($isCli) {
        define('ROOT', 'http://localhost/Rangamadala/public');
    } else {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
        $scheme = $isHttps ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        define('ROOT', $scheme . '://' . $host . '/Rangamadala/public');
    }
} 
else 
{
    // Live/production configuration
    define('DB_NAME', 'rangamadala');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_DRIVER', '');
    define('DB_PORT', '3306');
    define('DB_CHARSET', 'utf8mb4');
    
    define('ROOT', 'https://www.rangamadala.com');
}

// Application constants
define('APP_NAME', "Rangamadala");
define('APP_DESC', "A social media platform to connect all drama professionals.");

// true = show errors during development
define('DEBUG', true);
