<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . 'config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $newPassword = trim($_POST['new_password']);

    // Validar campos vacíos
    if (empty($email) || empty($newPassword)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: ../../public/reset_password.php');
        exit();
    }

    // Verificar si el correo electrónico está registrado
    $query = "SELECT * FROM USERS WHERE EMAIL = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Actualizar la contraseña
        $updateQuery = "UPDATE USERS SET PASSWORD = ? WHERE EMAIL = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('ss', $hashedPassword, $email);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Password updated successfully!';
            header('Location: ../../../public/reset_password.php');
        } else {
            $_SESSION['error'] = 'Password update failed.';
            header('Location: ../../../public/reset_password.php');
        }
    } else {
        $_SESSION['error'] = 'Email not found.';
        header('Location: ../../../public/reset_password.php');
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: ../../public/login.php');
    exit();
}
?>