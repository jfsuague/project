<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'src/models/Product.php';

class ProductController
{
    /**
     * Obtener productos por categoría.
     * 
     * @param int $categoryId ID de la categoría.
     * @return array Lista de productos.
     */
    public static function getProductsByCategory($categoryId)
    {
        return Product::getByCategory($categoryId);
    }

    /**
     * Obtener un producto por su ID.
     * 
     * @param int $productId ID del producto.
     * @return array|null Datos del producto o null si no existe.
     */
    public static function getProductById($productId)
    {
        return Product::getById($productId);
    }
}
?>