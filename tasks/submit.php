<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/Task.php";
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
if ($user_data['user_type'] !== 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only students can submit tasks']);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$database = new Database();
$db = $database->getConnection();
$task = new Task($db);

if ($task->submitTask($data->task_id, $user_data['id'], $data->submission)) {
    echo json_encode(['success' => true, 'message' => 'Task submitted successfully']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Failed to submit task']);
}
?>