<?php
    session_start();
    if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
        header('Location: index.php');
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
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                <i id="eyeIcon" class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                    <div class="text-center">
                        <small>Already registered? <a href="login.php">Login here</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIcon');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.removeClass('bi bi-eye-fill');
                    eyeIcon.addClass('bi bi-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.removeClass('bi bi-eye-slash');
                    eyeIcon.addClass('bi bi-eye-fill');
                }
            });

            $('#email').on('blur', function() {
                let email = $(this).val();
                if (email) {
                    $.ajax({
                        url: 'http://localhost/eventis/backend/api/user_email_check',
                        type: 'POST',
                        headers: {
                            "Authorization": "Basic YmF0X3Rlc3RpbmdfYXBpXzI=",
                            "Content-Type": "application/x-www-form-urlencoded",
                            "Username": "test_user"
                        },
                        data: { email: email },
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.status === 200 && data.data.exist) {
                                $('#emailCheck').show();
                            } else {
                                $('#emailCheck').hide();
                            }
                        }
                    });
                }
            });
            $('#username').on('blur', function() {
                let username = $(this).val();
                if (username) {
                    $.ajax({
                        url: 'http://localhost/eventis/backend/api/user_username_check',
                        type: 'POST',
                        headers: {
                            "Authorization": "Basic YmF0X3Rlc3RpbmdfYXBpXzI=",
                            "Content-Type": "application/x-www-form-urlencoded",
                            "Username": "test_user"
                        },
                        data: { username: username },
                        success: function(response) {
                            let data = JSON.parse(response);
                            if (data.status === 200 && data.data.exist) {
                                $('#usernameCheck').show();
                            } else {
                                $('#usernameCheck').hide();
                            }
                        }
                    });
                }
            });
            $("#registerForm").on("submit", function(e) {
                e.preventDefault();

                const name = $("#name").val();
                const username = $("#username").val();
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
                    url: "http://localhost/eventis/backend/api/user_create",
                    type: "POST",
                    headers: {
                        "Authorization": "Basic YmF0X3Rlc3RpbmdfYXBpXzI=",
                        "Content-Type": "application/x-www-form-urlencoded",
                        "Username": "test_user"
                    },
                    data: {
                        name: name,
                        username: username,
                        email: email,
                        password: password
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 200) {
                            window.location.href = "login.php";
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
