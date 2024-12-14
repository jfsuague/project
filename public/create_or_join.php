<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'config/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header('Location: ' . BASE_PATH . 'public/login.php');
    exit();
}

// Verificar si un producto ha sido seleccionado
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
    <title>Create or Join Group</title>
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
                <div class="card shadow ">
                    <div class="card-header bg-dark text-light text-center">
                        <h2>Create or Join a Group for <?php echo htmlspecialchars($productName); ?></h2>
                    </div>
                    <div class="card-body">
                        <h5>What would you like to do?</h5>
                        <div class="d-flex flex-column gap-3 mt-4">
                            <a href="create_group.php" class="btn btn-warning">Create Group</a>
                            <a href="join_group.php?product_id=<?php echo htmlspecialchars($productId); ?>"
                                class="btn btn-info">Join Group</a>
                            <a href="dashboard.php" class="btn btn-secondary">Go Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>