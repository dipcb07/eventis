<?php
namespace api;
require_once "attendee.php";

use PDO;
use PDOException;
use \api\Attendee;

class Event {

    private $pdo;
    private $table = 'events';
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(string $unique_id, string $user_id, string $name, string $description, string $start_date_time, string $end_date_time, string $max_capacity) {

        $start_date = date('Y-m-d', strtotime($start_date_time));
        $end_date = date('Y-m-d', strtotime($end_date_time));
        $start_time = date('H:i:s', strtotime($start_date_time));
        $end_time = date('H:i:s', strtotime($end_date_time));
        $create_date_time = date('Y-m-d H:i:s');
        $max_capacity = (int)$max_capacity;
        $is_active = 1;
        $user_id = $user_id;
        $response = [];

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $this->table WHERE name = :name AND start_date = :start_date AND end_date = :end_date AND start_time = :start_time AND end_time = :end_time"); 
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $response = ['status' => 'error', 'message' => 'Event already exists'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        $user = new User($this->pdo);
        $user_exist = $user->exist($user_id);
        if (!$user_exist) {
            $response = ['status' => 'error', 'message' => 'User does not exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        try {
            $sql = "INSERT INTO $this->table (unique_id, user_id, is_active, name, description, start_date, end_date, start_time, end_time, max_capacity, create_date_time) 
                    VALUES (:unique_id, :user_id, :is_active, :name, :description, :start_date, :end_date, :start_time, :end_time, :max_capacity, :create_date_time)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':unique_id', $unique_id);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':max_capacity', $max_capacity, PDO::PARAM_INT);
            $stmt->bindParam(':create_date_time', $create_date_time);
            $stmt->execute();
            $response = ['status' => 'success', 'message' => 'created'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function update(string $unique_id, array $data) {
        
        $sql1 = "SELECT COUNT(*) FROM $this->table WHERE unique_id = :unique_id"; 
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([':unique_id' => $unique_id]);
        $count = $stmt1->fetchColumn();
        if ($count == 0) {
            $response = ['status' => 'error', 'message' => 'Event does not exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        $update_date_time = date('Y-m-d H:i:s');
        $setClauses = [];
        $response = [];
    
        $sql = "UPDATE $this->table SET update_date_time = :update_date_time";
        $binds = [
            ':unique_id' => $unique_id,
            ':update_date_time' => $update_date_time
        ];

        foreach ($data as $param => $value) {
            if (!in_array($param, ['is_active', 'name', 'description', 'start_date_time', 'end_date_time', 'max_capacity'])) {
                $response = json_encode(['status' => 'error', 'message' => 'Invalid parameter'], JSON_UNESCAPED_UNICODE);
            }
            if($param == 'unique_id') continue;
            if($param == 'start_date_time'){
                $param_temp1 = 'start_date';
                $value_temp1 = date('Y-m-d', strtotime($value));
                $setClauses[] = "$param_temp1 = :$param_temp1";
                $binds[":$param_temp1"] = $value_temp1;
                $param_temp2 = 'start_time';
                $value_temp2 = date('H:i:s', strtotime($value));
                $setClauses[] = "$param_temp2 = :$param_temp2";
                $binds[":$param_temp2"] = $value_temp2;
                continue;    
            }
            if($param == 'end_date_time'){
                $param_temp1 = 'end_date';
                $value_temp1 = date('Y-m-d', strtotime($value));
                $setClauses[] = "$param_temp1 = :$param_temp1";
                $binds[":$param_temp1"] = $value_temp1;
                $param_temp2 = 'end_time';
                $value_temp2 = date('H:i:s', strtotime($value));
                $setClauses[] = "$param_temp2 = :$param_temp2";
                $binds[":$param_temp2"] = $value_temp2;
                continue;    
            }
            $setClauses[] = "$param = :$param";
            $binds[":$param"] = $value;
        }
    
        if (empty($setClauses)) {
            $response = json_encode(['status' => 'error', 'message' => 'No valid parameter'], JSON_UNESCAPED_UNICODE);
        }

        $sql .= ' , ' . implode(', ', $setClauses) . ' WHERE unique_id = :unique_id';
    
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($binds as $param => $value) {
                $stmt->bindParam($param, $value);
            }
            $stmt->execute();
            $response = ['status' => 'success', 'message' => 'Updated successfully'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function delete(string $unique_id) {
        $sql1 = "SELECT COUNT(*) FROM $this->table WHERE unique_id = :unique_id"; 
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([':unique_id' => $unique_id]);
        $count = $stmt1->fetchColumn();
        if ($count == 0) {
            $response = ['status' => 'error', 'message' => 'Event does not exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        try {
            $sql = "DELETE FROM $this->table WHERE unique_id = :unique_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':unique_id' => $unique_id]);
            $response = ['status' => 'success', 'message' => 'Event deleted successfully'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getinfo(string $unique_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE unique_id = :unique_id");
            $stmt->execute([':unique_id' => $unique_id]);
            $response = ['status' => 'success', 'data' => $stmt->fetch(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public function listEvents(string $user_unique_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_unique_id]);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $eventsWithAttendees = [];

            $attendee = new Attendee($this->pdo);
            foreach ($events as $event) {
                $event_id = $event['unique_id'];
                $start_date = $event['start_date'];
                $start_time = $event['start_time'];
                $end_date = $event['end_date'];
                $end_time = $event['end_time'];
                $attendeeResponse = $attendee->attendee_count($event_id);
                $attendeeData = json_decode($attendeeResponse, true);
                $event['start_datetime'] = "$start_date $start_time";
                $event['end_datetime'] = "$end_date $end_time";
                $event['attendee_count'] = $attendeeData['data'];
                $eventsWithAttendees[] = $event;
            }
            $response = ['status' => 'success', 'data' => $eventsWithAttendees];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function eventDetails(string $event_id){
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE unique_id = :event_id AND is_active = 1");
            $stmt->execute([':event_id' => $event_id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            $start_date = $event['start_date'];
            $start_time = $event['start_time'];
            $end_date = $event['end_date'];
            $end_time = $event['end_time'];
            $attendee = new Attendee($this->pdo);
            $attendeeResponse = $attendee->attendee_count($event_id);
            $attendeeData = json_decode($attendeeResponse, true);
            $event['attendee_count'] = $attendeeData['data'];
            $user = new User($this->pdo);
            $userResponse = $user->getinfo($event['user_id']);
            $userData = json_decode($userResponse, true);
            $event['user_name'] = $userData['data']['name'];
            $event['user_org'] = $userData['data']['org'];
            $event['start_datetime'] = "$start_date $start_time";
            $event['end_datetime'] = "$end_date $end_time";
            $response = ['status' => 'success', 'data' => $event];
        } catch (PDOException $e) {
            $response = ['status' => 'error','message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function status_change(string $event_id, int $status){
        try {
            $sql = "UPDATE $this->table SET is_active = :status WHERE unique_id = :event_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':event_id' => $event_id, ':status' => $status]);
            $response = ['status' => 'success','message' => 'Event disabled successfully'];
        } catch (PDOException $e) {
            $response = ['status' => 'error','message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
