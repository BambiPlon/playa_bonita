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

// Verificar que la requisición existe y está en un estado final
$requisicionModel = new Requisicion();
$requisicion = $requisicionModel->obtenerPorId($requisicion_id);

if (!$requisicion) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Requisición no encontrada']);
    exit;
}

if (!in_array($requisicion['estado'], ['rechazada', 'aprobada', 'completada'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solo se pueden ocultar requisiciones en estados finales (aprobadas, rechazadas o completadas)']);
    exit;
}

// Verificar permisos: el creador, compras o admin pueden ocultarla
if ($requisicion['usuario_id'] != $user_id && !in_array($user_rol, ['compras', 'admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para ocultar esta requisición']);
    exit;
}

if ($requisicionModel->ocultarRequisicionParaUsuario($requisicion_id, $user_id)) {
    echo json_encode(['success' => true, 'message' => 'Requisición ocultada correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al ocultar la requisición']);
}
?>
