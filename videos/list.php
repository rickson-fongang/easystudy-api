<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Video.php';
require_once '../classes/Auth.php';

// Require authentication
$auth_user = Auth::requireAuth();

// Initialize database and video object
$database = new Database();
$db = $database->getConnection();
$video = new Video($db);

// Get videos based on user type
if ($auth_user['user_type'] === 'tutor') {
    $stmt = $video->readByTutor($auth_user['user_id']);
} else {
    $stmt = $video->read();
}

$num = $stmt->rowCount();

if ($num > 0) {
    $videos_arr = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $video_item = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'file_path' => $row['file_path'],
            'file_size' => $row['file_size'],
            'duration' => $row['duration'],
            'time_limit' => $row['time_limit'],
            'is_active' => $row['is_active'],
            'created_at' => $row['created_at']
        ];

        // Add tutor info for students
        if ($auth_user['user_type'] === 'student' && isset($row['first_name'])) {
            $video_item['tutor_name'] = $row['first_name'] . ' ' . $row['last_name'];
        }

        array_push($videos_arr, $video_item);
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $videos_arr
    ]);
} else {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => []
    ]);
}
?>
