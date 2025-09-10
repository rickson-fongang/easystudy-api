<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../classes/User.php";
require_once __DIR__ . "/../classes/Auth.php";

$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!empty($data->first_name) && !empty($data->last_name) && !empty($data->email) && !empty($data->password)) {
    
    // Admin code validation for students
    if (!empty($data->user_type) && $data->user_type === 'student') {
        if (empty($data->admin_code)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Admin code is required for student registration.'
            ]);
            exit;
        }
        
        $valid_admin_codes = ['EASY2025', 'STUDENT123', 'RICKSON2024'];
        
        if (!in_array($data->admin_code, $valid_admin_codes)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid admin code provided.'
            ]);
            exit;
        }
    }

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $user->email = $data->email;

    // Check if email already exists
    if ($user->emailExists()) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Email already registered.'
        ]);
        exit;
    }

    // Assign user properties
    $user->first_name = $data->first_name;
    $user->last_name = $data->last_name;
    $user->email = $data->email;
    $user->phone = !empty($data->phone) ? $data->phone : ''; // Handle missing phone
    $user->password = $data->password; // will be hashed in create()
    $user->user_type = !empty($data->user_type) && in_array($data->user_type, ['student', 'tutor'])
        ? $data->user_type
        : 'student';
    $user->is_active = true;

    // Create user
    if ($user->create()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => ucfirst($user->user_type) . ' registered successfully.',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'user_type' => $user->user_type
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to register user. Check database connection.'
        ]);
    }

} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required (first_name, last_name, email, password).'
    ]);
}
?>