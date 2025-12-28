<?php
session_start();
require_once '../config/database.php';
require_once '../models/Proveedor.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

// Validar datos
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

if ($estado !== 0 && $estado !== 1) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit;
}

try {
    $proveedor = new Proveedor();
    
    if ($proveedor->cambiarEstado($id, $estado)) {
        echo json_encode([
            'success' => true, 
            'message' => $estado === 1 ? 'Proveedor activado correctamente' : 'Proveedor desactivado correctamente'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo cambiar el estado del proveedor']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}
