<?php
// Definir la ruta base del proyecto
if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../') . '/');
}

// Configuraciones globales
define('SITE_NAME', 'SPOT');
define('DEBUG_MODE', true);

// Configuración de la base de datos
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_37917496');
define('DB_PASSWORD', 'wqnqkjAIit');
define('DB_NAME', 'if0_37917496_project');

// Mensajes opcionales para depuración
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}
?>