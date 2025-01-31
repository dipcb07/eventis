<?php
namespace api;

use PDO;
use PDOException;

class Attendee {

    private PDO $pdo;
    private string $table = 'attendees';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function register(string $unique_id, string $event_id, string $name, string $email): string {

        $response = [];
        $registration_date_time = date('Y-m-d H:i:s');
        $is_active = 1;

        $event = new Event($this->pdo);
        $eventInfo = json_decode($event->getinfo($event_id), true);

        if ($eventInfo['status'] !== 'success' || empty($eventInfo['data'])) {
            $response = ['status' => 'error', 'message' => 'Event does not exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        $eventData = $eventInfo['data'];

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE event_id = :event_id");
        $stmt->execute([':event_id' => $event_id]);
        $currentCount = $stmt->fetchColumn();

        if ($currentCount >= $eventData['max_capacity']) {
            $response = ['status' => 'error', 'message' => 'Event is already full'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE event_id = :event_id AND email = :email");
        $stmt->execute([':event_id' => $event_id, ':email' => $email]);
        $duplicateCount = $stmt->fetchColumn();

        if ($duplicateCount > 0) {
            $response = ['status' => 'error', 'message' => 'Attendee already registered for this event'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        try {
            $sql = "INSERT INTO {$this->table} (unique_id, is_active, event_id, name, email, registration_date_time) 
                    VALUES (:unique_id, :is_active, :event_id, :name, :email, :registration_date_time)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':unique_id' => $unique_id,
                ':is_active' => $is_active,
                ':event_id' => $event_id,
                ':name' => $name,
                ':email' => $email,
                ':registration_date_time' => $registration_date_time,
            ]);

            $response = ['status' => 'success', 'message' => 'Attendee registered successfully'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function listAttendees(string $event_id): string {
        try {
            $stmt = $this->pdo->prepare("SELECT name, email, registration_date_time FROM {$this->table} WHERE event_id = :event_id");
            $stmt->execute([':event_id' => $event_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = ['status' => 'success', 'data' => $data];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function update(string $unique_id, string $name, string $email, int $is_active): string {
        try {
            $sql = "UPDATE {$this->table} SET name = :name, email = :email, is_active = :is_active WHERE unique_id = :unique_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':unique_id' => $unique_id,
                ':name' => $name,
                ':email' => $email,
                ':is_active' => $is_active
            ]);

            $response = ['status' => 'success', 'message' => 'Attendee updated successfully'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function delete(string $unique_id): string {
        try {
            $sql = "DELETE FROM {$this->table} WHERE unique_id = :unique_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':unique_id' => $unique_id]);

            $response = ['status' => 'success', 'message' => 'Attendee deleted successfully'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
