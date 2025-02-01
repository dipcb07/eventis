<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isAuthenticated = isset($_SESSION['logged_in']) && isset($_SESSION['user_id']) && isset($_SESSION['session_id']);
$redirect_url = $isAuthenticated ? 'dashboard' : 'login';
header('Location: '. $redirect_url);
?>