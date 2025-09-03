<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Video.php';
require_once '../classes/Auth.php';

// Require tutor authentication
$auth_user = Auth::requireTutor();

// Check if file was uploaded
if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No video file uploaded or upload error.'
    ]);
    exit();
}

$file = $_FILES['video'];
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$time_limit = $_POST['time_limit'] ?? 24;

// Validate required fields
if (empty($title)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Video title is required.'
    ]);
    exit();
}

// Validate file size
if ($file['size'] > MAX_FILE_SIZE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'File size exceeds maximum allowed size.'
    ]);
    exit();
}

// Validate file type
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($file_extension, ALLOWED_VIDEO_TYPES)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type. Allowed types: ' . implode(', ', ALLOWED_VIDEO_TYPES)
    ]);
    exit();
}

// Create upload directory if it doesn't exist
$upload_dir = UPLOAD_DIR . 'videos/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$filename = uniqid() . '_' . time() . '.' . $file_extension;
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    
    // Initialize database and video object
    $database = new Database();
    $db = $database->getConnection();
    $video = new Video($db);

    // Set video properties
    $video->title = $title;
    $video->description = $description;
    $video->file_path = 'uploads/videos/' . $filename;
    $video->file_size = $file['size'];
    $video->duration = 0; // Would need video processing library to get actual duration
    $video->time_limit = $time_limit;
    $video->tutor_id = $auth_user['user_id'];

    // Create video record
    if ($video->create()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Video uploaded successfully.',
            'video_id' => $video->id
        ]);
    } else {
        // Delete uploaded file if database insert fails
        unlink($file_path);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Unable to save video information.'
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to upload file.'
    ]);
}
?>
