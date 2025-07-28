<?php
session_start();
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';

$auth = new AuthController($pdo);
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($auth->login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Kullanıcı adı veya şifre hatalı!';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - Görev Takip</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h2>Görev Takip Giriş</h2>
        <?php if ($error): ?><div class="error"><?=$error?></div><?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required autofocus><br>
            <input type="password" name="password" placeholder="Şifre" required><br>
            <button type="submit">Giriş Yap</button>
        </form>
    </div>
</body>
</html> 