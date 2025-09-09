<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

try {
  $db = (new Database())->getConnection();
  $stmt = $db->query('SELECT 1');
  echo json_encode([
    'ok' => true,
    'db' => $stmt ? 'connected' : 'query-failed',
    'time' => date('c')
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
