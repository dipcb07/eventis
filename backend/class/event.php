<?php
namespace event;

use PDO;


class Event {

    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createEvent($name, $description, $date, $max_capacity) {
        $sql = "INSERT INTO events (name, description, date, max_capacity, create_date_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $description, $date, $max_capacity]);
        return ['message' => 'Event created!'];
    }

    public function updateEvent($id, $name, $description, $date, $max_capacity) {
        $stmt = $this->pdo->prepare("UPDATE events SET name = ?, description = ?, date = ?, max_capacity = ? WHERE id = ?");
        $stmt->execute([$name, $description, $date, $max_capacity, $id]);
        return ['message' => 'Event updated!'];
    }

    public function deleteEvent($id) {
        $stmt = $this->pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return ['message' => 'Event deleted!'];
    }

    public function listEvents() {
        $stmt = $this->pdo->prepare("SELECT * FROM events");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
