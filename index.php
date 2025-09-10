<?php
// CORS headers MUST be first, before any output
$allowed_origins = [
    'http://localhost:3000',
    'https://easystudy-platform.vercel.app'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Now include config
require_once __DIR__ . "/config/config.php";

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($request_uri, PHP_URL_PATH), '/');

switch ($path) {
    // Auth routes
    case 'auth/login':
        require_once __DIR__ . '/auth/login.php';
        break;
    case 'auth/register':
        require_once __DIR__ . '/auth/register.php';
        break;
    case 'auth/logout':
        require_once __DIR__ . '/auth/logout.php';
        break;

    // User routes
    case 'users/profile':
        require_once __DIR__ . '/users/profile.php';
        break;
    case 'users/update':
        require_once __DIR__ . '/users/update.php';
        break;
    case 'users/students':
        require_once __DIR__ . '/users/students.php';
        break;

    // Video routes
    case 'videos/upload':
        require_once __DIR__ . '/videos/upload.php';
        break;
    case 'videos/list':
        require_once __DIR__ . '/videos/list.php';
        break;
    case 'videos/delete':
        require_once __DIR__ . '/videos/delete.php';
        break;
    case 'videos/update':
        require_once __DIR__ . '/videos/update.php';
        break;

    // Task routes
    case 'tasks/create':
        require_once __DIR__ . '/tasks/create.php';
        break;
    case 'tasks/list':
        require_once __DIR__ . '/tasks/list.php';
        break;
    case 'tasks/submit':
        require_once __DIR__ . '/tasks/submit.php';
        break;
    case 'tasks/update':
        require_once __DIR__ . '/tasks/update.php';
        break;

    // Chat routes
    case 'chat/send':
        require_once __DIR__ . '/chat/send.php';
        break;
    case 'chat/messages':
        require_once __DIR__ . '/chat/messages.php';
        break;

    // Dashboard routes
    case 'dashboard/data':
        require_once __DIR__ . '/dashboard/data.php';
        break;
    case 'dashboard/progress':
        require_once __DIR__ . '/dashboard/progress.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found: ' . $path]);
        break;
}
?>