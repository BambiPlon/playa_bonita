<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

if (!in_array($user['rol'], ['gerencia', 'gerencia_general'])) {
    header('Location: requisiciones.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requisicion_id = intval($_POST['requisicion_id']);
    $accion = $_POST['accion'];
    
    $db = getConnection();
    
    try {
        if ($accion === 'rechazar') {
            $justificacion = trim($_POST['justificacion_general'] ?? '');
            
            if (empty($justificacion)) {
                throw new Exception('Debe proporcionar una justificación para rechazar');
            }
            
            $campo_aprobador = $user['rol'] === 'gerencia_general' ? 'aprobado_por_general' : 'aprobado_por';
            $campo_fecha = $user['rol'] === 'gerencia_general' ? 'fecha_aprobacion_general' : 'fecha_aprobacion';
            
            $stmt = $db->prepare("UPDATE requisiciones SET estado = 'rechazada', justificacion_rechazo = ?, {$campo_aprobador} = ?, {$campo_fecha} = NOW() WHERE id = ?");
            $stmt->bind_param("sii", $justificacion, $user['id'], $requisicion_id);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $db->prepare("UPDATE requisicion_detalles SET aprobado = 0, justificacion_rechazo = ? WHERE requisicion_id = ?");
            $stmt->bind_param("si", $justificacion, $requisicion_id);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $db->prepare("SELECT id FROM usuarios WHERE rol = 'compras' AND activo = 1");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $rol_rechazo = $user['rol'] === 'gerencia_general' ? 'Gerencia General' : 'Gerencia';
            while ($compras = $result->fetch_assoc()) {
                $titulo = "Requisición Rechazada";
                $mensaje = "La requisición REQ-{$requisicion_id} ha sido rechazada por {$rol_rechazo}.";
                $tipo = "rechazada";
                
                $stmtNot = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, created_at, leida) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                $stmtNot->bind_param("isssi", $compras['id'], $tipo, $titulo, $mensaje, $requisicion_id);
                $stmtNot->execute();
                $stmtNot->close();
            }
            $stmt->close();
            
            // Notificar al solicitante
            $stmt = $db->prepare("SELECT usuario_id FROM requisiciones WHERE id = ?");
            $stmt->bind_param("i", $requisicion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $requisicion = $result->fetch_assoc();
            $stmt->close();
            
            if ($requisicion) {
                $titulo = "Tu Requisición fue Rechazada";
                $mensaje = "Tu requisición REQ-{$requisicion_id} ha sido rechazada por {$rol_rechazo}.";
                $tipo = "rechazada";
                
                $stmtNot = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, created_at, leida) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                $stmtNot->bind_param("isssi", $requisicion['usuario_id'], $tipo, $titulo, $mensaje, $requisicion_id);
                $stmtNot->execute();
                $stmtNot->close();
            }
            
            $_SESSION['mensaje'] = 'Requisición rechazada correctamente';
            $_SESSION['tipo_mensaje'] = 'warning';
            
        } elseif ($accion === 'aprobar') {
            $articulosAprobados = $_POST['articulos_aprobados'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $justificaciones = $_POST['justificaciones'] ?? [];
            
            if (empty($articulosAprobados)) {
                throw new Exception('Debe aprobar al menos un artículo');
            }
            
            // Obtener todos los artículos de la requisición
            $stmt = $db->prepare("SELECT id FROM requisicion_detalles WHERE requisicion_id = ?");
            $stmt->bind_param("i", $requisicion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $todosArticulos = [];
            while ($row = $result->fetch_assoc()) {
                $todosArticulos[] = $row['id'];
            }
            $stmt->close();
            
            // Actualizar cada artículo
            foreach ($todosArticulos as $articuloId) {
                $aprobado = in_array($articuloId, $articulosAprobados) ? 1 : 0;
                $justif = trim($justificaciones[$articuloId] ?? '');
                $nuevaCantidad = isset($cantidades[$articuloId]) ? floatval($cantidades[$articuloId]) : null;
                
                if ($nuevaCantidad !== null && $nuevaCantidad > 0) {
                    $stmt = $db->prepare("UPDATE requisicion_detalles SET aprobado = ?, justificacion_rechazo = ?, cantidad = ? WHERE id = ?");
                    $stmt->bind_param("isdi", $aprobado, $justif, $nuevaCantidad, $articuloId);
                } else {
                    $stmt = $db->prepare("UPDATE requisicion_detalles SET aprobado = ?, justificacion_rechazo = ? WHERE id = ?");
                    $stmt->bind_param("isi", $aprobado, $justif, $articuloId);
                }
                $stmt->execute();
                $stmt->close();
            }
            
            // Calcular monto total de artículos aprobados
            $stmt = $db->prepare("SELECT COALESCE(SUM(precio_cotizado * cantidad), 0) as total FROM requisicion_detalles WHERE requisicion_id = ? AND aprobado = 1");
            $stmt->bind_param("i", $requisicion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $montoAprobado = floatval($row['total']);
            $stmt->close();
            
            if ($user['rol'] === 'gerencia') {
                // Gerencia envía a Gerencia General
                $nuevoEstado = 'en_gerencia_general';
                $stmt = $db->prepare("UPDATE requisiciones SET estado = ?, aprobado_por = ?, fecha_aprobacion = NOW(), monto_cotizado = ?, justificacion_rechazo = ? WHERE id = ?");
                $stmt->bind_param("sidsi", $nuevoEstado, $user['id'], $montoAprobado, $justificacionGeneral, $requisicion_id);
                $stmt->execute();
                $stmt->close();
                
                // Notificar a Gerencia General
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE rol = 'gerencia_general' AND activo = 1");
                $stmt->execute();
                $result = $stmt->get_result();
                
                $articulosAprobadosCount = count($articulosAprobados);
                $totalArticulos = count($todosArticulos);
                
                while ($gerenciaGeneral = $result->fetch_assoc()) {
                    $titulo = "Requisición Pendiente de Aprobación Final";
                    $mensaje = "La requisición REQ-{$requisicion_id} ha sido aprobada por Gerencia y requiere su aprobación final. Artículos: {$articulosAprobadosCount}/{$totalArticulos}. Monto: $" . number_format($montoAprobado, 2);
                    $tipo = "pendiente_aprobacion";
                    
                    $stmtNot = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, created_at, leida) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                    $stmtNot->bind_param("isssi", $gerenciaGeneral['id'], $tipo, $titulo, $mensaje, $requisicion_id);
                    $stmtNot->execute();
                    $stmtNot->close();
                }
                $stmt->close();
                
                $_SESSION['mensaje'] = 'Requisición enviada a Gerencia General para aprobación final';
                
            } else if ($user['rol'] === 'gerencia_general') {
                // Gerencia General aprueba finalmente
                $nuevoEstado = 'aprobada';
                $stmt = $db->prepare("UPDATE requisiciones SET estado = ?, aprobado_por_general = ?, fecha_aprobacion_general = NOW(), monto_cotizado = ?, justificacion_rechazo = ? WHERE id = ?");
                $stmt->bind_param("sidsi", $nuevoEstado, $user['id'], $montoAprobado, $justificacionGeneral, $requisicion_id);
                $stmt->execute();
                $stmt->close();
                
                // Notificar a Compras
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE rol = 'compras' AND activo = 1");
                $stmt->execute();
                $result = $stmt->get_result();
                
                $articulosAprobadosCount = count($articulosAprobados);
                $totalArticulos = count($todosArticulos);
                
                while ($compras = $result->fetch_assoc()) {
                    $titulo = "Requisición Aprobada por Gerencia General";
                    $mensaje = "La requisición REQ-{$requisicion_id} ha sido aprobada por Gerencia General. Artículos aprobados: {$articulosAprobadosCount}/{$totalArticulos}. Monto total: $" . number_format($montoAprobado, 2);
                    $tipo = "aprobada";
                    
                    $stmtNot = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, created_at, leida) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                    $stmtNot->bind_param("isssi", $compras['id'], $tipo, $titulo, $mensaje, $requisicion_id);
                    $stmtNot->execute();
                    $stmtNot->close();
                }
                $stmt->close();
                
                // Notificar al solicitante
                $stmt = $db->prepare("SELECT usuario_id FROM requisiciones WHERE id = ?");
                $stmt->bind_param("i", $requisicion_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $requisicion = $result->fetch_assoc();
                $stmt->close();
                
                if ($requisicion) {
                    $titulo = "Tu Requisición fue Aprobada";
                    $mensaje = "Tu requisición REQ-{$requisicion_id} ha sido aprobada por Gerencia General. Artículos aprobados: {$articulosAprobadosCount}/{$totalArticulos}.";
                    $tipo = "aprobada";
                    
                    $stmtNot = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, created_at, leida) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                    $stmtNot->bind_param("isssi", $requisicion['usuario_id'], $tipo, $titulo, $mensaje, $requisicion_id);
                    $stmtNot->execute();
                    $stmtNot->close();
                }
                
                $_SESSION['mensaje'] = 'Requisición aprobada correctamente';
            }
            
            $_SESSION['tipo_mensaje'] = 'success';
        }
        
    } catch (Exception $e) {
        $_SESSION['mensaje'] = 'Error: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
    }
}

header('Location: requisiciones.php');
exit;
