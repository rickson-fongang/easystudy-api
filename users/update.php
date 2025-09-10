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

$data = json_decode(file_get_contents("php://input"));
$user_data = Auth::getTokenData($token);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$user->id = $user_data['id'];

if (isset($data->first_name)) $user->first_name = $data->first_name;
if (isset($data->last_name)) $user->last_name = $data->last_name;
if (isset($data->email)) $user->email = $data->email;

if ($user->update()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>