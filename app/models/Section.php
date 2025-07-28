<?php
class Section {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getAllByList($list_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM sections WHERE list_id = ? ORDER BY position ASC');
        $stmt->execute([$list_id]);
        return $stmt->fetchAll();
    }
    public function create($list_id, $name, $position = 0) {
        $stmt = $this->pdo->prepare('INSERT INTO sections (list_id, name, position) VALUES (?, ?, ?)');
        return $stmt->execute([$list_id, $name, $position]);
    }
    public function delete($id, $list_id) {
        $stmt = $this->pdo->prepare('DELETE FROM sections WHERE id = ? AND list_id = ?');
        return $stmt->execute([$id, $list_id]);
    }
} 