<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'src/controllers/UserController.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to access settings.";
    header('Location: /public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userData = [];


try {
    $userData = UserController::getUserById($userId);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: /public/dashboard.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <!-- Navbar -->
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
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_groups.php">My Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Account Settings</h1>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="/src/controllers/UserController.php" method="POST" class="mt-4">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userData['ID']); ?>">

            <!-- Nombre -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control bg-dark text-light border-light" id="name" name="name"
                    value="<?php echo htmlspecialchars($userData['NAME']); ?>" required>
            </div>

            <!-- Apellido -->
            <div class="mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control bg-dark text-light border-light" id="surname" name="surname"
                    value="<?php echo htmlspecialchars($userData['SURNAME']); ?>" required>
            </div>

            <!-- Teléfono -->
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control bg-dark text-light border-light" id="phone" name="phone"
                    value="<?php echo htmlspecialchars($userData['PHONE']); ?>" required>
            </div>

            <!-- Correo electrónico (solo lectura) -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control bg-dark text-light border-light" id="email"
                    value="<?php echo htmlspecialchars($userData['EMAIL']); ?>" readonly>
            </div>

            <!-- Contraseña (opcional para cambiarla) -->
            <div class="mb-3">
                <label for="password" class="form-label">New Password (optional)</label>
                <input type="password" class="form-control bg-dark text-light border-light" id="password"
                    name="password" placeholder="Leave blank to keep current password">
            </div>

            <!-- Botones -->
            <div class="text-end">
                <button type="submit" name="delete_user" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                    Delete Account
                </button>
            </div>

            <div class="text-end">
                <button type="submit" name="update_user" class="btn btn-warning mt-3">Save Changes</button>
                </button>
            </div>
        </form>

        <div class="text-end">
            <a href="/public/dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>