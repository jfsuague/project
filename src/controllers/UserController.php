<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'src/models/User.php';

class UserController
{
    /**
     * Obtener datos del usuario por ID.
     */
    public static function getUserById($userId)
    {
        return User::getById($userId);
    }

    /**
     * Actualizar información del usuario.
     */
    public static function updateUser($userId, $data)
    {
        $password = $data['password'] ?? null;
        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        return User::update($userId, $data);
    }

    /**
     * Eliminar usuario.
     */
    public static function deleteUser($userId)
    {
        return User::delete($userId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    try {
        if (isset($_POST['update_user'])) {
            $userId = $_POST['user_id'];
            $data = [
                'name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'phone' => $_POST['phone'],
                'password' => $_POST['password'] ?? null,
            ];
            UserController::updateUser($userId, $data);
            $_SESSION['success'] = "Your account details have been updated.";
        } elseif (isset($_POST['delete_user'])) {
            $userId = $_POST['user_id'];
            UserController::deleteUser($userId);
            session_destroy();
            header('Location: /public/login.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header('Location: /public/settings.php');
    exit();
}
