<?php
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        throw new Exception("The .env file does not exist");
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (preg_match('/^["\'].*["\']$/', $value)) {
            $value = substr($value, 1, -1);
        }
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
function get_db($dbHost, $dbUser, $dbPassword, $dbName, $dbcharset){    

    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$dbcharset";
    $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
        return $pdo;
    } catch (PDOException $e) {
        return false;
    }
}
function get_dhaka_time() {
    date_default_timezone_set('Asia/Dhaka');
    return date('Y-m-d H:i:s');
}
function auth_check(){
    if (isset($_SESSION['logged_in']) && isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
        header('Location: ./');
        exit;
    }
}
loadEnv(realpath('.env'));

?>