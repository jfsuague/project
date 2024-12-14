<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'config/database.php';

class Product
{
    /**
     * Obtener productos por categoría.
     * 
     * @param int $categoryId ID de la categoría.
     * @return array Lista de productos.
     * @throws Exception Si hay un error en la consulta.
     */
    public static function getByCategory($categoryId)
    {
        global $conn;

        $query = "SELECT * FROM PRODUCTS WHERE CATEGORY_ID = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        return $products;
    }

    /**
     * Obtener un producto por su ID.
     * 
     * @param int $productId ID del producto.
     * @return array|null Datos del producto o null si no existe.
     * @throws Exception Si hay un error en la consulta.
     */
    public static function getById($productId)
    {
        global $conn;

        $query = "SELECT * FROM PRODUCTS WHERE ID = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        $product = $result->fetch_assoc();

        $stmt->close();
        return $product;
    }
}
?>