<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'config/database.php';

class Group
{
    // Crear un nuevo grupo
    public static function create($data)
    {
        global $conn;

        $query = "INSERT INTO GROUPS (NAME, PRODUCT_ID, MEMBER_COUNT, PRICE, USER_ID) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing insert query: " . $conn->error);
        }

        $stmt->bind_param(
            'siidi',
            $data['name'],
            $data['product_id'],
            $data['member_count'],
            $data['price'],
            $data['admin_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $stmt->close();
        return true;

    }

    public static function getAvailableGroups($productId, $userId)
    {
        global $conn;

        $query = "
            SELECT g.*, 
                (SELECT COUNT(*) FROM MEMBERSHIP m WHERE m.GROUP_ID = g.ID) AS current_members
            FROM GROUPS g
            WHERE g.PRODUCT_ID = ? 
            AND g.USER_ID != ? 
            AND (SELECT COUNT(*) FROM MEMBERSHIP m WHERE m.GROUP_ID = g.ID) < g.MEMBER_COUNT
        ";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('ii', $productId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $groups = [];
        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }

        $stmt->close();
        return $groups;
    }


    public static function getMemberCount($groupId)
    {
        global $conn;

        $query = "SELECT COUNT(*) AS current_members FROM MEMBERSHIP WHERE GROUP_ID = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return $row['current_members'];
    }

    public static function getGroupById($groupId)
    {
        global $conn;

        $query = "
            SELECT g.ID, g.NAME, g.MEMBER_COUNT, g.PRICE, g.PRODUCT_ID, g.USER_ID, p.NAME AS PRODUCT_NAME
            FROM GROUPS g
            INNER JOIN PRODUCTS p ON g.PRODUCT_ID = p.ID
            WHERE g.ID = ?
        ";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $result = $stmt->get_result();
        $group = $result->fetch_assoc();

        $stmt->close();
        return $group;
    }



    // Verificar si un usuario ya pertenece a un grupo
    public static function isUserInGroup($groupId, $userId)
    {
        global $conn;

        $query = "SELECT COUNT(*) AS count FROM MEMBERSHIP WHERE GROUP_ID = ? AND USER_ID = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('ii', $groupId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();
        return $row['count'] > 0;
    }

    // Unir al usuario a un grupo
    public static function joinGroup($groupId, $userId)
    {
        global $conn;

        // Verificar si el usuario ya pertenece al grupo
        $queryCheck = "SELECT COUNT(*) AS count FROM MEMBERSHIP WHERE GROUP_ID = ? AND USER_ID = ?";
        $stmtCheck = $conn->prepare($queryCheck);

        if (!$stmtCheck) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmtCheck->bind_param('ii', $groupId, $userId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $rowCheck = $resultCheck->fetch_assoc();

        if ($rowCheck['count'] > 0) {
            throw new Exception("You are already a member of this group.");
        }

        // Insertar en la tabla MEMBERSHIP
        $queryInsert = "INSERT INTO MEMBERSHIP (GROUP_ID, USER_ID) VALUES (?, ?)";
        $stmtInsert = $conn->prepare($queryInsert);

        if (!$stmtInsert) {
            throw new Exception("Error preparing insert query: " . $conn->error);
        }

        $stmtInsert->bind_param('ii', $groupId, $userId);
        $stmtInsert->execute();
        $stmtInsert->close();
    }


    public static function getUserGroups($userId)
    {
        global $conn;

        // Consulta para obtener los grupos donde el usuario es miembro
        $query = "
            SELECT g.ID, g.NAME, g.PRICE, g.MEMBER_COUNT, p.NAME AS PRODUCT
            FROM GROUPS g
            INNER JOIN MEMBERSHIP m ON g.ID = m.GROUP_ID
            INNER JOIN PRODUCTS p ON g.PRODUCT_ID = p.ID
            WHERE m.USER_ID = ?
        ";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $groups = [];
        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }

        $stmt->close();
        return $groups;
    }

    public static function getAdminGroups($userId)
    {
        global $conn;

        $query = "
            SELECT g.ID, g.NAME, g.PRICE, g.MEMBER_COUNT, g.MEMBER_COUNT, p.NAME AS PRODUCT
            FROM GROUPS g
            INNER JOIN PRODUCTS p ON g.PRODUCT_ID = p.ID
            WHERE g.USER_ID = ?
        ";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $groups = [];
        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }

        $stmt->close();
        return $groups;
    }



    public static function leaveGroup($groupId, $userId)
    {
        global $conn;

        // Eliminar al usuario del grupo
        $deleteQuery = "DELETE FROM MEMBERSHIP WHERE GROUP_ID = ? AND USER_ID = ?";
        $stmt = $conn->prepare($deleteQuery);

        if (!$stmt) {
            throw new Exception("Error preparing delete query: " . $conn->error);
        }

        $stmt->bind_param('ii', $groupId, $userId);
        $stmt->execute();
        $stmt->close();

        return true;
    }

    public static function delete($groupId)
    {
        global $conn;

        // Eliminar todas las membresías del grupo
        $queryMembership = "DELETE FROM MEMBERSHIP WHERE GROUP_ID = ?";
        $stmtMembership = $conn->prepare($queryMembership);
        if (!$stmtMembership) {
            throw new Exception("Error preparing delete memberships: " . $conn->error);
        }
        $stmtMembership->bind_param('i', $groupId);
        $stmtMembership->execute();
        $stmtMembership->close();

        // Eliminar el grupo
        $queryGroup = "DELETE FROM GROUPS WHERE ID = ?";
        $stmtGroup = $conn->prepare($queryGroup);
        if (!$stmtGroup) {
            throw new Exception("Error preparing delete group: " . $conn->error);
        }
        $stmtGroup->bind_param('i', $groupId);
        $stmtGroup->execute();
        $stmtGroup->close();
    }


    public static function updateGroupPrice($groupId, $newPrice, $userId)
    {
        global $conn;

        $query = "UPDATE GROUPS SET PRICE = ? WHERE ID = ? AND USER_ID = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param('dii', $newPrice, $groupId, $userId);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No se pudo actualizar el grupo.");
        }

        $stmt->close();
    }

}
?>