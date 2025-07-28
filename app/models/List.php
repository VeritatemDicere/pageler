<?php
class ListModel {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getAllByUser($user_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM lists WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    public function create($user_id, $name, $color, $emoji) {
        $stmt = $this->pdo->prepare('INSERT INTO lists (user_id, name, color, emoji) VALUES (?, ?, ?, ?)');
        return $stmt->execute([$user_id, $name, $color, $emoji]);
    }
    public function find($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM lists WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function delete($id, $user_id) {
        $stmt = $this->pdo->prepare('DELETE FROM lists WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $user_id]);
    }
} 