<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/User.php";
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
if ($user_data['user_type'] !== 'tutor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$students = $user->getAllStudents();
echo json_encode(['success' => true, 'students' => $students]);
?>