<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Auth.php";

$headers = getallheaders();
$token = $headers['Authorization'] ?? '';
$token = str_replace('Bearer ', '', $token);

if (!Auth::validateToken($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$user_data = Auth::getTokenData($token);
$database = new Database();
$db = $database->getConnection();

$query = "SELECT m.*, u.first_name, u.last_name FROM messages m 
          JOIN users u ON m.sender_id = u.id 
          WHERE m.sender_id = ? OR m.receiver_id = ? 
          ORDER BY m.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_data['id'], $user_data['id']]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'messages' => $messages]);
?>