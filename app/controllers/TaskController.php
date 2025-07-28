<?php
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../../config/database.php';
class TaskController {
    private $model;
    public function __construct($pdo) {
        $this->model = new Task($pdo);
    }
    public function getTasksByList($list_id) {
        return $this->model->getAllByList($list_id);
    }
    public function getTasksBySection($section_id) {
        return $this->model->getAllBySection($section_id);
    }
    public function addTask($list_id, $section_id, $user_id, $title, $description, $due_date, $label, $status = 'pending') {
        return $this->model->create($list_id, $section_id, $user_id, $title, $description, $due_date, $label, $status);
    }
    public function updateStatus($id, $status) {
        return $this->model->updateStatus($id, $status);
    }
    public function moveToList($id, $list_id) {
        return $this->model->moveToList($id, $list_id);
    }
    public function moveToSection($id, $section_id) {
        return $this->model->moveToSection($id, $section_id);
    }
    public function deleteTask($id, $user_id) {
        return $this->model->delete($id, $user_id);
    }
} 