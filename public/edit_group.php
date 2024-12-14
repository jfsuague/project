<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'src/controllers/GroupController.php';

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to edit a group.";
    header('Location: /public/login.php');
    exit();
}

// Obtener el ID del grupo desde el parámetro GET
$groupId = $_GET['group_id'] ?? null;

if (!$groupId || !is_numeric($groupId)) {
    $_SESSION['error'] = "Invalid group ID.";
    header('Location: /public/my_groups.php');
    exit();
}

try {
    // Obtener datos del grupo desde el controlador
    $group = GroupController::getGroupById($groupId);

    if (!$group) {
        throw new Exception("Group not found.");
    }

    // Verificar que el usuario sea administrador del grupo
    if ($group['USER_ID'] != $_SESSION['user_id']) {
        throw new Exception("You are not authorized to edit this group.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: /public/my_groups.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-light">
        <div class="container-fluid">
            <img src="./assets/images/logo.png" alt="Logo" style="width: 64px; height: auto;">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="my_groups.php">My Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Group: <?php echo htmlspecialchars($group['NAME']); ?></h1>

        <!-- Mensajes de error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($SESSION['error']);
            unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para editar el grupo -->
        <form action="/src/controllers/GroupController.php" method="POST">
            <input type="hidden" name="action" value="update_group">
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group['ID']); ?>">

            <!-- Nombre editable -->
            <div class="mb-3">
                <label for="group_name" class="form-label">Group Name</label>
                <input type="text" name="group_name" id="group_name" class="form-control"
                    value="<?php echo htmlspecialchars($group['NAME']); ?>" required>
            </div>

            <!-- Producto no editable -->
            <div class="mb-3">
                <label for="product" class="form-label">Product</label>
                <input type="text" name="product" id="product" class="form-control"
                    value="<?php echo htmlspecialchars($group['PRODUCT_NAME'] ?? 'Unknown'); ?>" readonly>
            </div>

            <!-- Precio editable -->
            <div class="mb-3">
                <label for="price" class="form-label">Price (€)</label>
                <input type="number" name="price" id="price" class="form-control"
                    value="<?php echo htmlspecialchars($group['PRICE']); ?>" min="0" step="0.01" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="/public/my_groups.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <!-- Botón para eliminar el grupo -->
        <form action="/src/controllers/GroupController.php" method="POST" class="mt-3 d-flex justify-content-end">
            <input type="hidden" name="action" value="delete_group">
            <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group['ID']); ?>">
            <button type="submit" class="btn btn-danger">Delete Group</button>
        </form>
    </div>
</body>

</html>