<?php
require_once 'helper/utils.php';

try {
    $path = dirname(__DIR__) . '/../.env';
    $absolutePath = realpath($path);    
    loadEnv($absolutePath);
} 
catch (Exception $e) {
    handle_response(500, $e->getMessage());
}

$timezone = $_ENV['APP_TIMEZONE'] ?? 'Asia/Dhaka';
$dbHost = $_ENV['DATABASE_HOST'] ?? 'localhost';
$dbUser = $_ENV['DATABASE_USER'] ?? 'root';
$dbPassword = $_ENV['DATABASE_PASSWORD'] ?? '';
$dbName = $_ENV['DATABASE_NAME'] ?? 'eventis';
$dbcharset = $_ENV['DATABASE_CHARSET'] ?? 'utf8mb4';

$allowed_methods = ['GET', 'POST'];
$allowed_endpoints = ['user_create', 'user_update', 'user_delete', 'user_login', 'event_create', 'event_update', 'event_delete', 'attendee_register','attendee_update', 'attendee_delete', 'attendee_list'];

set_default_timezone($timezone);
$pdo = get_db($dbHost, $dbUser, $dbPassword, $dbName, $dbcharset);
$endpoint = get_endpoint($allowed_endpoints);
$method = get_method($allowed_methods);

?>