<?php
class Database {
    private $host = "tramway.proxy.rlwy.net";          // Railway host
    private $db_name = "railway";                      // Railway database name
    private $username = "root";                        // Railway username
    private $password = "ZHwqVHydwhBhKKKRUNWeLxzhvdywkmPn"; // Railway password
    private $port = "42205";                           // Railway port
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            // Optional: throw exceptions on errors
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo json_encode([
                "success" => false,
                "message" => "Database connection error: " . $exception->getMessage()
            ]);
            exit();
        }

        return $this->conn;
    }
}
?>
