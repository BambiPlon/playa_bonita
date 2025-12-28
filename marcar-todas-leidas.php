<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$conn = getConnection();
$sql = "UPDATE notificaciones SET leida = 1 WHERE usuario_id = ? AND leida = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->close();
$conn->close();

// Redirigir de vuelta
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: dashboard.php');
}
exit();
