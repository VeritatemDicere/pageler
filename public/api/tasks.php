<?php
session_start();
require_once __DIR__ . '/../../app/controllers/TaskController.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

$controller = new TaskController($pdo);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $list_id = $_GET['list_id'] ?? null;
    $section_id = $_GET['section_id'] ?? null;
    $due_date = $_GET['due_date'] ?? null;
    if ($section_id) {
        $tasks = $controller->getTasksBySection($section_id);
    } elseif ($list_id) {
        $tasks = $controller->getTasksByList($list_id);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'list_id veya section_id gerekli']);
        exit;
    }
    // Tarih filtresi uygula
    if ($due_date) {
        $tasks = array_filter($tasks, function($task) use ($due_date) {
            return $task['due_date'] === $due_date;
        });
        $tasks = array_values($tasks);
    }
    header('Content-Type: application/json');
    echo json_encode($tasks);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $list_id = $data['list_id'] ?? null;
    $section_id = $data['section_id'] ?? null;
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $due_date = $data['due_date'] ?? null;
    $label = $data['label'] ?? '';
    $status = $data['status'] ?? 'pending';
    if ($list_id && $title) {
        $controller->addTask($list_id, $section_id, $user_id, $title, $description, $due_date, $label, $status);
        http_response_code(201);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'list_id ve title gerekli']);
    }
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;
    $move_list_id = $data['move_list_id'] ?? null;
    $move_section_id = $data['move_section_id'] ?? null;
    if ($id && $status) {
        $controller->updateStatus($id, $status);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($id && $move_list_id) {
        $controller->moveToList($id, $move_list_id);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($id && $move_section_id) {
        $controller->moveToSection($id, $move_section_id);
        echo json_encode(['success' => true]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['error' => 'id ve işlem parametresi gerekli']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    if ($id) {
        $controller->deleteTask($id, $user_id);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'id gerekli']);
    }
    exit;
}
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']); 