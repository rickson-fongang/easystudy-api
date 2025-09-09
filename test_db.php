<?php
require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?>
