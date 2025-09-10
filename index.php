<?php
require_once __DIR__ . "/config/config.php";

// Get the request URI and method
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string
$path = parse_url($request_uri, PHP_URL_PATH);

// Route the requests
switch ($path) {
    case '/auth/login':
        require_once __DIR__ . '/auth/login.php';
        break;
    
    case '/auth/register':
        require_once __DIR__ . '/auth/register.php';
        break;
    
    case '/auth/logout':
        require_once __DIR__ . '/auth/logout.php';
        break;
    
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}
?>