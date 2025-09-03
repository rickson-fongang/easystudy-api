<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $user_type;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET first_name=:first_name, last_name=:last_name, email=:email, 
                    phone=:phone, password=:password, user_type=:user_type, 
                    is_active=1, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->user_type = htmlspecialchars(strip_tags($this->user_type));

        // Hash password
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        // Bind values
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":user_type", $this->user_type);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Check if email exists
    public function emailExists() {
        $query = "SELECT id, first_name, last_name, email, phone, password, user_type, is_active 
                FROM " . $this->table_name . " 
                WHERE email = :email LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->password = $row['password'];
            $this->user_type = $row['user_type'];
            $this->is_active = $row['is_active'];
            return true;
        }

        return false;
    }

    // Get user by ID
    public function readOne() {
        $query = "SELECT id, first_name, last_name, email, phone, user_type, is_active, created_at 
                FROM " . $this->table_name . " 
                WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->user_type = $row['user_type'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    // Get all students (for tutors)
    public function getStudents() {
        $query = "SELECT id, first_name, last_name, email, phone, is_active, created_at 
                FROM " . $this->table_name . " 
                WHERE user_type = 'student' 
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Update user status
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table_name . " 
                SET is_active = :status, updated_at = NOW() 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    // Delete user
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
?>
