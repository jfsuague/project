<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'config/database.php';


class Category
{
    /**
     * Obtener todas las categorías.
     * 
     * @return array Lista de categorías.
     * @throws Exception Si hay un error en la consulta.
     */
    public static function getAll()
    {
        global $conn;

        $query = "SELECT * FROM CATEGORIES";
        $result = $conn->query($query);

        if (!$result) {
            throw new Exception("Error fetching categories: " . $conn->error);
        }

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }
}
?>