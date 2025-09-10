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

$dashboard_data = [];

if ($user_data['user_type'] === 'tutor') {
    // Get tutor dashboard data
    $query = "SELECT COUNT(*) as student_count FROM users WHERE user_type = 'student'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dashboard_data['student_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['student_count'];
    
    $query = "SELECT COUNT(*) as video_count FROM videos WHERE tutor_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_data['id']]);
    $dashboard_data['video_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['video_count'];
} else {
    // Get student dashboard data
    $query = "SELECT COUNT(*) as task_count FROM tasks";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dashboard_data['task_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['task_count'];
    
    $query = "SELECT COUNT(*) as video_count FROM videos";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $dashboard_data['video_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['video_count'];
}

echo json_encode(['success' => true, 'data' => $dashboard_data]);
?>