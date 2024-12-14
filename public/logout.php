<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . 'config/database.php';

session_destroy();

header('Location: /public/login.php');
exit();
?>