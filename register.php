<?php
require_once "helper.php";
session_start();
auth_check();

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(isset($_POST['email_check'])){
        if(isset($_POST['user_email']) && !empty($_POST['user_email'])){
            $url = $api_url . 'eventis/headless/api/user_email_check';
            $headers = array(
                "Authorization: Basic " . $api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Username: " . $api_username
            );
            $data = http_build_query(array(
                'email' => $_POST['user_email']
            ));
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
    }
    elseif(isset($_POST['username_check'])){
        if(isset($_POST['user_username']) && !empty($_POST['user_username'])){
            $url = $api_url . 'eventis/headless/api/user_username_check';
            $headers = array(
                "Authorization: Basic ". $api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Username: ". $api_username
            );
            $data = http_build_query(array(
                'username' => $_POST['user_username']
            ));
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
    }
    elseif(isset($_POST['user_register'])){
        if(isset($_POST['user_name']) && isset($_POST['user_email']) && isset($_POST['user_username']) && isset($_POST['user_password']) && !empty($_POST['user_name']) && !empty($_POST['user_email']) && !empty($_POST['user_username']) && !empty($_POST['user_password'])){
            $url = $api_url . 'eventis/headless/api/user_create';
            $headers = array(
                "Authorization: Basic ". $api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Username: ". $api_username
            );
            $data = http_build_query(array(
                'name' => $_POST['user_name'],
                'email' => $_POST['user_email'],
                'org' => $_POST['org'],
                'username' => $_POST['user_username'],
                'password' => $_POST['user_password']
            ));
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            echo $response;
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
    <title>Register - Eventis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        a {
            text-decoration: none;
        }
        .blinking {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            50% {
                visibility: hidden;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg" style="width: 400px;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Eventis</h5>
                <form id="registerForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                        <div id="usernameCheck" class="text-danger" style="display:none;">Username already exists.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        <div id="emailCheck" class="text-danger" style="display:none;">Email already exists.</div>
                    </div>
                    <div class="mb-3">
                        <label for="org" class="form-label">Email</label>
                        <input type="org" class="form-control" id="org" name="org" placeholder="Enter your organization" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required onpaste="return false;">
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
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                    <div class="text-center">
                        <small>Already registered? <a href="login">Login Here</a></small>
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
                    eyeIcon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    repeatpasswordField.attr('type', 'password');
                    eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
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

            $('#email').on('blur', function() {
                let email = $(this).val();
                if (email) {
                    $.ajax({
                        url: "",
                        type: 'POST',
                        data: { 
                            email_check: true,
                            user_email: email 
                        },
                        success: function(response) {
                            console.log(response);
                            let data = JSON.parse(response);
                            if (data.status === 200 && data.data.exist) {
                                $('#emailCheck').show();
                                submitButton.prop('disabled', true);
                            } else {
                                $('#emailCheck').hide();
                                submitButton.prop('disabled', false);
                            }
                        }
                    });
                }
            });
            $('#username').on('blur', function() {
                let username = $(this).val();
                if (username) {
                    $.ajax({
                        url: "",
                        type: 'POST',
                        data: { 
                            username_check: true,
                            user_username: username
                        },
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.status === 200 && data.data.exist) {
                                $('#usernameCheck').show();
                                submitButton.prop('disabled', true);
                            } else {
                                $('#usernameCheck').hide();
                                submitButton.prop('disabled', false);
                            }
                        }
                    });
                }
            });
            $("#registerForm").on("submit", function(e) {
                e.preventDefault();

                const name = $("#name").val();
                const username = $("#username").val();
                const org = $("#org").val();
                const email = $("#email").val();
                const password = $("#password").val();

                if (!name || !username || !email || !password) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Please fill out all fields."
                    });
                    return;
                }

                $.ajax({
                    url: "",
                    type: "POST",
                    data: {
                        user_register: true,
                        user_name: name,
                        user_username: username,
                        user_email: email,
                        user_password: password,
                        user_org: org
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 200) {
                            Swal.fire({
                                icon: "success",
                                title: "Registration Successful",
                                text: "You have successfully registered.",
                                showConfirmButton: false,
                                timer: 3000
                            });
                            window.location.href = "loginusername";
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Registration Failed",
                                text: response.message || "Please try again."
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to register. Please try again later."
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
