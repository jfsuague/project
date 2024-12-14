<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'config/database.php';

class User
{
    // Obtener usuario por ID
    public static function getById($userId)
    {
        global $conn;

        $query = "SELECT * FROM USERS WHERE ID = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            throw new Exception("User not found.");
        }

        return $user;
    }


    // Actualizar usuario
    public static function update($userId, $data)
    {
        global $conn;

        $query = "UPDATE USERS SET NAME = ?, SURNAME = ?, PHONE = ?" .
            (!empty($data['password']) ? ", PASSWORD = ?" : "") .
            " WHERE ID = ?";
        $stmt = $conn->prepare($query);

        if (!empty($data['password'])) {
            $stmt->bind_param('ssssi', $data['name'], $data['surname'], $data['phone'], $data['password'], $userId);
        } else {
            $stmt->bind_param('sssi', $data['name'], $data['surname'], $data['phone'], $userId);
        }

        $stmt->execute();
        $result = $stmt->affected_rows > 0;
        $stmt->close();

        return $result;
    }

    // Eliminar usuario
    public static function delete($userId)
    {
        global $conn;

        $query = "DELETE FROM USERS WHERE ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->affected_rows > 0;
        $stmt->close();

        return $result;
    }
}

?>