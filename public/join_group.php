<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'src/models/Group.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to view available groups.";
    header('Location: /public/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$productId = $_GET['product_id'] ?? null;

if (!$productId || !is_numeric($productId)) {
    $_SESSION['error'] = "Invalid product ID.";
    header('Location: /public/dashboard.php');
    exit();
}

try {
    $availableGroups = Group::getAvailableGroups($productId, $userId);
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    $availableGroups = [];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Group</title>
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

    <!-- Content -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Available Groups</h2>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Groups Table -->
        <?php if (!empty($availableGroups)): ?>
            <form action="/src/controllers/JoinController.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productId); ?>">
                <div class="table-responsive">
                    <table class="table table-dark table-hover table-bordered align-middle">
                        <thead class="table-primary text-dark">
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Members</th>
                                <th class="text-center">Price (€)</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($availableGroups as $group): ?>
                                <tr>
                                    <td class="text-center"><?php echo htmlspecialchars($group['NAME']); ?></td>
                                    <td class="text-center">
                                        <?php echo htmlspecialchars($group['current_members']) . '/' . htmlspecialchars($group['MEMBER_COUNT']); ?>
                                    </td>
                                    <td class="text-center"><?php echo htmlspecialchars($group['PRICE']); ?></td>
                                    <td class="text-center">
                                        <?php if (Group::isUserInGroup($group['ID'], $userId)): ?>
                                            <span class="badge bg-success">You are a member</span>
                                        <?php else: ?>
                                            <button type="submit" name="group_id"
                                                value="<?php echo htmlspecialchars($group['ID']); ?>"
                                                class="btn btn-warning btn-sm">
                                                Join
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">No available groups.</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="/public/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>