<?php
if (file_exists(__DIR__.'/install.lock')) {
    echo '<script>window.location.href = "/eventis/login";</script>';
    exit;
}

function setupEnvFiles() {
    $errors = [];
    $directory = __DIR__;
    if (!is_writable($directory)) {
        $errors[] = "Directory not writable: " . realpath($directory);
        return $errors;
    }
    if (!file_exists(".env")) {
        if (!copy(".env.example", ".env")) {
            $errors[] = "Failed to create .env file (check permissions)";
        } else {
            chmod(".env", 0644);
        }
    }
    if (!file_exists(".htaccess")) {
        if (!copy(".htaccess.example", ".htaccess")) {
            $errors[] = "Failed to create .htaccess file (check permissions)";
        } else {
            chmod(".htaccess", 0644);
        }
    }
    return $errors;
}

function getEnvData() {
    $envData = [];
    if (file_exists(".env")) {
        $lines = file(".env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), "=") !== false) {
                list($key, $value) = explode("=", $line, 2);
                $envData[trim($key)] = trim($value);
            }
        }
    }
    return $envData;
}

function updateEnvFile($newData) {
    $content = "";
    foreach ($newData as $key => $value) {
        $content .= "$key=$value\n";
    }
    file_put_contents(".env", $content);
}

function executeSqlFile($host, $port, $user, $pass, $dbname, $sqlFile) {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $queries = file_get_contents($sqlFile);
        if ($queries === false) {
            return "SQL file not found: $sqlFile";
        }
        $queries = explode(';', $queries);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function checkDatabaseConnection($host, $port, $user, $pass, $dbname) {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function isDatabaseInitialized($host, $port, $user, $pass, $dbname) {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function checkHeadlessAPI($url, $username, $apikey) {
    $headers = [
        "Authorization: Basic " . $apikey,
        "Content-Type: application/x-www-form-urlencoded",
        "Username: " . $username
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "CURL Error: " . curl_error($ch);
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return "HTTP Error: $httpCode";
    }
    
    $result = json_decode($response, true);
    return isset($result['status']) && $result['status'] == 200;
}

$errors = [];
$success = false;
$showSetup = isset($_GET['setup']);
$envData = getEnvData();

if ($showSetup && empty($envData)) {
    $fileErrors = setupEnvFiles();
    if (!empty($fileErrors)) {
        $errors = array_merge($errors, $fileErrors);
    }
    $envData = getEnvData();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_env'])) {
    $requiredFields = [
        'APP_NAME', 'APP_ENV', 'APP_URL', 'APP_TIMEZONE',
        'DATABASE_HOST', 'DATABASE_HOST_PORT', 'DATABASE_USER',
        'DATABASE_PASSWORD', 'DATABASE_NAME', 'DATABASE_CHARSET',
        'HEADLESS_API_USERNAME', 'HEADLESS_API_KEY'
    ];
    
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        $errors[] = "Missing required fields: " . implode(', ', $missingFields);
    } else {
        updateEnvFile($_POST);
        $envData = getEnvData();
        
        $dbCheck = checkDatabaseConnection(
            $_POST['DATABASE_HOST'],
            $_POST['DATABASE_HOST_PORT'],
            $_POST['DATABASE_USER'],
            $_POST['DATABASE_PASSWORD'],
            $_POST['DATABASE_NAME']
        );
        
        if ($dbCheck !== true) {
            $errors[] = "Database connection failed: " . $dbCheck;
        } else {
            $needsInitialization = !isDatabaseInitialized(
                $_POST['DATABASE_HOST'],
                $_POST['DATABASE_HOST_PORT'],
                $_POST['DATABASE_USER'],
                $_POST['DATABASE_PASSWORD'],
                $_POST['DATABASE_NAME']
            );

            if ($needsInitialization) {
                $sqlResult = executeSqlFile(
                    $_POST['DATABASE_HOST'],
                    $_POST['DATABASE_HOST_PORT'],
                    $_POST['DATABASE_USER'],
                    $_POST['DATABASE_PASSWORD'],
                    $_POST['DATABASE_NAME'],
                    __DIR__.'/eventis.sql'
                );
                
                if ($sqlResult !== true) {
                    $errors[] = "SQL execution failed: " . $sqlResult;
                }
            } else {
                try {
                    $dsn = "mysql:host={$_POST['DATABASE_HOST']};port={$_POST['DATABASE_HOST_PORT']};dbname={$_POST['DATABASE_NAME']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $_POST['DATABASE_USER'], $_POST['DATABASE_PASSWORD']);
                    $stmt = $pdo->query("SELECT COUNT(*) FROM api_secret");
                    if ($stmt->fetchColumn() < 1) {
                        $errors[] = "Database appears initialized but has no data";
                    }
                } catch (PDOException $e) {
                    $errors[] = "Database verification failed: " . $e->getMessage();
                }
            }

            if (empty($errors)) {
                $apiCheck = checkHeadlessAPI(
                    rtrim($_POST['APP_URL'], '/') . '/eventis/headless/api/test_api',
                    $_POST['HEADLESS_API_USERNAME'],
                    $_POST['HEADLESS_API_KEY']
                );
                
                if ($apiCheck !== true) {
                    $errors[] = "Headless API verification failed: " . $apiCheck;
                } else {
                    file_put_contents(__DIR__.'/install.lock', "Installation completed: ".date('Y-m-d H:i:s'));
                    $success = true;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventis Installer</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .installer-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 50px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 600;
            color: #2d3748;
        }
        .required::after {
            content: "*";
            color: #e53e3e;
            margin-left: 3px;
        }
        input.form-control {
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        input.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .btn-primary {
            background: #667eea;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #764ba2;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if (!$showSetup && !$success): ?>
                    <div class="installer-card text-center">
                        <h1 class="mb-4">Welcome to Eventis</h1>
                        <p class="lead mb-4">Let's get your application configured</p>
                        <button class="btn btn-primary btn-lg" onclick="startInstallation()">
                            Start Installation
                        </button>
                    </div>
                <?php else: ?>
                    <div class="installer-card">
                        <h3 class="mb-4">Configuration Settings</h3>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mb-4">
                                <?php foreach ($errors as $error): ?>
                                    <div><?= $error ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <?php foreach ($envData as $key => $value): ?>
                                <div class="form-group">
                                    <label class="<?= in_array($key, ['CLOUDFLARE_TURNSTILE_SITE_KEY', 'CLOUDFLARE_TURNSTILE_SECRET_KEY', 'BREVO_API_KEY']) ? '' : 'required' ?>">
                                        <?= $key ?>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="<?= $key ?>" 
                                           value="<?= htmlspecialchars($value) ?>"
                                           placeholder="<?= in_array($key, ['CLOUDFLARE_TURNSTILE_SITE_KEY', 'CLOUDFLARE_TURNSTILE_SECRET_KEY', 'BREVO_API_KEY']) ? 'Optional' : 'Required' ?>"
                                           <?= in_array($key, ['CLOUDFLARE_TURNSTILE_SITE_KEY', 'CLOUDFLARE_TURNSTILE_SECRET_KEY', 'BREVO_API_KEY']) ? '' : 'required' ?>>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='installer.php'">
                                    Exit
                                </button>
                                <button type="submit" name="update_env" class="btn btn-primary">
                                    Save Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        <?php if ($success): ?>
            Swal.fire({
                title: 'Success!',
                text: 'Installation completed successfully!',
                icon: 'success',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.href = '/eventis/login';
            });
        <?php endif; ?>

        function startInstallation() {
            Swal.fire({
                title: 'Initializing...',
                html: '<div class="spinner-border text-primary" role="status"></div>',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1000
            }).then(() => {
                window.location.search = 'setup=true';
            });
        }
    </script>
</body>
</html>