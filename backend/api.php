<?php
require_once "helper/config.php";
require_once "class/event.php";
require_once "class/user.php";

$username = authenticate($pdo, $endpoint);

switch ($endpoint) {

    case 'user_create':
        $user = new api\User($pdo);        
        $required_parameters = ['name', 'username', 'password', 'email'];
        $data = get_data($method, $required_parameters);

        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'missing parameter');
            handle_response(400, 'Missing parameters');
        }            
        
        $unique_id = generate_unique_id($data['name'] . $data['username']);
        $response = $user->create($unique_id, $data['name'], $data['username'], $data['email'], $data['password']);
        $response = json_decode($response, true);
        
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        
        log_request($pdo, $username, $method, 'success', $endpoint, $response['message']);
        handle_response(200, "user created successfully", ['user_id' => $unique_id]);
        
        break;

    case 'user_update':
        $user = new api\User($pdo);
        $required_parameters = ['unique_id'];

        $data = get_data($method, $required_parameters);

        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }            

        $response = $user->update($data['unique_id'], $data);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'user updated');
        handle_response(200, "user updated successfully");
        break;

    case 'user_delete':
        $user = new api\User($pdo);
        $required_parameters = ['unique_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $user->delete($data['unique_id']);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'user deleted');
        handle_response(200, "user deleted successfully");
        break;

    case 'user_login':
        $user = new api\User($pdo);
        $required_parameters = ['username', 'password'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $user->authenticate($data['username'], $data['password']);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'user logged in');
        handle_response(200, "user logged in successfully", ['user_id' => $response['user_id'], 'session_id' => $response['session_id']]);
        break;
    case 'user_username_check':
        $user = new api\User($pdo);
        $required_parameters = ['username'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $user->username_duplicate_check($data['username']);
        if($response){
            log_request($pdo, $username, $method, 'Success', $endpoint, 'username checked');
            handle_response(200, "username exist", ['exist' => true]);
        }
        else{
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'username not found');
            handle_response(200, 'username not found', ['exist' => false]);
        }
        break;
    
    case 'user_email_check':
        $user = new api\User($pdo);
        $required_parameters = ['email'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $user->email_duplicate_check($data['email']);
        if($response){
            log_request($pdo, $username, $method, 'Success', $endpoint, 'username checked');
            handle_response(200, "email exist", ['exist' => true]);
        }
        else{
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'email not found');
            handle_response(200, 'email not found', ['exist' => false]);
        }
        break;
    case 'event_create':
        $event = new api\Event($pdo);
        if ($method === 'POST') {
            $event = new api\Event($pdo);        
            $required_parameters = ['user_id', 'name', 'description', 'start_date_time', 'end_date_time', 'max_capacity'];
            $data = get_data($method, $required_parameters);
            $unique_id = generate_unique_id($data['name']);
            $response = $event->create($unique_id, $data['user_id'], $data['name'], $data['description'], $data['start_date_time'], $data['end_date_time'], $data['max_capacity']);
            $response = json_decode($response, true);
            if ($response['status'] === 'error') {
                log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
                handle_response(500, $response['message']);
            }
            log_request($pdo, $username, $method, 'Success', $endpoint, 'event created');
            handle_response(200, "event created successfully", ['event_id' => $unique_id]);
        }
        break;
        
    case 'event_update':
        $event = new api\Event($pdo);
        $required_parameters = ['unique_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $event->update($data['unique_id'], $data);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'event updated');
        handle_response(200, "event updated successfully");
        break;
        
    case 'event_delete':
        $event = new api\Event($pdo);
        $required_parameters = ['unique_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $event->delete($data['unique_id']);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'event deleted');
        handle_response(200, "event deleted successfully");
        break;
        
    case 'event_list':
        $event = new api\Event($pdo);
        $required_parameters = ['unique_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $event->listEvents();
        log_request($pdo, $username, $method, 'Success', $endpoint, 'List of events');
        handle_response(200, 'List of events', $response);
        break;
    
    case 'attendee_register':
        $attendee = new api\Attendee($pdo);
        $required_parameters = ['event_id', 'name', 'email'];
        $data = get_data($method, $required_parameters);
        $unique_id = generate_unique_id($data['name'] . $data['email']);
        $response = $attendee->register($unique_id, $data['event_id'], $data['name'], $data['email']);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'attendee registered');
        handle_response(200, "attendee registered successfully", ['attendee_id' => $unique_id]);
        break;
        
    case 'attendee_update':
        $attendee = new api\Attendee($pdo);
        $required_parameters = ['unique_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $attendee->update($data['unique_id'], $data['name'], $data['email'], $data['is_active']);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'attendee updated');
        handle_response(200, "attendee updated successfully");
        break;
        
    case 'attendee_delete':
        $attendee = new api\Attendee($pdo);
        $required_parameters = ['unique_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $attendee->delete($data['unique_id']);
        $response = json_decode($response, true);
        if ($response['status'] === 'error') {
            log_request($pdo, $username, $method, 'Failed', $endpoint, $response['message']);
            handle_response(500, $response['message']);
        }
        log_request($pdo, $username, $method, 'Success', $endpoint, 'attendee deleted');
        handle_response(200, "attendee deleted successfully");
        break;
        
    case 'attendee_list':
        $attendee = new api\Attendee($pdo);
        $required_parameters = ['event_id'];
        $data = get_data($method, $required_parameters);
        if (!$data) {
            log_request($pdo, $username, $method, 'Failed', $endpoint, 'Missing required parameters');
            handle_response(400, 'Missing required parameters');
        }
        $response = $attendee->listAttendees($data['event_id']);
        log_request($pdo, $username, $method, 'Success', $endpoint, 'List of attendees');
        handle_response(200, 'List of attendees', $response);
        break;
    default:
        handle_response(501, 'endpoint not implemented');
}
?>