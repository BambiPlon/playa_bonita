<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';
require_once 'models/Notificacion.php';

if (isset($_GET['id'])) {
    $notificacion_id = intval($_GET['id']);
    
    // Verificar que la notificación pertenece al usuario actual
    $conn = getConnection();
    $sql = "SELECT usuario_id FROM notificaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notificacion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['usuario_id'] == $_SESSION['user_id']) {
            // Eliminar la notificación
            $sql_delete = "DELETE FROM notificaciones WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $notificacion_id);
            $stmt_delete->execute();
            $stmt_delete->close();
        }
    }
    
    $stmt->close();
    $conn->close();
}

// Redirigir de vuelta
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: dashboard.php');
}
exit();
