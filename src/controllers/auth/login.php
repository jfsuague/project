<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate empty fields
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: /public/login.php');
        exit();
    }

    // Check if the email exists
    $query = "SELECT * FROM USERS WHERE EMAIL = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['PASSWORD'])) {
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['email'] = $user['EMAIL'];
            $_SESSION['name'] = $user['NAME'];

            // Redirect to dashboard after successful login
            header('Location: /public/dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid password.';
            header('Location: /public/login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Email not found.';
        header('Location: /public/login.php');
        exit();
    }

    $conn->close();
} else {
    header('Location: /public/login.php');
    exit();
}
?>