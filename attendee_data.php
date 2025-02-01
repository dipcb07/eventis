<?php
require_once 'helper.php';
session_start();
auth_check();

$attendee_table = false;
$event_show = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
        $event_id = $_GET['event_id'];
        $attendee_table = true;
        if (isset($_GET['event_id'])) {
            $event_id = $_GET['event_id'];
            $event_show = true;
            $url = $api_url . 'eventis/headless/api/attendee_list';
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
            curl_close($curl);
        }
    }
    else{
        $event_show = true;
        $url = $api_url. 'eventis/headless/api/event_list';
        $headers = [
            "Authorization: Basic ". $api_key,
            "Content-Type: application/x-www-form-urlencoded",
            "Username: ". $api_username
        ];
        $data = http_build_query(['user_id' => $_SESSION['user_id']]);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $data = json_decode($data, true);
        $render_data = json_decode($data['data'], true);
        $render_data = $render_data['data'];
        curl_close($curl);
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
            if ($_POST['action'] == 'delete') {
                $url = $api_url . 'eventis/headless/api/attendee_delete';
                $headers = [
                    "Authorization: Basic " . $api_key,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Username: " . $api_username
                ];
                $data = http_build_query([
                    'event_id' => $_POST['event_id'],
                    'attendee_id' => $_POST['attendee_id'],
                ]);
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
            if ($_POST['action'] == 'status_change') {
                $url = $api_url . 'eventis/headless/api/attendee_status_change';
                $headers = [
                    "Authorization: Basic " . $api_key,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Username: " . $api_username
                ];
                $data = http_build_query([
                    'event_id' => $_POST['event_id'],
                    'attendee_id' => $_POST['attendee_id'],
                    'status' => $_POST['status'],
                ]);
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
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Eventis - Attendee</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
        <style>
        a {
            text-decoration: none;
        }
        .brand {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.5rem;
            font-weight: bold;
        }
        table.dataTable th {
            text-align: center;
        }
        table.dataTable tr {
            text-align: center;
        }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand brand" href="#">Eventis</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        if ($currentPage == 'dashboard.php') {
                            echo '<a href="attendee_data" class="btn btn-primary">Events</a>';
                        } elseif ($currentPage == 'attendee_data.php') {
                            echo '<a href="dashboard" class="btn btn-primary">Dashboard</a>';
                        }
                    ?>
                    <a href="logout.php" class="btn btn-secondary ms-2">Logout</a>
                </div>
            </div>
        </nav>
        <?php if ($attendee_table): ?>
        <div class="container mt-4">
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="card-title">Attendee Details</h5>
                </div>
                <div class="card-body">
                    <table id="attendeeTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Reg Date Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($render_data as $key => $value): ?>
                            <tr>
                                <td><?php echo ++$key; ?></td>
                                <td><?php echo $value['unique_id']; ?></td>
                                <td><?php echo $value['name']; ?></td>
                                <td><?php echo $value['email']; ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($value['registration_date_time'])); ?></td>
                                <td>
                                    <button class="btn btn-danger btn-sm statusAttendee"><?php echo $value['is_active'] == 1 ? 'Deactivate' : 'Activate'; ?></button>
                                    <button class="btn btn-danger btn-sm deleteAttendee">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php elseif ($event_show): ?>
            <div class="container mt-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="card-title">Event List</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($render_data as $key => $value): ?>
                                <div class="col-12 mb-3">
                                    <a href="?event_id=<?php echo $value['unique_id']; ?>" class="btn btn-primary w-100 text-left">
                                        <?php echo $value['name']; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <script>
            $(document).ready(function() {
                var table = $('#attendeeTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            text: 'Copy',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'csv',
                            text: 'CSV',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Excel',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            exportOptions: {
                                columns: ':visible:not(:last-child)'
                            }
                        }
                    ],
                    scrollX: true,  
                });

                $('body').on('click', '.deleteAttendee', function() {
                    var row = $(this).closest('tr');
                    var attendeeId = row.find('td:eq(1)').text();
                    var eventId = '<?php echo $event_id; ?>';
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You are about to delete this attendee!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '',
                                type: 'POST',
                                data: {
                                    action: 'delete',
                                    attendee_id: attendeeId,
                                    event_id: eventId
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'The attendee has been deleted.',
                                        'success'
                                    );
                                    table.row(row).remove().draw();
                                },
                                error: function() {
                                    Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                                }
                            });
                        }
                    });
                });

                $('body').on('click', '.statusAttendee', function() {
                    var row = $(this).closest('tr');
                    var attendeeId = row.find('td:eq(1)').text();
                    var eventId = '<?php echo $event_id; ?>';
                    var status = $(this).text() === 'Deactivate' ? 'deactivate' : 'activate';

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You are about to " + status + " this attendee!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: status.charAt(0).toUpperCase() + status.slice(1),
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '',
                                type: 'POST',
                                data: {
                                    action: 'status_change',
                                    attendee_id: attendeeId,
                                    event_id: eventId,
                                    status: status
                                },
                                success: function(response) {
                                    Swal.fire(
                                        status.charAt(0).toUpperCase() + status.slice(1) + 'd!',
                                        'The attendee status has been changed.',
                                        'success'
                                    );
                                    row.find('.statusAttendee').text(status === 'deactivate' ? 'Activate' : 'Deactivate');
                                },
                                error: function() {
                                    Swal.fire('Error!', 'An error occurred while changing the status.', 'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    </body>
</html>
