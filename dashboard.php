<?php
require_once 'helper.php';
session_start();
auth_check();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])){
        if ($_POST['action'] == 'create') {
            $val_fail = false;
            $required_parameters = ['user_id', 'name', 'description', 'start_date_time', 'end_date_time', 'max_capacity'];
            foreach ($required_parameters as $rp){
                if (!isset($_POST[$rp]) || empty($_POST[$rp])) {
                    $val_fail = true;
                    break;
                }
            }
            if ($val_fail) {
                echo json_encode(['status' => 'error','message' => 'All fields are required.']);
                exit;
            }
            $url = $api_url. 'eventis/headless/api/event_create';
            $headers = [
                "Authorization: Basic " . $api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Username: " . $api_username
            ];
            $data = http_build_query([
                'user_id' => $_POST['user_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
               'start_date_time' => $_POST['start_date_time'],
                'end_date_time' => $_POST['end_date_time'],
               'max_capacity' => $_POST['max_capacity']
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
        elseif ($_POST['action'] == 'update') {
            $val_fail = false;
            $required_parameters = ['event_id', 'name', 'description','start_date_time', 'end_date_time','max_capacity'];
            foreach ($required_parameters as $rp){
                if (!isset($_POST[$rp]) || empty($_POST[$rp])) {
                    $val_fail = true;
                    break;
                }
            }
            if ($val_fail) {
                echo json_encode(['status' => 'error','message' => 'All fields are required.']);
                exit;
            }
            $url = $api_url. 'eventis/headless/api/event_update';
            $headers = [
                "Authorization: Basic " . $api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Username: " . $api_username
            ];
            $data = http_build_query([
                'unique_id' => $_POST['event_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
               'start_date_time' => $_POST['start_date_time'],
                'end_date_time' => $_POST['end_date_time'],
               'max_capacity' => $_POST['max_capacity']
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
        elseif ($_POST['action'] == 'list') {
            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {            
                $url = $api_url. 'eventis/headless/api/event_list';
                $headers = [
                    "Authorization: Basic ". $api_key,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Username: ". $api_username
                ];
                $data = http_build_query([
                    'user_id' => $_POST['user_id']
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
        elseif ($_POST['action'] == 'single_details') {
            if (isset($_POST['event_id']) &&!empty($_POST['event_id'])) {
                $url = $api_url. 'eventis/headless/api/event_single_details';
                $headers = [
                    "Authorization: Basic ". $api_key,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Username: ". $api_username
                ];
                $data = http_build_query([
                    'event_id' => $_POST['event_id']
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
        elseif ($_POST['action'] == 'delete') {
            if (isset($_POST['event_id']) &&!empty($_POST['event_id'])) {
                $url = $api_url. 'eventis/headless/api/event_delete';
                $headers = [
                    "Authorization: Basic ". $api_key,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Username: ". $api_username
                ];
                $data = http_build_query([
                    'event_id' => $_POST['event_id']
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
        elseif ($_POST['action'] == 'status_change') {
            $val_fail2 = false;
            $required_parameters = ['event_id', 'status'];
            foreach ($required_parameters as $rp){
                if (!isset($_POST[$rp]) || $_POST[$rp] == NULL) {
                    var_dump($rp);
                    $val_fail2 = true;
                    break;
                }
            }
            if($val_fail2){
                echo json_encode(['status' => 'error','message' => 'All fields are required.']);
                exit;
            }
            else{
                $url = $api_url. 'eventis/headless/api/event_status_change';
                $headers = [
                    "Authorization: Basic ". $api_key,
                    "Content-Type: application/x-www-form-urlencoded",
                    "Username: ". $api_username
                ];
                $data = http_build_query([
                    'event_id' => $_POST['event_id'],
                   'status' => $_POST['status']
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
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Eventis - Dashboard</title>
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
        <div class="container mt-4">
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="card-title">Event Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start mb-3">
                        <button class="btn btn-primary" id="newEventBtn">New Event</button>
                    </div>
                    <table id="eventTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Capacity</th>
                                <th>Attendee</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Create Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalLabel">Create New Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="eventForm">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">

                            <div class="mb-3">
                                <label for="eventName" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="eventName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="eventDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="eventDescription" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="startDateTime" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="startDateTime" name="start_date_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="endDateTime" class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="endDateTime" name="end_date_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="maxCapacity" class="form-label">Max Capacity</label>
                                <input type="number" class="form-control" id="maxCapacity" name="max_capacity" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="updateEventModal" tabindex="-1" aria-labelledby="updateEventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateEventModalLabel">Update Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateEventForm">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" id="eventId" name="event_id">
                            
                            <div class="mb-3">
                                <label for="updateEventName" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="updateEventName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateEventDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="updateEventDescription" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="updateStartDateTime" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control" id="updateStartDateTime" name="start_date_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateEndDateTime" class="form-label">End Date & Time</label>
                                <input type="datetime-local" class="form-control" id="updateEndDateTime" name="end_date_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateMaxCapacity" class="form-label">Max Capacity</label>
                                <input type="number" class="form-control" id="updateMaxCapacity" name="max_capacity" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                let table = $('#eventTable').DataTable({
                    ajax: {
                        url: '',
                        type: 'POST',
                        data: { 
                            action: 'list',
                            user_id: '<?=$_SESSION['user_id'];?>',
                        },
                        dataSrc: function(response) {
                            let responsedata = JSON.parse(response.data);
                            if (response.status === 200) {
                                return responsedata.data;
                            } else {
                                return [];
                            }
                        },
                        error: function(xhr, status, error) {
                            alert(xhr.responseJSON.message);
                        }
                    },
                    columns: [
                        {
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        { 
                            data: 'unique_id',
                            render: function(data, type, row) {
                                return `<a href="./attend?event_id=${data}" class="event-link">${data}</a>`;
                            } 
                        },
                        { data: 'name' },
                        { data: 'description' },
                        { data:'max_capacity' },
                        {
                            data: 'attendee_count',
                            render: function(data, type, row) {
                                return `<a href="./attendee_data?event_id=${row.unique_id}" class="attendee-link">${data}</a>`;
                            }
                        },
                        { data:'start_datetime' },
                        { data: 'end_datetime' },
                        { data: 'create_date_time' },
                        {
                            data: 'is_active',
                            render: function(data, type, row) {
                                return `
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-warning btn-sm updateBtn" data-id="${row.unique_id}">Update</button>
                                        <button class="btn btn-danger btn-sm deleteBtn" data-id="${row.unique_id}">Delete</button>
                                        <button class="btn btn-primary btn-sm attendBtn" data-id="${row.unique_id}">${data === 1 ? 'Disable' : 'Enable'}</button>
                                    </div>
                                `;
                            }
                        }

                    ],
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
                });
                $('#eventForm').submit(function(e) {
                    e.preventDefault();

                    let startDate = new Date($('#startDateTime').val());
                    let endDate = new Date($('#endDateTime').val());
                    let currentDate = new Date();
                    let maxCapacity = parseInt($('#maxCapacity').val());

                    if (startDate < currentDate) {
                        Swal.fire("Validation Error", "Start date cannot be in the past.", "warning");
                        return;
                    }

                    if (endDate < startDate) {
                        Swal.fire("Validation Error", "End date cannot be before start date.", "warning");
                        return;
                    }

                    if (maxCapacity <= 0) {
                        Swal.fire("Validation Error", "Capacity must be greater than zero.", "warning");
                        return;
                    }

                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.status === 200) {
                                Swal.fire("Success", res.message, "success");
                                $('#eventModal').modal('hide');
                                $('#eventForm')[0].reset();
                                table.ajax.reload();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            Swal.fire("Error", "Something went wrong!", "error");
                        }
                    });
                });
                $('#updateEventForm').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: $(this).serialize() + '&action=update',
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.status === 200) {
                                Swal.fire("Success", res.message, "success");
                                $('#updateEventModal').modal('hide');
                                $('#updateEventForm')[0].reset();
                                $('#eventTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            Swal.fire("Error", "Something went wrong!", "error");
                        }
                    });
                });
                $('#newEventBtn').click(function() {
                    $('#eventModal').modal('show');
                });
                $(document).on('click', '.updateBtn', function() {
                    let eventId = $(this).data('id');
                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: { action: 'single_details', event_id: eventId },
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.status === 200) {
                                let event_data = JSON.parse(res.data);
                                let event = event_data.data;
                                $('#eventId').val(event.unique_id);
                                $('#updateEventName').val(event.name);
                                $('#updateEventDescription').val(event.description);
                                $('#updateStartDateTime').val(event.start_datetime);
                                $('#updateEndDateTime').val(event.end_datetime);
                                $('#updateMaxCapacity').val(event.max_capacity);
                                $('#updateEventModal').modal('show');
                            } else {
                                Swal.fire("Error", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            Swal.fire("Error", "Something went wrong!", "error");
                        }
                    });
                });
                $(document).on("click", ".deleteBtn", function() {
                    let eventId = $(this).data("id");
                    Swal.fire({
                        title: "Are you sure?",
                        text: "This event will be deleted permanently!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "",
                                type: "POST",
                                data: { action: "delete", event_id: eventId },
                                success: function(response) {
                                    
                                    let res = JSON.parse(response);
                                    if (res.status === 200) {
                                        Swal.fire("Deleted!", res.message, "success");
                                        $("#eventTable").DataTable().ajax.reload();
                                    } else {
                                        Swal.fire("Error!", res.message, "error");
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire("Error!", "Something went wrong!", "error");
                                }
                            });
                        }
                    });
                });
                $(document).on("click", ".attendBtn", function() {
                    let eventId = $(this).data("id");
                    let newStatus = $(this).text().trim() === "Disable" ? 0 : 1;
                    $.ajax({
                        url: "",
                        type: "POST",
                        data: { action: "status_change", event_id: eventId, status: newStatus },
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.status === 200) {
                                $("#eventTable").DataTable().ajax.reload();
                            } else {
                                Swal.fire("Error!", res.message, "error");
                            }
                        },
                        error: function(xhr) {
                            Swal.fire("Error!", "Something went wrong!", "error");
                        }
                    });
                });
            });
        </script>
    </body>
</html>