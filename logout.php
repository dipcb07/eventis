<?php
require_once 'helper.php';
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {

    loadEnv(realpath('.env'));
    $pdo = get_db($_ENV['DATABASE_HOST'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD'], $_ENV['DATABASE_NAME'], $_ENV['DATABASE_CHARSET']);    

    $user_id = $_SESSION['user_id'];
    $session_id = $_SESSION['session_id'];
    $end_date_time = date('Y-m-d H:i:s');

    $sql = "UPDATE user_log SET end_date_time = :end_date_time WHERE user_id = :user_id AND session_id = :session_id AND end_date_time IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':end_date_time', $end_date_time);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':session_id', $session_id);
    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    } else {
        echo "Error updating logout time. Please try again.";
    }
    $pdo = null;
} else {
    header('Location: login.php');
    exit;
}
?>
