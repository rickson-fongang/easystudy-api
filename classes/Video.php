<?php
require_once 'config/database.php';

class Video {
    private $conn;
    private $table_name = "videos";

    public $id;
    public $title;
    public $description;
    public $file_path;
    public $file_size;
    public $duration;
    public $time_limit;
    public $tutor_id;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create video
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET title=:title, description=:description, file_path=:file_path, 
                    file_size=:file_size, duration=:duration, time_limit=:time_limit, 
                    tutor_id=:tutor_id, is_active=1, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":file_path", $this->file_path);
        $stmt->bindParam(":file_size", $this->file_size);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":time_limit", $this->time_limit);
        $stmt->bindParam(":tutor_id", $this->tutor_id);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Get all videos
    public function read() {
        $query = "SELECT v.id, v.title, v.description, v.file_path, v.file_size, 
                         v.duration, v.time_limit, v.is_active, v.created_at,
                         u.first_name, u.last_name
                FROM " . $this->table_name . " v
                LEFT JOIN users u ON v.tutor_id = u.id
                WHERE v.is_active = 1
                ORDER BY v.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get videos by tutor
    public function readByTutor($tutor_id) {
        $query = "SELECT id, title, description, file_path, file_size, duration, 
                         time_limit, is_active, created_at
                FROM " . $this->table_name . " 
                WHERE tutor_id = :tutor_id 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tutor_id', $tutor_id);
        $stmt->execute();

        return $stmt;
    }

    // Get single video
    public function readOne() {
        $query = "SELECT v.id, v.title, v.description, v.file_path, v.file_size, 
                         v.duration, v.time_limit, v.is_active, v.created_at,
                         u.first_name, u.last_name
                FROM " . $this->table_name . " v
                LEFT JOIN users u ON v.tutor_id = u.id
                WHERE v.id = :id AND v.is_active = 1
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->file_path = $row['file_path'];
            $this->file_size = $row['file_size'];
            $this->duration = $row['duration'];
            $this->time_limit = $row['time_limit'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    // Update video
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET title=:title, description=:description, time_limit=:time_limit, 
                    updated_at=NOW() 
                WHERE id=:id AND tutor_id=:tutor_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":time_limit", $this->time_limit);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":tutor_id", $this->tutor_id);

        return $stmt->execute();
    }

    // Delete video
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
