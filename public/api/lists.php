<?php
session_start();
require_once __DIR__ . '/../../app/controllers/ListController.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

$controller = new ListController($pdo);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lists = $controller->getLists($user_id);
    header('Content-Type: application/json');
    echo json_encode($lists);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $color = $data['color'] ?? '#007aff';
    $emoji = $data['emoji'] ?? '📋';
    if ($name) {
        $controller->addList($user_id, $name, $color, $emoji);
        http_response_code(201);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Liste adı gerekli']);
    }
    exit;
}
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']); 