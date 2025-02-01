<?php
require_once "helper.php";

$captchasitekey = $_ENV['CLOUDFLARE_TURNSTILE_SITE_KEY']; 
$captchaSecret = $_ENV['CLOUDFLARE_TURNSTILE_SECRET_KEY'];
$mail_api_key = $_ENV['BREVO_API_KEY'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $event_show = false;
    if (isset($_GET['event_id'])) {
        $event_id = $_GET['event_id'];
        $event_show = true;
        $url = $api_url . 'eventis/headless/api/event_single_details';
        $headers = [
            "Authorization: Basic " . $api_key,
            "Content-Type: application/x-www-form-urlencoded",
            "Username: " . $api_username
        ];
        $data = http_build_query(['event_id' => $event_id]);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $data = json_decode($data, true);
        $render_data = json_decode($data['data'], true);
        $render_data = $render_data['data'];
        if(empty($render_data)){
          echo json_encode(['status' => 'error', 'message' => 'No event found with the provided ID.']);
          exit;
        }
        curl_close($curl);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Event ID is required.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if($captchaSecret){
        $captchaResponse = $_POST['cf-turnstile-response'] ?? null;
        if (!$captchaResponse) {
            echo "Please complete the CAPTCHA";
        }
        $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret' => $captchaSecret,
            'response' => $captchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($verifyUrl, false, $context);
        $responseData = json_decode($result, true);
        if (!$responseData['success']) {
            echo "CAPTCHA verification failed. Please try again";
        }
    }
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'register') {
        $url = $api_url . 'eventis/headless/api/attendee_register';
        $headers = [
            "Authorization: Basic " . $api_key,
            "Content-Type: application/x-www-form-urlencoded",
            "Username: " . $api_username
        ];
        $data = http_build_query([
            'event_id' => $_POST['event_id'],
            'name' => $_POST['name'],
            'email' => $_POST['email']
        ]);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($response, true);
        if($resp['status'] == 200) {
            if($mail_api_key){
                $subject = "Event Registration Confirmation";
                $content = "<p>Hi,<br><br></p><p>Thank you for registering</p><p>Looking forward to seeing you there!</p><br><p>Best Regards,<br>Eventis Team</p>";
                $mail_send = mail_send($mail_api_key, $_POST['email'], $_POST['name'], $subject, $content);
            }
            echo $response;
            exit;
        }
        }
    }
}
?>

<?php if ($event_id): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventis - Attendee Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
      .disabled-btn {
          pointer-events: none;
          opacity: 0.6;
      }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Registration Form</h4>
                    </div>
                    <div class="card-body">
                        <form id="attendForm" method="POST">
                            <input type="hidden" name="event_id" value="<?php echo $event_id ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <?php if($captchasitekey): ?>
                            <div class="cf-turnstile" data-sitekey="<?=$captchasitekey?>" data-theme="light" data-callback="turnstileCallback"></div><br>
                            <?php endif;?>
                            <button id="attend_submit" type="submit" class="btn btn-primary  disabled-btn">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Event Information</h4>
                    </div>
                    <div class="card-body">
                        <h5><?= $render_data['name'] ?></h5>
                        <h7><i><?= $render_data['user_org'] ?? $render_data['user_name'] ?></i></h7><br>
                        <p><?= $render_data['description'] ?></p>
                        <p><strong>Start DateTime: </strong><?=$render_data['start_datetime']?></p>
                        <p><strong>End DateTime: </strong><?=$render_data['end_datetime']?></span></p>
                        <p><strong>Capacity: </strong> <?= $render_data['max_capacity'] ?></p>
                        <p><strong>Registered So Far: </strong><?= $render_data['attendee_count'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function turnstileCallback(token) {
            let loginButton = document.getElementById('attend_submit');
            loginButton.classList.remove('disabled-btn');
            loginButton.style.pointerEvents = "auto";
            loginButton.style.opacity = "1";
        }

        $("#attendForm").submit(function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Registering...',
                text: 'Please wait for confirmation',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                type: "POST",
                url: "",
                data: $(this).serialize() + "&action=register",
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.status === 200) {
                      Swal.fire({
                          title: "Success!",
                          text: "You have been registered.",
                          icon: "success",
                          allowOutsideClick: false,
                          timer: 5000,
                          timerProgressBar: true,
                      }).then(() => {
                          location.reload();
                      });
                    } else {
                        Swal.fire("Error!", res.message, "error");
                    }
                    turnstile.reset();
                    let loginButton = document.getElementById('attend_submit');
                    loginButton.classList.add('disabled-btn');
                    loginButton.style.pointerEvents = "none";
                    loginButton.style.opacity = "0.6";
                }
            });
        });
    </script>
</body>
</html>
<?php endif; ?>
