<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Plantilla.php';

$plantilla_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$plantilla_id) {
    echo json_encode(['error' => 'ID de plantilla requerido']);
    exit;
}

$plantillaModel = new Plantilla();
$plantilla = $plantillaModel->obtenerPorId($plantilla_id, $_SESSION['user_id']);

if (!$plantilla) {
    echo json_encode(['error' => 'Plantilla no encontrada']);
    exit;
}

$productos = $plantillaModel->obtenerProductos($plantilla_id);

echo json_encode([
    'plantilla' => $plantilla,
    'productos' => $productos
]);
