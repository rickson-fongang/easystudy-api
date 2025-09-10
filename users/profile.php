<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/User.php";
require_once __DIR__ . "/../classes/Auth.php";

// Verify JWT token
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
$user = new User($db);
$user->id = $user_data['id'];

if ($user->getById()) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'user_type' => $user->user_type,
            'is_active' => $user->is_active
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>