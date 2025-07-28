<?php
class Notification {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function isEnabled($list_id, $user_id) {
        $stmt = $this->pdo->prepare('SELECT enabled FROM notifications WHERE list_id = ? AND user_id = ?');
        $stmt->execute([$list_id, $user_id]);
        $row = $stmt->fetch();
        return $row ? (bool)$row['enabled'] : false;
    }
    public function set($list_id, $user_id, $enabled) {
        $stmt = $this->pdo->prepare('INSERT INTO notifications (list_id, user_id, enabled) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE enabled = VALUES(enabled)');
        return $stmt->execute([$list_id, $user_id, $enabled]);
    }
} 