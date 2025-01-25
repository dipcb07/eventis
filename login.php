<?php
    require_once 'helper.php';
    session_start();

    if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
        header('Location: index.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert_log') {

        loadEnv(realpath('.env'));
        $pdo = get_db($_ENV['DATABASE_HOST'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD'], $_ENV['DATABASE_NAME'], $_ENV['DATABASE_CHARSET']);    

        $session_id = $_POST['session_id'];
        $user_id = $_POST['user_id'];
        $logged_ip = $_POST['logged_ip'];
        $start_date_time = $_POST['start_date_time'];

        $sql = "SELECT COUNT(*) FROM user_log WHERE user_id = :user_id AND end_date_time = NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'User already logged in.']);
            $pdo = null;
            exit;
        }
        else{
            $sql = "INSERT INTO user_log (session_id, user_id, logged_ip, start_date_time) VALUES (:session_id, :user_id, :logged_ip, :start_date_time)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':logged_ip', $logged_ip);
            $stmt->bindParam(':start_date_time', $start_date_time);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['session_id'] = $session_id;
                echo json_encode(['success' => true, 'message' => 'User log added successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error inserting user log.']);
            }
        }
        $pdo = null;
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    <div class="text-center">
                        <small>Not registered? <a href="register.php">Register here</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#loginForm").on("submit", function(e) {
                e.preventDefault();

                const username = $("#username").val();
                const password = $("#password").val();
                const rememberMe = $("#rememberMe").is(":checked");

                if (!username || !password) {
                    Swal.fire({
                        icon: "warning",
                        title: "Missing Fields",
                        text: "Please fill out all fields.",
                        confirmButtonText: "OK",
                    });
                    return;
                }

                $.ajax({
                    url: "http://localhost/eventis/backend/api/user_login",
                    type: "POST",
                    headers: {
                        "Authorization": "Basic YmF0X3Rlc3RpbmdfYXBpXzI=",
                        "Username": "test_user",
                    },
                    contentType: "application/x-www-form-urlencoded",
                    data: {
                        username: username,
                        password: password
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 200) {
                            const session_id = response.data.session_id;
                            const user_id = response.data.user_id;
                            const logged_ip = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
                            const start_date_time = new Date().toISOString();

                            $.ajax({
                                url: '',
                                type: 'POST',
                                data: {
                                    action: 'insert_log',
                                    session_id: session_id,
                                    user_id: user_id,
                                    logged_ip: logged_ip,
                                    start_date_time: start_date_time
                                },
                                success: function(logResponse) {
                                    logResponse = JSON.parse(logResponse);
                                    if (logResponse.success) {
                                        window.location.href = "index.php";
                                    } else {
                                        Swal.fire({
                                            icon: "error",
                                            title: "Logging Error",
                                            text: "Error logging. Please try again.",
                                            confirmButtonText: "OK",
                                        });
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Login Failed",
                                text: response.message,
                                confirmButtonText: "Try Again",
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: "error",
                            title: "Request Failed",
                            text: "Login failed. Please try again.",
                            confirmButtonText: "OK",
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
