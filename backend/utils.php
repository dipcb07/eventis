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
function handle_response($status, $message, $data) {
    $message = strtoupper($message);
    http_response_code($status);
    exit(json_encode(["status" => $status, "message" => $message, "data" => $data]));
}
function authenticate($pdo, $apiName) {
    $headers = apache_request_headers();
    $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;
    $username = isset($headers['Username']) ? strtolower($headers['Username']) : null;

    if (!$authorizationHeader || !$username) {
        $response = "Authorization/Username missing";
        log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, get_request_info(), $response);
        handle_error(401, $response);
    }
    else{
        if (!preg_match('/Basic\s+(.+)/', $authorizationHeader, $matches)) {
            $response = "Invalid Authorization code format";
            log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, get_request_info(), $response);
            handle_error(401, $response);
        }
        else{
            $token = $matches[1];
            if (empty($token)) {
                $response = "Token is missing";
                log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, get_request_info(), $response);
                handle_error(401, $response);
            }
            else{
                $stmt = $pdo->prepare("SELECT * FROM api_secret WHERE secret_key = ? AND username = ? AND active = 1");
                $stmt->execute([$token, $username]);
                $authInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$authInfo) {
                    $response = 'Invalid secret key or username.';
                    log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, get_request_info(), $response);
                    handle_error(401, $response);
                }
                else{
                    return $authInfo['username'];
                }   
            }
        }
    }
}
function log_request($pdo, $username, $request_type, $status, $apiName, $requestUserInfo, $apiResponse) {
    $status = strtoupper($status);
    if($username == NULL) $username = 'NOT AUTHENTICATED';
    $stmt = $pdo->prepare("INSERT INTO api_log (username, request_type, request_user_info, date_time, status, api_name, api_response) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $request_type, $requestUserInfo, get_dhaka_time(), $status, $apiName, $apiResponse]);
}
function get_post_filtering($val){
    $val = htmlspecialchars($val);
    $val = str_replace("'","",$val);
    $val = str_replace('"','',$val);
    $val = str_replace('<','',$val);
    $val = str_replace('>','',$val);
    $val = str_replace('%','',$val);
    return $val;
}