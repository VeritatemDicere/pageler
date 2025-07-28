<?php
class Task {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    public function getAllByList($list_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE list_id = ? ORDER BY due_date ASC');
        $stmt->execute([$list_id]);
        return $stmt->fetchAll();
    }
    public function getAllBySection($section_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE section_id = ? ORDER BY due_date ASC');
        $stmt->execute([$section_id]);
        return $stmt->fetchAll();
    }
    public function create($list_id, $section_id, $user_id, $title, $description, $due_date, $label, $status = 'pending') {
        $stmt = $this->pdo->prepare('INSERT INTO tasks (list_id, section_id, user_id, title, description, due_date, label, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$list_id, $section_id, $user_id, $title, $description, $due_date, $label, $status]);
    }
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }
    public function moveToList($id, $list_id) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET list_id = ?, section_id = NULL WHERE id = ?');
        return $stmt->execute([$list_id, $id]);
    }
    public function moveToSection($id, $section_id) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET section_id = ? WHERE id = ?');
        return $stmt->execute([$section_id, $id]);
    }
    public function delete($id, $user_id) {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $user_id]);
    }
} 