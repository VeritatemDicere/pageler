<?php
session_start();
require_once __DIR__ . '/../../app/controllers/SectionController.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

$controller = new SectionController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $list_id = $_GET['list_id'] ?? null;
    if ($list_id) {
        $sections = $controller->getSections($list_id);
        header('Content-Type: application/json');
        echo json_encode($sections);
        exit;
    }
    http_response_code(400);
    echo json_encode(['error' => 'list_id gerekli']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $list_id = $data['list_id'] ?? null;
    $name = $data['name'] ?? '';
    if ($list_id && $name) {
        $controller->addSection($list_id, $name);
        http_response_code(201);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'list_id ve name gerekli']);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $list_id = $data['list_id'] ?? null;
    if ($id && $list_id) {
        $controller->deleteSection($id, $list_id);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'id ve list_id gerekli']);
    }
    exit;
}
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']); 