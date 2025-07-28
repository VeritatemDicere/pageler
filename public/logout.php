<?php
session_start();
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';
$auth = new AuthController($pdo);
$auth->logout();
header('Location: login.php');
exit; 