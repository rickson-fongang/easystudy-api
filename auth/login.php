<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Auth.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    if ($user->loadByEmail($data->email)) {

        if ($user->user_type !== 'tutor') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Only tutors can log in here.']);
            exit;
        }

        if (password_verify($data->password, $user->password)) {
            if ($user->is_active) {
                $user_data = [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name
                ];

                $token = Auth::generateToken($user_data);

                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Tutor login successful',
                    'token' => $token,
                    'user' => $user_data
                ]);
                exit;
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Account deactivated.']);
                exit;
            }
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
            exit;
        }

    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Tutor not found.']);
        exit;
    }

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password required.']);
    exit;
}
?>
