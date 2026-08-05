<?php
require_once 'init.php';
require_once 'models/Bitacora.php';

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
    $surtir_almacen = $_POST['surtir_almacen'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    $porcentaje_iva = floatval($_POST['porcentaje_iva'] ?? 16);
    $productos_incluidos = $_POST['productos_incluidos'] ?? [];
    
    $requisicionModel = new Requisicion();
    $notificacionModel = new Notificacion();
    $bitacora = new Bitacora();
    
    // Obtener información de la requisición
    $requisicion = $requisicionModel->obtenerPorId($requisicion_id);
    if (!$requisicion) {
        $_SESSION['mensaje'] = 'Requisición no encontrada';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: requisiciones.php');
        exit;
    }
    
    $subtotalSinIVA = 0;
    $productosSurtidosAlmacen = [];
    $productosQuitados = [];
    $db = getConnection();
    
    try {
        $db->begin_transaction();
        
        // Primero, marcar como no aprobados los productos quitados
        $detallesReq = $requisicionModel->obtenerDetalles($requisicion_id);
        foreach ($detallesReq as $det) {
            if (!in_array($det['id'], $productos_incluidos)) {
                // Este producto fue quitado por compras
                $stmtQuitar = $db->prepare("UPDATE requisicion_detalles SET aprobado = 0, justificacion_rechazo = 'Quitado por Compras durante cotización' WHERE id = ?");
                $stmtQuitar->bind_param("i", $det['id']);
                $stmtQuitar->execute();
                $stmtQuitar->close();
                $productosQuitados[] = $det['producto_nombre'];
            }
        }
        
        foreach ($precios as $detalle_id => $precio) {
            // Saltar productos quitados
            if (!in_array($detalle_id, $productos_incluidos)) {
                continue;
            }
            $precio = floatval($precio);
            $detalle_id = intval($detalle_id);
            $proveedor_id = isset($proveedores[$detalle_id]) ? intval($proveedores[$detalle_id]) : null;
            $unidad = isset($unidades[$detalle_id]) ? trim($unidades[$detalle_id]) : null;
            $inventario_id = isset($surtir_almacen[$detalle_id]) && !empty($surtir_almacen[$detalle_id]) ? intval($surtir_almacen[$detalle_id]) : null;
            
            // Obtener datos del detalle
            $stmtDet = $db->prepare("SELECT cantidad, producto_nombre FROM requisicion_detalles WHERE id = ?");
            $stmtDet->bind_param("i", $detalle_id);
            $stmtDet->execute();
            $resultDet = $stmtDet->get_result();
            $rowDet = $resultDet->fetch_assoc();
            // Usar cantidad del formulario si existe, si no usar la original
            $cantidad = isset($cantidades[$detalle_id]) && intval($cantidades[$detalle_id]) > 0 
                        ? intval($cantidades[$detalle_id]) 
                        : $rowDet['cantidad'];
            $producto_nombre = $rowDet['producto_nombre'];
            $stmtDet->close();
            
            // Actualizar cantidad en requisicion_detalles si fue modificada
            if (isset($cantidades[$detalle_id]) && intval($cantidades[$detalle_id]) != $rowDet['cantidad']) {
                $nuevaCantidad = intval($cantidades[$detalle_id]);
                $stmtUpdateCant = $db->prepare("UPDATE requisicion_detalles SET cantidad = ? WHERE id = ?");
                $stmtUpdateCant->bind_param("ii", $nuevaCantidad, $detalle_id);
                $stmtUpdateCant->execute();
                $stmtUpdateCant->close();
            }
            
            // Si se selecciono surtir desde almacen
            if ($inventario_id) {
                // Verificar stock disponible
                $stmtStock = $db->prepare("SELECT cantidad, nombre, sub_almacen_id, codigo, unidad FROM inventario WHERE id = ?");
                $stmtStock->bind_param("i", $inventario_id);
                $stmtStock->execute();
                $resultStock = $stmtStock->get_result();
                $rowStock = $resultStock->fetch_assoc();
                $stockDisponible = $rowStock['cantidad'];
                $sub_almacen_id = $rowStock['sub_almacen_id'];
                $nombre_inventario = $rowStock['nombre'];
                $codigo_inventario = $rowStock['codigo'];
                $stmtStock->close();
                
                $cantidadASurtir = min($cantidad, $stockDisponible);
                
                if ($cantidadASurtir > 0) {
                    // Dar salida del inventario
                    $stmtSalida = $db->prepare("UPDATE inventario SET cantidad = cantidad - ?, updated_at = NOW() WHERE id = ?");
                    $stmtSalida->bind_param("ii", $cantidadASurtir, $inventario_id);
                    $stmtSalida->execute();
                    $stmtSalida->close();
                    
                    // Registrar la salida en salidas_almacen
                    $folio_salida = 'SAL-REQ-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    $folio_requisicion = $requisicion['folio'] ?? "REQ-{$requisicion_id}";
                    $motivo = "Surtido desde almacén para requisición {$folio_requisicion}";
                    $destino = $requisicion['solicitante'] ?? "Requisicion #{$requisicion_id}";
                    $fecha_salida = date('Y-m-d H:i:s');
                    
                    $stmtRegSalida = $db->prepare("INSERT INTO salidas_almacen (folio, usuario_id, sub_almacen_id, producto_id, cantidad, motivo, destino, fecha_salida) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtRegSalida->bind_param("siiissss", $folio_salida, $user['id'], $sub_almacen_id, $inventario_id, $cantidadASurtir, $motivo, $destino, $fecha_salida);
                    $stmtRegSalida->execute();
                    $stmtRegSalida->close();
                    
                    // Marcar el detalle como surtido desde almacen (precio 0, aprobado automaticamente)
                    $stmtMarcar = $db->prepare("UPDATE requisicion_detalles SET precio_cotizado = 0, aprobado = 1, surtido_almacen = 1 WHERE id = ?");
                    $stmtMarcar->bind_param("i", $detalle_id);
                    $stmtMarcar->execute();
                    $stmtMarcar->close();
                    
                    $productosSurtidosAlmacen[] = "$producto_nombre ($cantidadASurtir unidades desde código: $codigo_inventario)";
                }
                
                // Si no hay suficiente stock, la diferencia se debe cotizar (precio 0 por ahora)
            } else {
                // Cotizacion normal
                if ($unidad) {
                    $stmt = $db->prepare("UPDATE requisicion_detalles SET precio_cotizado = ?, proveedor_id = ?, unidad = ? WHERE id = ?");
                    $stmt->bind_param("disi", $precio, $proveedor_id, $unidad, $detalle_id);
                } else {
                    $stmt = $db->prepare("UPDATE requisicion_detalles SET precio_cotizado = ?, proveedor_id = ? WHERE id = ?");
                    $stmt->bind_param("dii", $precio, $proveedor_id, $detalle_id);
                }
                $stmt->execute();
                $stmt->close();
                
                $subtotalSinIVA += ($precio * $cantidad);
            }
        }
        
        $iva = $subtotalSinIVA * ($porcentaje_iva / 100);
        $totalConIVA = $subtotalSinIVA + $iva;
        
        // Guardar el SUBTOTAL SIN IVA en monto_cotizado (el IVA se calcula al mostrar)
        $stmt = $db->prepare("UPDATE requisiciones SET estado = 'en_gerencia', monto_cotizado = ?, porcentaje_iva = ?, fecha_cotizacion = NOW() WHERE id = ?");
        $stmt->bind_param("ddi", $subtotalSinIVA, $porcentaje_iva, $requisicion_id);
        $stmt->execute();
        $stmt->close();
        
        $db->commit();
        
        // Construir mensaje
        $mensaje_extra = '';
        if (!empty($productosSurtidosAlmacen)) {
            $mensaje_extra = ' Productos surtidos desde almacen: ' . implode(', ', $productosSurtidosAlmacen) . '.';
        }
        
        // Mensaje extra para productos quitados
        $mensaje_quitados = '';
        if (!empty($productosQuitados)) {
            $mensaje_quitados = ' | Productos quitados: ' . count($productosQuitados);
        }
        
        // Notificar a gerencia
        $notificacionModel->notificarRol(
            'gerencia',
            'Requisición cotizada',
            "La requisición #$requisicion_id ha sido cotizada por compras. Subtotal: $" . number_format($subtotalSinIVA, 2) . " + IVA ({$porcentaje_iva}%): $" . number_format($iva, 2) . " = Total: $" . number_format($totalConIVA, 2) . $mensaje_extra . $mensaje_quitados,
            'requisicion',
            $requisicion_id
        );
        
        // Registrar en bitacora
        $bitacora->registrar(
            $user['id'],
            $user['nombre_completo'],
            'cotizar_requisicion',
            'requisiciones',
            'Requisicion #' . $requisicion_id . ' (Folio: ' . ($requisicion['folio'] ?? 'N/A') . ') cotizada. Total: $' . number_format($totalConIVA, 2) . $mensaje_extra . $mensaje_quitados,
            null,
            ['requisicion_id' => $requisicion_id, 'total' => $totalConIVA, 'productos_almacen' => $productosSurtidosAlmacen, 'productos_quitados' => $productosQuitados]
        );
        
        $_SESSION['mensaje'] = 'Cotización enviada a gerencia exitosamente. Total con IVA (' . $porcentaje_iva . '%): $' . number_format($totalConIVA, 2) . $mensaje_extra . $mensaje_quitados;
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
