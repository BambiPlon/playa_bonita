<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

if ($user['rol'] !== 'compras') {
    header('Location: requisiciones.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requisicion_id = intval($_POST['requisicion_id']);
    $precios = $_POST['precios'] ?? [];
    $proveedores = $_POST['proveedores'] ?? [];
    $unidades = $_POST['unidades'] ?? [];
    $porcentaje_iva = floatval($_POST['porcentaje_iva'] ?? 16);
    
    $requisicionModel = new Requisicion();
    $notificacionModel = new Notificacion();
    
    $subtotalSinIVA = 0;
    $db = getConnection();
    
    try {
        $db->begin_transaction();
        
        foreach ($precios as $detalle_id => $precio) {
            $precio = floatval($precio);
            $detalle_id = intval($detalle_id);
            $proveedor_id = isset($proveedores[$detalle_id]) ? intval($proveedores[$detalle_id]) : null;
            $unidad = isset($unidades[$detalle_id]) ? trim($unidades[$detalle_id]) : null;
            
            // Actualizar precio cotizado (sin IVA), proveedor y unidad
            if ($unidad) {
                $stmt = $db->prepare("UPDATE requisicion_detalles SET precio_cotizado = ?, proveedor_id = ?, unidad = ? WHERE id = ?");
                $stmt->bind_param("disi", $precio, $proveedor_id, $unidad, $detalle_id);
            } else {
                $stmt = $db->prepare("UPDATE requisicion_detalles SET precio_cotizado = ?, proveedor_id = ? WHERE id = ?");
                $stmt->bind_param("dii", $precio, $proveedor_id, $detalle_id);
            }
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
            
            $subtotalSinIVA += ($precio * $cantidad);
        }
        
        $iva = $subtotalSinIVA * ($porcentaje_iva / 100);
        $totalConIVA = $subtotalSinIVA + $iva;
        
        $stmt = $db->prepare("UPDATE requisiciones SET estado = 'en_gerencia', monto_cotizado = ?, porcentaje_iva = ?, fecha_cotizacion = NOW() WHERE id = ?");
        $stmt->bind_param("ddi", $totalConIVA, $porcentaje_iva, $requisicion_id);
        $stmt->execute();
        $stmt->close();
        
        $db->commit();
        
        // Notificar a gerencia
        $notificacionModel->notificarRol(
            'gerencia',
            'Requisición cotizada',
            "La requisición #$requisicion_id ha sido cotizada por compras. Subtotal: $" . number_format($subtotalSinIVA, 2) . " + IVA ({$porcentaje_iva}%): $" . number_format($iva, 2) . " = Total: $" . number_format($totalConIVA, 2),
            'requisicion',
            $requisicion_id
        );
        
        $_SESSION['mensaje'] = 'Cotización enviada a gerencia exitosamente. Total con IVA (' . $porcentaje_iva . '%): $' . number_format($totalConIVA, 2);
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
?>
