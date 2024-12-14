<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Validar campos vacíos
    if (empty($name) || empty($lastName) || empty($email) || empty($password) || empty($phone)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: ../../public/register.php');
        exit();
    }

    // Verificar si el correo ya está registrado
    $query = "SELECT * FROM USERS WHERE EMAIL = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Email is already registered.';
        header('Location: ../../public/register.php');
        exit();
    }

    // Encriptar contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insertar nuevo usuario
    $insertQuery = "INSERT INTO USERS (NAME, SURNAME, EMAIL, PASSWORD, PHONE) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('sssss', $name, $lastName, $email, $hashedPassword, $phone);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful! You can now log in.';
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
    }

    $stmt->close();
    $conn->close();

    // Redirigir de vuelta al formulario de registro
    header('Location: ../../../public/register.php');
    exit();
} else {
    // Si no es una solicitud POST, redirigir al formulario de registro
    header('Location: ../../../public/register.php');
    exit();
}
