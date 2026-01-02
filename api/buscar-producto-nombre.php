<?php
require_once '../config/database.php';
require_once '../models/Producto.php';
session_start();

// Verificar que el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar que se envió el nombre
if (!isset($_GET['nombre']) || strlen(trim($_GET['nombre'])) < 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre no proporcionado o muy corto']);
    exit;
}

$nombre = trim($_GET['nombre']);
$producto_model = new Producto();

// Buscar productos que coincidan con el nombre
$productos = $producto_model->buscarPorNombreParcial($nombre, 10);

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'productos' => $productos
]);
?>
