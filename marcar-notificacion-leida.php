<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once 'config/database.php';
require_once 'models/Notificacion.php';
require_once 'controllers/NotificacionController.php';

if (isset($_POST['notificacion_id'])) {
    $notificacion_id = intval($_POST['notificacion_id']);
    
    // Verificar que la notificación pertenece al usuario actual
    $conn = getConnection();
    $sql = "SELECT usuario_id FROM notificaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notificacion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['usuario_id'] == $_SESSION['user_id']) {
            $notificacionController = new NotificacionController();
            $notificacionController->marcarLeida($notificacion_id);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Notificación no encontrada']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
}
