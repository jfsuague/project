<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'src/controllers/GroupController.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view your groups.";
    header('Location: /public/login.php');
    exit();
}

$userName = $_SESSION['user_name'] ?? 'Guest';
$userId = $_SESSION['user_id'];

try {
    $userGroups = GroupController::getUserGroups($userId); // Grupos en los que el usuario es miembro
    $adminGroups = GroupController::getAdminGroups($userId); // Grupos que administra el usuario
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
    <title>My Groups</title>
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
        <h1 class="text-center mb-4">Groups of <?php echo htmlspecialchars($userName); ?></h1>

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

        <!-- Grupos que administra -->
        <h2>Groups you administer</h2>
        <?php if (!empty($adminGroups)): ?>
            <form action="edit_group.php" method="GET">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Group Name</th>
                            <th>Product</th>
                            <th>Max Members</th>
                            <th>Price (€)</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adminGroups as $group): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($group['NAME']); ?></td>
                                <td><?php echo htmlspecialchars($group['PRODUCT']); ?></td>
                                <td><?php echo htmlspecialchars($group['MEMBER_COUNT']); ?></td>
                                <td><?php echo htmlspecialchars($group['PRICE']); ?></td>
                                <td>
                                    <input type="radio" name="group_id" value="<?php echo htmlspecialchars($group['ID']); ?>"
                                        required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex flex-column gap-3">
                    <div class="text-end">
                        <button type="submit" class="btn btn-info">Edit Selected Group</button>
                    </div>
                    <div class="text-end">
                        <a href="/public/dashboard.php" class="btn btn-warning">Create New Group</a>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <div class="alert alert-warning">You are not an administrator of any group.</div>
            <div class="text-end">
                <a href="/public/dashboard.php" class="btn btn-warning">Create a Group</a>
            </div>
        <?php endif; ?>

        <!-- Grupos en los que es miembro -->
        <h2 class="mt-5">Groups you are a member of</h2>
        <?php if (!empty($userGroups)): ?>
            <form action="/src/controllers/GroupController.php" method="POST">
                <input type="hidden" name="action" value="leave_groups">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Group Name</th>
                            <th>Product</th>
                            <th>Price (€)</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userGroups as $group): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($group['NAME']); ?></td>
                                <td><?php echo htmlspecialchars($group['PRODUCT']); ?></td>
                                <td><?php echo htmlspecialchars($group['PRICE']); ?></td>
                                <td>
                                    <input type="checkbox" name="group_ids[]"
                                        value="<?php echo htmlspecialchars($group['ID']); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex flex-column gap-3">
                    <div class="text-end">
                        <button type="submit" name="leave_groups" class="btn btn-danger">Leave Selected Groups</button>
                    </div>
                    <div class="text-end">
                        <a href="/public/dashboard.php" class="btn btn-warning">Join New Group</a>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">You are not a member of any group.</div>
            <div class="text-end">
                <a href="/public/dashboard.php" class="btn btn-warning">Join a Group</a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="/public/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

    </div>

</body>

</html>