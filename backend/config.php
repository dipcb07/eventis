<?php
require_once 'utils.php';

try {
    loadEnv(__DIR__ . '/.env');
} 
catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

$dbHost = $_ENV['DATABASE_HOST'] ?? 'localhost';
$dbUser = $_ENV['DATABASE_USER'] ?? 'root';
$dbPassword = $_ENV['DATABASE_PASSWORD'] ?? '';
$dbName = $_ENV['DATABASE_NAME'] ?? 'eventis';
$dbcharset = $_ENV['DATABASE_CHARSET'] ?? 'utf8mb4';

$pdo = get_db($dbHost, $dbUser, $dbPassword, $dbName, $dbcharset);


?>