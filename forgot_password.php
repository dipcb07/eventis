<?php
require_once 'helper.php';

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

    if (isset($_POST['submit_reset'])) {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            $url = $api_url . 'eventis/headless/api/user_forgot_password';
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
            echo $result;
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
    <title>Eventis - Forgot Password</title>
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
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Email/Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter email or username" required>
                        <small id="usernameStatus" style="display:none;"></small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                <i id="eyeIcon" class="bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="repeatPassword" class="form-label">Repeat Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="repeatPassword" name="repeatPassword" placeholder="Re-enter your password" required onpaste="return false;">
                            <button type="button" id="toggleRepeatPassword" class="btn btn-outline-secondary">
                                <i id="repeateyeIcon" class="bi-eye-slash"></i>
                            </button>
                        </div>
                        <div id="passwordMismatch" class="text-danger mt-1" style="display: none;">Passwords do not match.</div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary" name="submit_reset">Reset Password</button>
                    </div>
                    <div class="text-center">
                        <small>Remembered your password? </small><a href="./login">Login Here</a><br>
                        <small class="ms-2">Don't have an account? </small><a href="./register">Register Here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {

            const submitButton = $('button[type="submit"]');

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

            $('#toggleRepeatPassword').on('click', function() {
                const repeatpasswordField = $('#repeatPassword');
                const repeateyeIcon = $('#repeateyeIcon');

                if (repeatpasswordField.attr('type') === 'password') {
                    repeatpasswordField.attr('type', 'text');
                    repeateyeIcon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    repeatpasswordField.attr('type', 'password');
                    repeateyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            });

            $('#password, #repeatPassword').on('copy paste', function(e) {
                e.preventDefault();
            });

            $('#repeatPassword').on('blur', function() {
                const password = $('#password').val();
                const repeatPassword = $(this).val();

                if (password !== repeatPassword) {
                    $('#passwordMismatch').show();
                    submitButton.prop('disabled', true);
                } else {
                    $('#passwordMismatch').hide();
                    submitButton.prop('disabled', false);
                }
            });

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

            $('#forgotPasswordForm').submit(function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: formData + '&submit_reset=true',
                    success: function(response) {
                        console.log(response);
                        let res = JSON.parse(response);
                        Swal.fire({
                            title: (res.status == 200) ? 'Success' : 'Error',
                            text: res.message,
                            icon:(res.status == 200) ? 'success' : 'error'
                        }).then(() => {
                            if (res.status == 200) window.location.href = 'login';
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
