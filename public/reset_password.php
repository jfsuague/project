<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white d-flex flex-column min-vh-100">
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
                        <a class="btn btn-outline-light" href="login.php">Login</a>
                        <a class="btn btn-outline-light" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Reset Password Form -->
    <div class="container d-flex flex-column align-items-center justify-content-center flex-grow-1">
        <div class="card bg-secondary text-white shadow p-4" style="width: 400px;">
            <h1 class="text-center mb-4">Reset Password</h1>

            <!-- Alert Section -->
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        " . htmlspecialchars($_SESSION['error']) . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        " . htmlspecialchars($_SESSION['success']) . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                unset($_SESSION['success']);
            }
            ?>

            <form action="../src/controllers/auth/reset_password.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control bg-dark text-white border-light" id="email" name="email"
                        placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control bg-dark text-white border-light" id="new_password"
                        name="new_password" placeholder="Enter your new password" required>
                </div>
                <button type="submit" class="btn btn-light w-100">Reset Password</button>
            </form>

            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none text-info">Back to Login</a>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>