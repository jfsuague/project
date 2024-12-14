<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . 'src/models/Group.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: /public/join_group.php');
    exit();
}

if (!isset($_POST['group_id'], $_POST['product_id'], $_SESSION['user_id'])) {
    $_SESSION['error'] = "Invalid data.";
    header('Location: /public/join_group.php');
    exit();
}

$groupId = (int) $_POST['group_id'];
$productId = (int) $_POST['product_id'];
$userId = (int) $_SESSION['user_id'];

try {
    // Verificar si el grupo pertenece al producto
    $group = Group::getGroupById($groupId);
    if (!$group) {
        throw new Exception("Group not found.");
    }
    if ((int) $group['PRODUCT_ID'] !== $productId) {
        throw new Exception("The group does not belong to the selected product.");
    }

    // Verificar el número actual de miembros
    $currentMembers = Group::getMemberCount($groupId);
    if ($currentMembers >= (int) $group['MEMBER_COUNT']) {
        throw new Exception("The group is already full.");
    }

    // Unir al usuario al grupo
    Group::joinGroup($groupId, $userId);
    $_SESSION['success'] = "Successfully joined the group!";
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Redirigir de nuevo a la página de unir grupos
header('Location: /public/join_group.php?product_id=' . $productId);
exit();
