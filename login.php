<?php
require_once 'helper.php';
session_start();
auth_check();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['check_username'])) {
        $url = $api_url . 'eventis/headless/api/user_exist_check';
        $headers = [
            "Authorization: Basic " . $api_key,
            "Content-Type: application/x-www-form-urlencoded",
            "Username: " . $api_username
        ];
        $data = http_build_query(['username' => $_POST['username']]);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
        exit;
    }
    
    if (isset($_POST['login'])) {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $url = $api_url . 'eventis/headless/api/user_login';
            $headers = [
                "Authorization: Basic " . $api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Username: " . $api_username
            ];
            $data = http_build_query([
                'username' => $_POST['username'],
                'password' => $_POST['password']
            ]);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($result, true);
            if ($result['status'] == 200) {
                $user_id = $result['data']['user_id'];
                $session_id = $result['data']['session_id'];
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $result['data']['user_id'];
                $_SESSION['session_id'] = $result['data']['session_id'];
                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
            }
            exit;
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventis - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 400px;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Eventis</h5>
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Email/Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter email or username" required>
                        <small id="usernameStatus" style="display:none;"></small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                <i id="eyeIcon" class="bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me">
                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    <div class="text-center">
                        <small>Don't have an account? </small><a href="./register">Register Here</a><br>
                        <small>Forgot Password? </small><a href="./forgot_password">Reset Here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {

            const submitButton = $('button[type="submit"]');

            $('#username').on('blur', function() {
                let username = $(this).val();
                if (username !== '') {
                    $.post('', { check_username: true, username: username }, function(response) {
                        let res = JSON.parse(response);
                        if (res.status == 200 && res.data.exist) {
                            $('#usernameStatus').hide();
                            submitButton.prop('disabled', false);
                        }
                        else {
                            $('#usernameStatus').text('Username does not exist').css('color', 'red').show();
                            submitButton.prop('disabled', true);
                        }
                    });
                }
            });
            
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize() + '&login=true';
                $.post('', formData, function(response) {
                    let res = JSON.parse(response);
                    Swal.fire({
                        title: res.success ? 'Success' : 'Error',
                        text: res.message,
                        icon: res.success ? 'success' : 'error'
                    }).then(() => {
                        if (res.success) window.location.href = 'dashboard';
                    });
                });
            });
            
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIcon');
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            });
        });
    </script>
</body>
</html>
