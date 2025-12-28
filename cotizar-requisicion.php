<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

// Solo el rol de compras puede cotizar
if ($user['rol'] !== 'compras') {
    header('Location: requisiciones.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requisicion_id = intval($_POST['requisicion_id']);
    $precios = $_POST['precios'] ?? [];
    $proveedores = $_POST['proveedores'] ?? [];
    
    $requisicionModel = new Requisicion();
    $notificacionModel = new Notificacion();
    
    $totalCotizado = 0;
    $db = getConnection();
    
    try {
        $db->begin_transaction();
        
        foreach ($precios as $detalle_id => $precio) {
            $precio = floatval($precio);
            $detalle_id = intval($detalle_id);
            $proveedor_id = isset($proveedores[$detalle_id]) ? intval($proveedores[$detalle_id]) : null;
            
            $stmt = $db->prepare("UPDATE requisicion_detalles SET precio_cotizado = ?, proveedor_id = ? WHERE id = ?");
            $stmt->bind_param("dii", $precio, $proveedor_id, $detalle_id);
            $stmt->execute();
            $stmt->close();
            
            // Obtener cantidad para calcular subtotal
            $stmtCant = $db->prepare("SELECT cantidad FROM requisicion_detalles WHERE id = ?");
            $stmtCant->bind_param("i", $detalle_id);
            $stmtCant->execute();
            $result = $stmtCant->get_result();
            $row = $result->fetch_assoc();
            $cantidad = $row['cantidad'];
            $stmtCant->close();
            
            $totalCotizado += ($precio * $cantidad);
        }
        
        // Actualizar requisición
        $stmt = $db->prepare("UPDATE requisiciones SET estado = 'en_gerencia', monto_cotizado = ?, fecha_cotizacion = NOW() WHERE id = ?");
        $stmt->bind_param("di", $totalCotizado, $requisicion_id);
        $stmt->execute();
        $stmt->close();
        
        $db->commit();
        
        // Notificar a gerencia
        $notificacionModel->notificarRol(
            'gerencia',
            'Requisición cotizada',
            "La requisición #$requisicion_id ha sido cotizada por compras. Monto total: $" . number_format($totalCotizado, 2),
            'requisicion',
            $requisicion_id
        );
        
        $_SESSION['mensaje'] = 'Cotización enviada a gerencia exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
        
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['mensaje'] = 'Error al cotizar: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
    
    header('Location: requisiciones.php');
    exit;
}

header('Location: requisiciones.php');
exit;
