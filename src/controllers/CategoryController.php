<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'config/database.php';
require_once BASE_PATH . 'src/models/Category.php';

class CategoryController
{
    public static function getAllCategories()
    {
        return Category::getAll();
    }
}
?>