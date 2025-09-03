<?php
require_once 'config/database.php';

class Task {
    private $conn;
    private $table_name = "tasks";

    public $id;
    public $title;
    public $description;
    public $due_date;
    public $tutor_id;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create task
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET title=:title, description=:description, due_date=:due_date, 
                    tutor_id=:tutor_id, is_active=1, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":due_date", $this->due_date);
        $stmt->bindParam(":tutor_id", $this->tutor_id);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Get all tasks
    public function read() {
        $query = "SELECT t.id, t.title, t.description, t.due_date, t.created_at,
                         u.first_name, u.last_name
                FROM " . $this->table_name . " t
                LEFT JOIN users u ON t.tutor_id = u.id
                WHERE t.is_active = 1
                ORDER BY t.due_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get tasks by tutor
    public function readByTutor($tutor_id) {
        $query = "SELECT id, title, description, due_date, is_active, created_at
                FROM " . $this->table_name . " 
                WHERE tutor_id = :tutor_id 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tutor_id', $tutor_id);
        $stmt->execute();

        return $stmt;
    }

    // Get single task
    public function readOne() {
        $query = "SELECT t.id, t.title, t.description, t.due_date, t.created_at,
                         u.first_name, u.last_name
                FROM " . $this->table_name . " t
                LEFT JOIN users u ON t.tutor_id = u.id
                WHERE t.id = :id AND t.is_active = 1
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->due_date = $row['due_date'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    // Update task
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET title=:title, description=:description, due_date=:due_date, 
                    updated_at=NOW() 
                WHERE id=:id AND tutor_id=:tutor_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":due_date", $this->due_date);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":tutor_id", $this->tutor_id);

        return $stmt->execute();
    }

    // Delete task
    public function delete() {
        $query = "UPDATE " . $this->table_name . " 
                SET is_active = 0, updated_at = NOW() 
                WHERE id = :id AND tutor_id = :tutor_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':tutor_id', $this->tutor_id);

        return $stmt->execute();
    }
}
?>
