<?php
require_once '../config/database.php';
require_once '../models/Requisicion.php';
session_start();

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener datos JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['requisicion_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de requisición no proporcionado']);
    exit;
}

$requisicion_id = intval($data['requisicion_id']);
$user_id = $_SESSION['user_id'];
$user_rol = $_SESSION['user_rol'];

// Verificar que la requisición existe y está rechazada
$requisicionModel = new Requisicion();
$requisicion = $requisicionModel->obtenerPorId($requisicion_id);

if (!$requisicion) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Requisición no encontrada']);
    exit;
}

if ($requisicion['estado'] !== 'rechazada') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solo se pueden ocultar requisiciones rechazadas']);
    exit;
}

// Verificar permisos: solo el creador de la requisición o compras pueden ocultarla
if ($requisicion['usuario_id'] != $user_id && $user_rol !== 'compras' && $user_rol !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para ocultar esta requisición']);
    exit;
}

// Ocultar la requisición
if ($requisicionModel->ocultarRequisicion($requisicion_id)) {
    echo json_encode(['success' => true, 'message' => 'Requisición ocultada correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al ocultar la requisición']);
}
?>
