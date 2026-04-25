<?php
// config/db.php

// 1. Obtener la URL de la base de datos (Supabase / Railway)
$dbUrl = getenv('DATABASE_URL');

if ($dbUrl) {
    // Formato: postgresql://user:pass@host:port/dbname
    $p = parse_url($dbUrl);
    $host = $p['host'];
    $port = $p['port'] ?? 5432;
    $user = $p['user'];
    $pass = $p['pass'];
    $dbname = ltrim($p['path'], '/');
} else {
    // Valores por defecto para Local (Docker)
    $host = getenv('DB_HOST') ?: 'db';
    $port = 5432;
    $dbname = getenv('DB_NAME') ?: 'sneaker_catalog';
    $user = getenv('DB_USER') ?: 'postgres';
    $pass = getenv('DB_PASS') ?: '123';
}

try {
    // Construir DSN para PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $pass);
    
    // Configuraciones de seguridad y error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Constantes Globales
define('SITE_NAME', 'SneakerVault');
define('ADMIN_WHATSAPP', '573226327178'); // Actualizado con tu número
define('CURRENCY', '$');
?>
