<?php
session_start();
require_once __DIR__ . '/../../app/models/Notification.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

$user_id = $_SESSION['user_id'];
$notification = new Notification($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $list_id = $_GET['list_id'] ?? null;
    if ($list_id) {
        $enabled = $notification->isEnabled($list_id, $user_id);
        echo json_encode(['enabled' => $enabled]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['error' => 'list_id gerekli']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $list_id = $data['list_id'] ?? null;
    $enabled = $data['enabled'] ?? false;
    if ($list_id !== null) {
        $notification->set($list_id, $user_id, $enabled ? 1 : 0);
        echo json_encode(['success' => true]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['error' => 'list_id gerekli']);
    exit;
}
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']); 