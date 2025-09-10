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

$progress_data = [];

if ($user_data['user_type'] === 'student') {
    // Get student progress
    $query = "SELECT COUNT(*) as completed_tasks FROM task_submissions WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_data['id']]);
    $progress_data['completed_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed_tasks'];
    
    $query = "SELECT COUNT(*) as watched_videos FROM video_views WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_data['id']]);
    $progress_data['watched_videos'] = $stmt->fetch(PDO::FETCH_ASSOC)['watched_videos'];
}

echo json_encode(['success' => true, 'progress' => $progress_data]);
?>