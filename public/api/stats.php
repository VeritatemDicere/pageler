<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'daily';

if ($type === 'daily') {
    $stmt = $pdo->prepare("SELECT DATE(completed_at) as date, COUNT(*) as count FROM tasks WHERE user_id = ? AND status = 'completed' AND completed_at IS NOT NULL AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(completed_at) ORDER BY date ASC");
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll();
    echo json_encode($data);
    exit;
}
if ($type === 'weekly') {
    $stmt = $pdo->prepare("SELECT YEARWEEK(completed_at, 1) as week, COUNT(*) as count FROM tasks WHERE user_id = ? AND status = 'completed' AND completed_at IS NOT NULL AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK) GROUP BY YEARWEEK(completed_at, 1) ORDER BY week ASC");
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll();
    echo json_encode($data);
    exit;
}
if ($type === 'monthly') {
    $stmt = $pdo->prepare("SELECT DATE_FORMAT(completed_at, '%Y-%m') as month, COUNT(*) as count FROM tasks WHERE user_id = ? AND status = 'completed' AND completed_at IS NOT NULL AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(completed_at, '%Y-%m') ORDER BY month ASC");
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll();
    echo json_encode($data);
    exit;
}
http_response_code(400);
echo json_encode(['error' => 'Geçersiz tip']); 