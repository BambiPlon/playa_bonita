<?php
require_once __DIR__ . '/../config/database.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ]);
    exit;
}

header('Content-Type: application/json');

// Solo para peticiones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'toggle_bloqueo') {
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $nuevo_estado = intval($_POST['nuevo_estado'] ?? 1);
        
        if ($producto_id > 0) {
            try {
                $conn = getConnection();
                
                $sql = "UPDATE inventario SET activo = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception("Error preparando la consulta: " . $conn->error);
                }
                
                $stmt->bind_param("ii", $nuevo_estado, $producto_id);
                
                if ($stmt->execute()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Estado del producto actualizado correctamente',
                        'nuevo_estado' => $nuevo_estado
                    ]);
                } else {
                    throw new Exception("Error ejecutando la consulta: " . $stmt->error);
                }
                
                $stmt->close();
                $conn->close();
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el estado: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'ID de producto inválido'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
