<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/database.php';

class AuthController {
    private $userModel;
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }
    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }
    public function logout() {
        session_destroy();
    }
    public function check() {
        return isset($_SESSION['user_id']);
    }
    public function user() {
        if ($this->check()) {
            return $this->userModel->findById($_SESSION['user_id']);
        }
        return null;
    }
} 