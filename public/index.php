<?php
session_start();
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';
$auth = new AuthController($pdo);
if (!$auth->check()) {
    header('Location: login.php');
    exit;
}
$user = $auth->user();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>VeriUp Ofis Takip Uygulaması</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/chart.min.js"></script>
    <script src="assets/js/app.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">📝 Görev Takip</div>
        <div class="user-info">
            Merhaba, <b><?=htmlspecialchars($user['username'])?></b> (<?=htmlspecialchars($user['role'])?>)
            | <a href="logout.php">Çıkış</a>
        </div>
    </header>
    <div class="container">
        <aside class="sidebar">
            <!-- Listeler burada yüklenecek -->
            <button id="add-list-btn">+ Yeni Liste</button>
            <ul id="lists"></ul>
        </aside>
        <main class="main-content">
            <div id="calendar-view"></div>
            <div id="sections"></div>
            <div id="tasks"></div>
        </main>
        <aside class="stats">
            <canvas id="statsChart"></canvas>
        </aside>
    </div>
    <audio id="notify-sound" src="assets/sounds/notify.mp3" preload="auto"></audio>
</body>
</html> 
