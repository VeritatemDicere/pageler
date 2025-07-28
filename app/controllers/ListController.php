<?php
require_once __DIR__ . '/../models/List.php';
require_once __DIR__ . '/../../config/database.php';
class ListController {
    private $model;
    public function __construct($pdo) {
        $this->model = new ListModel($pdo);
    }
    public function getLists($user_id) {
        return $this->model->getAllByUser($user_id);
    }
    public function addList($user_id, $name, $color, $emoji) {
        return $this->model->create($user_id, $name, $color, $emoji);
    }
    public function deleteList($id, $user_id) {
        return $this->model->delete($id, $user_id);
    }
} 