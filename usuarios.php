<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$usuarioModel = new Usuario();
$usuarios = $usuarioModel->obtenerTodos();

// Obtener sub-almacenes para el formulario
$sql = "SELECT * FROM sub_almacenes ORDER BY nombre";
$conn = getConnection();
$result = $conn->query($sql);
$sub_almacenes = [];
while($row = $result->fetch_assoc()) {
    $sub_almacenes[] = $row;
}

require 'includes/header.php';
require 'views/usuarios.view.php';
require 'includes/footer.php';
?>
