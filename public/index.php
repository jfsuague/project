<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPOT</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 bg-dark text-light">
    <!-- Header -->
    <header class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom border-dark">
        <!-- Logo -->
        <img src="./assets/images/logo.png" alt="Logo" class="img-fluid" style="width: 64px; height: auto;">

        <!-- Action Buttons -->
        <div>
            <a href="/public/login.php" class="btn btn-primary me-2">Enter</a>
            <a href="/public/register.php" class="btn btn-secondary">Register</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container d-flex justify-content-center align-items-center flex-grow-1">
        <div class="row w-100 align-items-center text-center">
            <!-- Left Image -->
            <div class="col-md-6">
                <img src="./assets/images/ahorro.png" alt="Placeholder Image" class="img-fluid">
            </div>

            <!-- Right Text -->
            <div class="col-md-6">
                <h2 class="mb-3">Group Buy and Save More</h2>
                <p class="lead">Join a group buy and save up to 75%</p>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>