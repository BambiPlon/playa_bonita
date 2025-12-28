<?php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SESSION['user_rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No tiene permisos para esta acción']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

if ($estado !== 0 && $estado !== 1) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit;
}

if ($id == $_SESSION['user_id'] && $estado === 0) {
    echo json_encode(['success' => false, 'message' => 'No puede desactivarse a sí mismo']);
    exit;
}

try {
    $usuario = new Usuario();
    
    if ($usuario->cambiarEstado($id, $estado)) {
        echo json_encode([
            'success' => true, 
            'message' => $estado === 1 ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo cambiar el estado del usuario']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
