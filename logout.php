<?php
require_once 'helper.php';
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
    $user_id = $_SESSION['user_id'];
    $session_id = $_SESSION['session_id'];
    $url = $api_url . 'eventis/headless/api/user_logout';
    $headers = array(
        "Authorization: Basic " . $api_key,
        "Content-Type: application/x-www-form-urlencoded",
        "Username: " . $api_username
    );
    $data = http_build_query(array(
        'user_id' => $user_id,
        'session_id' => $session_id
    ));
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>
