<?php
require_once __DIR__ . '/../models/Section.php';
require_once __DIR__ . '/../../config/database.php';
class SectionController {
    private $model;
    public function __construct($pdo) {
        $this->model = new Section($pdo);
    }
    public function getSections($list_id) {
        return $this->model->getAllByList($list_id);
    }
    public function addSection($list_id, $name, $position = 0) {
        return $this->model->create($list_id, $name, $position);
    }
    public function deleteSection($id, $list_id) {
        return $this->model->delete($id, $list_id);
    }
} 