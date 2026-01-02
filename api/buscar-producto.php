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

// Verificar que se envió el código
if (!isset($_GET['codigo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Código no proporcionado']);
    exit;
}

$codigo = $_GET['codigo'];

$producto_model = new Producto();
$producto = $producto_model->buscarPorCodigoGlobal($codigo);

header('Content-Type: application/json');

if ($producto) {
    echo json_encode([
        'existe' => true,
        'producto' => [
            'id' => $producto['id'],
            'codigo' => $producto['codigo'],
            'nombre' => $producto['nombre'],
            'descripcion' => $producto['descripcion'] ?? '',
            'unidad' => $producto['unidad'],
            'precio_unitario' => $producto['precio_unitario'] ?? 0,
            'stock_minimo' => $producto['stock_minimo'] ?? 10,
            'cantidad_actual' => $producto['cantidad']
        ]
    ]);
} else {
    echo json_encode(['existe' => false]);
}
?>
