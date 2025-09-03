<?php
// Application configuration
define('JWT_SECRET', 'your-secret-key-here-change-in-production');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600 * 24 * 7); // 7 days

// File upload configuration
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'wmv', 'flv']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png']);

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');
?>
