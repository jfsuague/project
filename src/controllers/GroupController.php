<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'src/models/Group.php';

class GroupController
{
    /**
     * Crear un nuevo grupo basado en datos enviados por POST.
     */
    public static function handleCreateGroup()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception("You must be logged in to create a group.");
            }

            // Validar datos
            $groupName = trim($_POST['group_name'] ?? '');
            $memberCount = $_POST['member_count'] ?? null;
            $price = $_POST['price'] ?? null;
            $productId = $_POST['product_id'] ?? null;

            if (empty($groupName) || empty($memberCount) || empty($price) || empty($productId)) {
                throw new Exception("All fields are required.");
            }

            // Preparar datos
            $groupData = [
                'name' => $groupName,
                'product_id' => (int) $productId,
                'member_count' => (int) $memberCount,
                'price' => (float) $price,
                'admin_id' => (int) $_SESSION['user_id'],
            ];

            if (!Group::create($groupData)) {
                throw new Exception("Failed to create group.");
            }

            $_SESSION['success'] = "Group created successfully!";
            header('Location: /public/create_group.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /public/create_group.php');
            exit();
        }
    }

    /**
     * Salir de uno o más grupos como miembro.
     */
    public static function leaveGroups($groupIds, $userId)
    {
        try {
            if (empty($groupIds) || empty($userId)) {
                throw new Exception("Invalid data to leave groups.");
            }

            foreach ($groupIds as $groupId) {
                Group::leaveGroup($groupId, $userId);
            }

            $_SESSION['success'] = "You have successfully left the selected groups.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error leaving groups: " . $e->getMessage();
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    /**
     * Editar un grupo como administrador.
     */
    public static function editGroup($groupId, $userId, $price)
    {
        try {
            if (empty($groupId) || empty($price) || empty($userId)) {
                throw new Exception("Invalid data to edit group.");
            }

            Group::updateGroupPrice($groupId, $price, $userId);
        } catch (Exception $e) {
            throw new Exception("Error editing group: " . $e->getMessage());
        }
    }

    public static function getGroupById($groupId)
    {
        try {
            return Group::getGroupById($groupId);
        } catch (Exception $e) {
            throw new Exception("Error fetching group: " . $e->getMessage());
        }
    }


    /**
     * Eliminar un grupo como administrador.
     */
    public static function deleteGroup($groupId, $userId)
    {
        try {
            // Verificar si el usuario es el administrador del grupo
            $group = Group::getGroupById($groupId);

            if (!$group) {
                throw new Exception("Group not found.");
            }

            if ($group['USER_ID'] != $userId) {
                throw new Exception("You are not authorized to delete this group.");
            }

            // Eliminar el grupo
            Group::delete($groupId);
        } catch (Exception $e) {
            throw new Exception("Error deleting group: " . $e->getMessage());
        }
    }


    /**
     * Obtener grupos en los que el usuario es miembro.
     */
    public static function getUserGroups($userId)
    {
        try {
            return Group::getUserGroups($userId);
        } catch (Exception $e) {
            throw new Exception("Error fetching user groups: " . $e->getMessage());
        }
    }

    /**
     * Obtener grupos que administra el usuario.
     */
    public static function getAdminGroups($userId)
    {
        try {
            return Group::getAdminGroups($userId);
        } catch (Exception $e) {
            throw new Exception("Error fetching admin groups: " . $e->getMessage());
        }
    }
}

// Manejo de solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    try {
        $action = $_POST['action'] ?? null;

        switch ($action) {
            case 'create_group':
                GroupController::handleCreateGroup();
                break;

            case 'update_group':
                $groupId = $_POST['group_id'] ?? null;
                $newPrice = $_POST['price'] ?? null;
                $userId = $_SESSION['user_id'] ?? null;

                if (!$groupId || !$newPrice || !$userId) {
                    throw new Exception("Missing required data for updating the group.");
                }

                GroupController::editGroup($groupId, $userId, $newPrice);
                $_SESSION['success'] = "Group updated successfully!";
                header('Location: /public/my_groups.php');
                exit();

            case 'leave_groups':
                $groupIds = $_POST['group_ids'] ?? [];
                $userId = $_SESSION['user_id'] ?? null;

                if (empty($groupIds) || !$userId) {
                    throw new Exception("Missing required data for leaving groups.");
                }

                GroupController::leaveGroups($groupIds, $userId);
                $_SESSION['success'] = "You have successfully left the selected groups.";
                header('Location: /public/my_groups.php');
                exit();

            case 'delete_group':
                $groupId = $_POST['group_id'] ?? null;
                $userId = $_SESSION['user_id'] ?? null;

                if (!$groupId || !$userId) {
                    throw new Exception("Missing required data for deleting the group.");
                }

                try {
                    GroupController::deleteGroup($groupId, $userId);
                    $_SESSION['success'] = "Group deleted successfully!";
                    header('Location: /public/my_groups.php');
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error deleting group: " . $e->getMessage();
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                }
                exit();


            default:
                throw new Exception("Invalid action.");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/public/my_groups.php'));
        exit();
    }
}
