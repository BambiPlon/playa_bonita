<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

require_once '../config/database.php';
require_once '../controllers/SalidaController.php';

$requisicion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$requisicion_id) {
    echo json_encode(['error' => 'ID de requisicion no valido']);
    exit;
}

$db = getConnection();
$salidaController = new SalidaController($db);
$productos = $salidaController->obtenerProductosRequisicion($requisicion_id);

echo json_encode(['productos' => $productos]);
