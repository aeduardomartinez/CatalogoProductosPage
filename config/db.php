<?php
// Database configuration using environment variables for Docker
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'sneaker_catalog');
define('DB_USER', getenv('DB_USER') ?: 'postgres');
define('DB_PASS', getenv('DB_PASS') ?: '123');

try {
    // Using pgsql driver instead of mysql
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión (PostgreSQL): " . $e->getMessage());
}

// Global constants
define('SITE_NAME', 'SneakerVault');
define('ADMIN_WHATSAPP', '573000000000');
define('CURRENCY', '$');
?>
