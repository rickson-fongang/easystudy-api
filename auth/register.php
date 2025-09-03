<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Auth.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->first_name) && !empty($data->last_name) && !empty($data->email) && !empty($data->password)) {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    // Check if email already exists
    if ($user->loadByEmail($data->email)) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Email already registered.'
        ]);
        exit;
    }

    // Hash password
    $user->first_name = $data->first_name;
    $user->last_name = $data->last_name;
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_BCRYPT);
    $user->user_type = 'tutor';
    $user->is_active = true;

    if ($user->create()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Tutor registered successfully.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to register tutor.'
        ]);
    }

} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required.'
    ]);
}
?>
