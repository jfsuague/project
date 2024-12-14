<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'src/controllers/CategoryController.php';
require_once BASE_PATH . 'src/controllers/ProductController.php';



// Verificar si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header('Location: ' . BASE_PATH . 'public/login.php');
    exit();
}

try {
    $categories = CategoryController::getAllCategories();
} catch (Exception $e) {
    die("Error fetching categories: " . $e->getMessage());
}

$products = [];
$selectedCategory = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category_id'])) {
        $selectedCategory = $_POST['category_id'];
        try {
            $products = ProductController::getProductsByCategory($selectedCategory);
        } catch (Exception $e) {
            $_SESSION['error'] = "Error fetching products: " . $e->getMessage();
        }
    }

    if (isset($_POST['product_id'])) {
        if (empty($_POST['product_id'])) {
            $_SESSION['error'] = "Please select a product before proceeding.";
        } else {
            $_SESSION['selected_product'] = $_POST['product_id'];
            header('Location: create_or_join.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-light border-light">
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
    <div class="container mt-5s">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-center bg-dark text-light border-light">
                        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Select a Category</h5>

                        <!-- Display Alerts -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_SESSION['error']);
                                unset($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Display Categories -->
                        <form action="dashboard.php" method="POST" class="mb-4">
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($categories as $category): ?>
                                    <button type="submit" name="category_id" value="<?php echo $category['ID']; ?>"
                                        class="btn btn-secondary">
                                        <?php echo htmlspecialchars($category['NAME']); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </form>

                        <!-- Display Products -->
                        <?php if (!empty($products)): ?>
                            <form action="dashboard.php" method="POST">
                                <div class="mb-3">
                                    <label for="product" class="form-label">Select a Product</label>
                                    <select name="product_id" id="product"
                                        class="form-select bg-dark text-light border-light" required>
                                        <option value="" hidden>Select a product</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['ID']; ?>">
                                                <?php echo htmlspecialchars($product['NAME']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary">Next</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>