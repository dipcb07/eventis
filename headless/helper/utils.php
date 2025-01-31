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
function handle_response($status, $message, $data = []) {
    $message = strtoupper($message);
    http_response_code($status);
    $response = ["status" => $status, "message" => $message];
    if(!empty($data)) $response['data'] = $data;
    exit(json_encode($response));
}
function authenticate($pdo, $apiName) {
    $headers = apache_request_headers();
    $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;
    $username = isset($headers['Username']) ? strtolower($headers['Username']) : null;

    if (!$authorizationHeader || !$username) {
        $response = "Authorization/Username missing";
        log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, $response);
        handle_response(500, $response);
    }
    else{
        if (!preg_match('/Basic\s+(.+)/', $authorizationHeader, $matches)) {
            $response = "Invalid Authorization code format";
            log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, $response);
            handle_response(500, $response);
        }
        else{
            $token = $matches[1];
            if (empty($token)) {
                $response = "Token is missing";
                log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, $response);
                handle_response(500, $response);
            }
            else{
                $stmt = $pdo->prepare("SELECT * FROM api_secret WHERE secret_key = ? AND username = ? AND active = 1");
                $stmt->execute([$token, $username]);
                $authInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$authInfo) {
                    $response = 'Invalid secret key or username.';
                    log_request($pdo, null, $_SERVER['REQUEST_METHOD'], 'Failed', $apiName, $response);
                    handle_response(500, $response);
                }
                else{
                    return $authInfo['username'];
                }   
            }
        }
    }
}
function log_request($pdo, $username, $request_type, $status, $apiName, $apiResponse) {
    $status = strtoupper($status);
    if($username == NULL) $username = 'NOT AUTHENTICATED';
    $stmt = $pdo->prepare("INSERT INTO api_log (username, request_type, date_time, status, api_name, api_response) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $request_type, get_dhaka_time(), $status, $apiName, $apiResponse]);
}
function get_endpoint($allowed_endpoints){
    $requestUri = basename($_SERVER['REQUEST_URI']);
    $first_string = explode('/', $requestUri)[count(explode('/', $requestUri)) - 1];
    if (strpos($first_string, '?') !== false) {
        $sec_string = explode("?", $first_string);
        $endpoint = $sec_string[0];
    } else {
        $endpoint = $first_string;
    }
    if (!in_array($endpoint, $allowed_endpoints)) {
        handle_response(405, 'Endpoint not allowed');
    }
    else return $endpoint;
}
function get_method($allowed_methods){
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
        handle_response(405, 'Method Not Allowed');
    }
    else return $_SERVER['REQUEST_METHOD'];
}
function set_default_timezone($timezone){
    date_default_timezone_set(strtolower($timezone));
}
function sanitizeInputs(array $input, string $method = 'post'): array {
    $sanitized = [];
    foreach ($input as $key => $value) {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $value = str_replace("'","",$value);
        $value = str_replace('"','',$value);
        $value = str_replace('<','',$value);
        $value = str_replace('>','',$value);
        $value = str_replace('%','',$value);
        $sanitized[$key] = $value;
    }
    if (strtolower($method) === 'get') {
        return array_intersect_key($sanitized, $_GET);
    } elseif (strtolower($method) === 'post') {
        return array_intersect_key($sanitized, $_POST);
    }
    return $sanitized;
}
function generate_unique_id($string) {
    $microtime = microtime(true);
    $hash = md5($microtime . $string);
    $shuffledHash = str_shuffle($hash);
    $uniqueId = substr($shuffledHash, 0, 10);
    return $uniqueId;
}
function get_data($method, $required_parameters = []){
    switch ($method) {
        case 'POST':
            $data = sanitizeInputs($_POST, 'post');
            break;
        case 'GET':
            $data = sanitizeInputs($_GET, 'get');
            break;
        case 'PUT':
            $data = sanitizeInputs(json_decode(file_get_contents('php://input'), true), 'put');
            break;
        case 'DELETE':
            $data = sanitizeInputs(json_decode(file_get_contents('php://input'), true), 'delete');
            break;
        default:
            $data = [];
            break;
    }
    if(!empty($required_parameters)){
        foreach ($required_parameters as $param) {
            if (!isset($data[$param])) {
                return false;
            }
        }
    }
    return $data;
}
?>