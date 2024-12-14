<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'config/database.php';

if (!isset($_SESSION['selected_product'])) {
    $_SESSION['error'] = "No product selected. Please select a product first.";
    header('Location: dashboard.php');
    exit();
}

$productId = $_SESSION['selected_product'];
$productName = "";


try {
    require_once BASE_PATH . 'src/controllers/ProductController.php';
    $product = ProductController::getProductById($productId);
    if ($product) {
        $productName = $product['NAME'];
    } else {
        throw new Exception("Product not found.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching product: " . $e->getMessage();
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
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

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-dark text-light text-center">
                        <h2>Create Group for <?php echo htmlspecialchars($productName); ?></h2>
                    </div>
                    <div class="card-body">
                        <form action="/src/controllers/GroupController.php" method="POST">
                            <input type="hidden" name="action" value="create_group">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productId); ?>">

                            <div class="mb-3">
                                <label for="group_name" class="form-label">Group Name</label>
                                <input type="text" name="group_name" class="form-control" id="group_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="members" class="form-label">Number of Members</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="member_count" id="members2"
                                            value="2" required>
                                        <label class="form-check-label" for="members2">2 Members</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="member_count" id="members3"
                                            value="3" required>
                                        <label class="form-check-label" for="members3">3 Members</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="member_count" id="members4"
                                            value="4" required>
                                        <label class="form-check-label" for="members4">4 Members</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price (€)</label>
                                <input type="number" name="price" class="form-control" id="price" min="0" step="0.01"
                                    required>
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-warning">Create</button>
                                <a href="create_or_join.php" class="btn btn-secondary">Go Back</a>
                            </div>
                            <div class="mt-3">
                                <?php
                                if (isset($_SESSION['success'])) {
                                    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['success']) . '</div>';
                                    unset($_SESSION['success']);
                                }

                                if (isset($_SESSION['error'])) {
                                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error']) . '</div>';
                                    unset($_SESSION['error']);
                                }
                                ?>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>