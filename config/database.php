<?php
require_once 'config.php'; // Importar configuraciones globales

// Crear conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Configurar codificación de caracteres
$conn->set_charset("utf8");

// Función para verificar y crear tablas si no existen
function checkAndCreateTables($conn)
{
    $tables = [
        "USERS" => "
            CREATE TABLE IF NOT EXISTS USERS (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                NAME VARCHAR(25),
                SURNAME VARCHAR(25),
                EMAIL VARCHAR(50) UNIQUE,
                PASSWORD VARCHAR(255),
                PHONE VARCHAR(15)
            )
        ",
        "CATEGORIES" => "
            CREATE TABLE IF NOT EXISTS CATEGORIES (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                NAME VARCHAR(25)
            )
        ",
        "PRODUCTS" => "
            CREATE TABLE IF NOT EXISTS PRODUCTS (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                NAME VARCHAR(50),
                CATEGORY_ID INT(11),
                FOREIGN KEY (CATEGORY_ID) REFERENCES CATEGORIES(ID) ON DELETE CASCADE
            )
        ",
        "GROUPS" => "
            CREATE TABLE IF NOT EXISTS GROUPS (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                NAME VARCHAR(100),
                PRICE DECIMAL(10, 2),
                MEMBER_COUNT INT(11),
                USER_ID INT(11),
                PRODUCT_ID INT(11),
                FOREIGN KEY (USER_ID) REFERENCES USERS(ID) ON DELETE CASCADE,
                FOREIGN KEY (PRODUCT_ID) REFERENCES PRODUCTS(ID) ON DELETE CASCADE
            )
        ",
        "MEMBERSHIP" => "
            CREATE TABLE IF NOT EXISTS MEMBERSHIP (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                USER_ID INT(11),
                GROUP_ID INT(11),
                FOREIGN KEY (USER_ID) REFERENCES USERS(ID) ON DELETE CASCADE,
                FOREIGN KEY (GROUP_ID) REFERENCES GROUPS(ID) ON DELETE CASCADE
            )
        "
    ];

    foreach ($tables as $name => $query) {
        if (!$conn->query($query)) {
            die("Error creando la tabla $name: " . $conn->error);
        }
    }
}

// Función para insertar datos iniciales si no existen
function insertInitialData($conn)
{
    // Verificar e insertar datos en la tabla USERS
    $result = $conn->query("SELECT COUNT(*) AS total FROM USERS");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $conn->query("INSERT INTO USERS (NAME, SURNAME, EMAIL, PASSWORD, PHONE) 
                      VALUES 
                      ('Jose Fran', 'Suárez', 'jose@gmail.com', '" . password_hash('pepe2000', PASSWORD_BCRYPT) . "', '648270678'),
                      ('Sergio', 'Gamero', 'sergioga@gmail.com', '" . password_hash('sergio2000', PASSWORD_BCRYPT) . "', '698742312'),
                      ('Migue', 'Guerrero', 'migue@gmail.com', '" . password_hash('migue2000', PASSWORD_BCRYPT) . "', '678752313')");
    }

    // Verificar e insertar categorías iniciales
    $result = $conn->query("SELECT COUNT(*) AS total FROM CATEGORIES");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $conn->query("INSERT INTO CATEGORIES (NAME) VALUES 
                      ('Streaming'), 
                      ('Music'), 
                      ('Gaming'), 
                      ('Software'), 
                      ('News')");
    }

    // Verificar e insertar productos iniciales
    $result = $conn->query("SELECT COUNT(*) AS total FROM PRODUCTS");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $conn->query("INSERT INTO PRODUCTS (NAME, CATEGORY_ID) VALUES 
                      ('HBO MAX', 1), 
                      ('NETFLIX', 1), 
                      ('Disney+', 1), 
                      ('SPOTIFY', 2), 
                      ('APPLE MUSIC', 2), 
                      ('NINTENDO SWITCH', 3), 
                      ('XBOX GAME PASS', 3), 
                      ('Microsoft 365', 4), 
                      ('El Mundo', 5)");
    }
}

// Ejecutar funciones de verificación e inserción
checkAndCreateTables($conn);
insertInitialData($conn);
?>