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

$data = json_decode(file_get_contents("php://input"));
$user_data = Auth::getTokenData($token);

if ($user_data['user_type'] !== 'tutor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only tutors can update tasks']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$task = new Task($db);
$task->id = $data->task_id;
$task->title = $data->title;
$task->description = $data->description;
$task->deadline = $data->deadline;

if ($task->update()) {
    echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Failed to update task']);
}
?>