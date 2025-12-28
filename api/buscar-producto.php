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
$sub_almacen_id = $_SESSION['user_sub_almacen_id'] ?? null;

// Si el usuario es de compras, buscar en almacén general (null)
if ($_SESSION['user_rol'] === 'compras') {
    $sub_almacen_id = null;
}

$producto_model = new Producto();
$producto = $producto_model->buscarPorCodigo($codigo, $sub_almacen_id);

header('Content-Type: application/json');

if ($producto) {
    echo json_encode([
        'existe' => true,
        'producto' => [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'descripcion' => $producto['descripcion'],
            'unidad' => $producto['unidad'],
            'precio_unitario' => $producto['precio_unitario'],
            'stock_minimo' => $producto['stock_minimo'],
            'cantidad_actual' => $producto['cantidad']
        ]
    ]);
} else {
    echo json_encode(['existe' => false]);
}
