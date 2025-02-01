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
function get_dhaka_time() {
    date_default_timezone_set('Asia/Dhaka');
    return date('Y-m-d H:i:s');
}
function auth_check() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $isAuthenticated = isset($_SESSION['logged_in']) && isset($_SESSION['user_id']) && isset($_SESSION['session_id']);
    $currentPage = $_SERVER['REQUEST_URI'];
    if (!$isAuthenticated) {
        if (strpos($currentPage, 'eventis/login') === false) {
            header('Location: ./login');
            exit;
        }
    }
    if ($isAuthenticated && strpos($currentPage, 'eventis/login') !== false) {
        header('Location: ./dashboard');
        exit;
    }
    if (strpos($currentPage, 'eventis/dashboard') !== false) {
        return;
    }

    $currentPath = ltrim(str_replace("/eventis", "", $currentPage), "/");
    $new_url = ltrim(str_replace("/eventis", "", $_SERVER['REQUEST_URI']), "/");
    if ($currentPath === $new_url) {
        return; 
    }
    header("Location: ./$new_url");
    exit;
}
function mail_send($apiKey, $toEmail, $toName, $subject, $content) {
    $senderEmail = "dipcb05@gmail.com";
    $senderName = "Eventis";
    $data = [
        "sender" => ["name" => $senderName, "email" => $senderEmail],
        "to" => [["email" => $toEmail, "name" => $toName]],
        "subject" => $subject,
        "htmlContent" => $content
    ];
    $ch = curl_init("https://api.brevo.com/v3/smtp/email");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "api-key: $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $msg =  json_decode($response, true);
    return (isset($msg['messageId'])) ? true : false;
}


loadEnv(realpath('.env'));
$api_key = $_ENV['HEADLESS_API_KEY'];
$api_username = $_ENV['HEADLESS_API_USERNAME'];
$api_url = $_ENV['APP_URL'];
?>