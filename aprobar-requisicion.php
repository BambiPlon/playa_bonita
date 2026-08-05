<?php
require_once 'init.php';
require_once 'models/Bitacora.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$bitacora = new Bitacora();

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
            
            $rol_rechazo = $user['rol'] === 'gerencia_general' ? 'Gerencia Administrativa' : 'Gerencia';
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
            
            // Auto-ocultar para el usuario de gerencia/gerencia_general que rechaza
            $stmtOcultar = $db->prepare("INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id) VALUES (?, ?)");
            $stmtOcultar->bind_param("ii", $requisicion_id, $user['id']);
            $stmtOcultar->execute();
            $stmtOcultar->close();
            
            // Si gerencia_general rechaza, tambien ocultar para gerencia
            if ($user['rol'] === 'gerencia_general') {
                $stmtGerencia = $db->prepare("SELECT id FROM usuarios WHERE rol = 'gerencia' AND activo = 1");
                $stmtGerencia->execute();
                $resultGerencia = $stmtGerencia->get_result();
                while ($gerente = $resultGerencia->fetch_assoc()) {
                    $stmtOcultarG = $db->prepare("INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id) VALUES (?, ?)");
                    $stmtOcultarG->bind_param("ii", $requisicion_id, $gerente['id']);
                    $stmtOcultarG->execute();
                    $stmtOcultarG->close();
                }
                $stmtGerencia->close();
            }
            
            $_SESSION['mensaje'] = 'Requisicion rechazada correctamente';
            $_SESSION['tipo_mensaje'] = 'warning';
            
            // Registrar en bitacora
            $bitacora->registrar(
                $user['id'],
                $user['nombre_completo'],
                'rechazar',
                'requisiciones',
                'Requisicion #' . $requisicion_id . ' rechazada por ' . $rol_rechazo . '. Motivo: ' . substr($justificacion, 0, 100),
                null,
                ['requisicion_id' => $requisicion_id, 'justificacion' => $justificacion]
            );
            
        } elseif ($accion === 'aprobar') {
            $articulosAprobados = $_POST['articulos_aprobados'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $justificaciones = $_POST['justificaciones'] ?? [];
            $justificacionGeneral = trim($_POST['justificacion_general'] ?? '');
            
            // Obtener todos los artículos de la requisición (incluyendo si son surtidos desde almacén)
            $stmt = $db->prepare("SELECT id, COALESCE(surtido_almacen, 0) as surtido_almacen, precio_cotizado, proveedor_id FROM requisicion_detalles WHERE requisicion_id = ?");
            $stmt->bind_param("i", $requisicion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $todosArticulos = [];
            $articulosAlmacen = [];
            $articulosNormales = [];
            while ($row = $result->fetch_assoc()) {
                $todosArticulos[] = $row['id'];
                // Detectar si es de almacén por campo O por precio 0 sin proveedor
                $esDeAlmacen = ($row['surtido_almacen'] == 1) || 
                    (floatval($row['precio_cotizado'] ?? 0) == 0 && empty($row['proveedor_id']));
                if ($esDeAlmacen) {
                    $articulosAlmacen[] = $row['id'];
                } else {
                    $articulosNormales[] = $row['id'];
                }
            }
            $stmt->close();
            
            // Validar: si NO hay artículos de almacén Y no hay artículos aprobados, error
            // Si hay artículos de almacén, se puede aprobar (los normales no aprobados se rechazan)
            $hayArticulosAlmacen = count($articulosAlmacen) > 0;
            $todosDeAlmacen = count($articulosNormales) == 0;
            if (!$todosDeAlmacen && empty($articulosAprobados) && !$hayArticulosAlmacen) {
                throw new Exception('Debe aprobar al menos un artículo');
            }
            
            // Actualizar cada artículo (NO modificar los que son surtidos desde almacén)
            foreach ($todosArticulos as $articuloId) {
                // Si es surtido desde almacén, NO modificar (ya está aprobado con precio 0)
                if (in_array($articuloId, $articulosAlmacen)) {
                    continue;
                }
                
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
            
            // Calcular monto total de artículos aprobados (EXCLUYENDO los surtidos desde almacén)
            $stmt = $db->prepare("SELECT COALESCE(SUM(precio_cotizado * cantidad), 0) as total FROM requisicion_detalles WHERE requisicion_id = ? AND aprobado = 1 AND (surtido_almacen = 0 OR surtido_almacen IS NULL)");
            $stmt->bind_param("i", $requisicion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $montoAprobado = floatval($row['total']);
            $stmt->close();
            
            if ($user['rol'] === 'gerencia') {
                // Gerencia envía a Gerencia Administrativa
                $nuevoEstado = 'en_gerencia_general';
                $stmt = $db->prepare("UPDATE requisiciones SET estado = ?, aprobado_por = ?, fecha_aprobacion = NOW(), monto_cotizado = ?, justificacion_rechazo = ? WHERE id = ?");
                $stmt->bind_param("sidsi", $nuevoEstado, $user['id'], $montoAprobado, $justificacionGeneral, $requisicion_id);
                $stmt->execute();
                $stmt->close();
                
                // Ocultar para el usuario actual (gerencia) después de aprobar
                $stmtOcultar = $db->prepare("INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id) VALUES (?, ?)");
                $stmtOcultar->bind_param("ii", $requisicion_id, $user['id']);
                $stmtOcultar->execute();
                $stmtOcultar->close();
                
                // Notificar a Gerencia Administrativa
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE rol = 'gerencia_general' AND activo = 1");
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Contar artículos aprobados incluyendo los de almacén
                $articulosAprobadosCount = count($articulosAprobados) + count($articulosAlmacen);
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
                
                $_SESSION['mensaje'] = 'Requisición enviada a Gerencia Administrativa para aprobación final';
                
                // Registrar en bitacora
                $bitacora->registrar(
                    $user['id'],
                    $user['nombre_completo'],
                    'aprobar_gerencia',
                    'requisiciones',
                    'Requisicion #' . $requisicion_id . ' enviada a Gerencia Administrativa. Articulos: ' . $articulosAprobadosCount . '/' . $totalArticulos,
                    null,
                    ['requisicion_id' => $requisicion_id, 'articulos_aprobados' => $articulosAprobadosCount, 'monto' => $montoAprobado]
                );
                
            } else if ($user['rol'] === 'gerencia_general') {
                // Gerencia Administrativa aprueba finalmente
                $nuevoEstado = 'aprobada';
                $stmt = $db->prepare("UPDATE requisiciones SET estado = ?, aprobado_por_general = ?, fecha_aprobacion_general = NOW(), monto_cotizado = ?, justificacion_rechazo = ? WHERE id = ?");
                $stmt->bind_param("sidsi", $nuevoEstado, $user['id'], $montoAprobado, $justificacionGeneral, $requisicion_id);
                $stmt->execute();
                $stmt->close();
                
                // Ocultar para el usuario actual (gerencia_general)
                $stmtOcultar = $db->prepare("INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id) VALUES (?, ?)");
                $stmtOcultar->bind_param("ii", $requisicion_id, $user['id']);
                $stmtOcultar->execute();
                $stmtOcultar->close();
                
                // Ocultar para usuarios de gerencia
                $stmtGerencia = $db->prepare("SELECT id FROM usuarios WHERE rol = 'gerencia' AND activo = 1");
                $stmtGerencia->execute();
                $resultGerencia = $stmtGerencia->get_result();
                while ($gerente = $resultGerencia->fetch_assoc()) {
                    $stmtOcultarG = $db->prepare("INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id) VALUES (?, ?)");
                    $stmtOcultarG->bind_param("ii", $requisicion_id, $gerente['id']);
                    $stmtOcultarG->execute();
                    $stmtOcultarG->close();
                }
                $stmtGerencia->close();
                
                // Notificar a Compras
                $stmt = $db->prepare("SELECT id FROM usuarios WHERE rol = 'compras' AND activo = 1");
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Contar artículos aprobados incluyendo los de almacén
                $articulosAprobadosCount = count($articulosAprobados) + count($articulosAlmacen);
                $totalArticulos = count($todosArticulos);
                
                while ($compras = $result->fetch_assoc()) {
                    $titulo = "Requisición Aprobada por Gerencia Administrativa";
                    $mensaje = "La requisición REQ-{$requisicion_id} ha sido aprobada por Gerencia Administrativa. Artículos aprobados: {$articulosAprobadosCount}/{$totalArticulos}. Monto total: $" . number_format($montoAprobado, 2);
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
                    $mensaje = "Tu requisición REQ-{$requisicion_id} ha sido aprobada por Gerencia Administrativa. Artículos aprobados: {$articulosAprobadosCount}/{$totalArticulos}.";
                    $tipo = "aprobada";
                    
                    $stmtNot = $db->prepare("INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id, created_at, leida) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                    $stmtNot->bind_param("isssi", $requisicion['usuario_id'], $tipo, $titulo, $mensaje, $requisicion_id);
                    $stmtNot->execute();
                    $stmtNot->close();
                }
                
                $_SESSION['mensaje'] = 'Requisición aprobada correctamente';
                
                // Registrar en bitacora
                $bitacora->registrar(
                    $user['id'],
                    $user['nombre_completo'],
                    'aprobar_general',
                    'requisiciones',
                    'Requisicion #' . $requisicion_id . ' aprobada por Gerencia Administrativa. Articulos: ' . $articulosAprobadosCount . '/' . $totalArticulos . '. Monto: $' . number_format($montoAprobado, 2),
                    null,
                    ['requisicion_id' => $requisicion_id, 'articulos_aprobados' => $articulosAprobadosCount, 'monto' => $montoAprobado]
                );
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
