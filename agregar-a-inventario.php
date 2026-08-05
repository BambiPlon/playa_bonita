<?php
require_once 'init.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_rol = $_SESSION['user_rol'];
$user_sub_almacen_id = $_SESSION['user_sub_almacen_id'] ?? null;

if ($user_rol === 'compras' || $user_rol === 'admin' || $user_rol === 'gerencia' || $user_rol === 'gerencia_general') {
    // Estos roles pueden agregar al almacén general
    $puede_agregar_almacen_general = true;
} elseif ($user_sub_almacen_id) {
    // Usuarios con sub-almacén solo pueden agregar a su propio sub-almacén
    $puede_agregar_almacen_general = false;
} else {
    $_SESSION['error_message'] = 'No tienes permisos para agregar productos al inventario';
    header('Location: requisiciones.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'ID de requisición no especificado';
    header('Location: requisiciones.php');
    exit;
}

$requisicion_id = intval($_GET['id']);

// Obtener la requisición
$requisicion = new Requisicion();
$req = $requisicion->obtenerPorId($requisicion_id);

if (!$req) {
    $_SESSION['error_message'] = 'Requisición no encontrada';
    header('Location: requisiciones.php');
    exit;
}

// Verificar que la requisición esté aprobada
if ($req['estado'] !== 'aprobada') {
    $_SESSION['error_message'] = 'Solo se pueden agregar al inventario requisiciones aprobadas';
    header('Location: ver-requisicion.php?id=' . $requisicion_id);
    exit;
}

// Verificar que no haya sido agregada previamente
if ($req['agregado_a_inventario'] == 1) {
    $_SESSION['warning_message'] = 'Esta requisición ya fue agregada al inventario';
    header('Location: ver-requisicion.php?id=' . $requisicion_id);
    exit;
}

// Verificar si todos los productos son de almacén
if ($requisicion->todosProductosDeAlmacen($requisicion_id)) {
    $_SESSION['warning_message'] = 'Todos los productos de esta requisicion fueron surtidos desde almacen. No hay nada que agregar al inventario.';
    header('Location: ver-requisicion.php?id=' . $requisicion_id);
    exit;
}

// Obtener detalles de la requisición
$detalles = $requisicion->obtenerDetalles($requisicion_id);

$conn = getConnection();

if ($puede_agregar_almacen_general) {
    $almacen_destino_id = 100; // Almacén general
} else {
    $almacen_destino_id = $user_sub_almacen_id; // Sub-almacén del usuario
}

$productos_agregados = 0;
$productos_actualizados = 0;
$errores = [];

$productos_desde_almacen = 0;

foreach ($detalles as $detalle) {
    if ($detalle['aprobado'] != 1) {
        continue; // Saltar productos no aprobados
    }
    
    // Saltar productos que fueron surtidos desde almacén (ya existen en inventario)
    if (isset($detalle['surtido_almacen']) && $detalle['surtido_almacen'] == 1) {
        $productos_desde_almacen++;
        continue;
    }
    
    $nombre = trim($detalle['producto_nombre']);
    $cantidad = intval($detalle['cantidad']);
    // Asegurar que la unidad siempre tenga valor - usar de detalle o de inventario original
    $unidad_detalle = isset($detalle['unidad']) ? trim($detalle['unidad']) : '';
    $unidad = !empty($unidad_detalle) ? $unidad_detalle : 'pieza';
    $precio = floatval($detalle['precio_cotizado'] ?? 0);
    $codigo_original = trim($detalle['codigo_original'] ?? '');
    
    $producto_existente = null;
    
    // Si no hay codigo, buscar por nombre + unidad + almacen
    if (empty($codigo_original)) {
        $sql_nombre = "SELECT id, cantidad, unidad, sub_almacen_id FROM inventario 
                       WHERE nombre = ? AND sub_almacen_id = ? AND LOWER(unidad) = LOWER(?)
                       LIMIT 1";
        $stmt_nombre = $conn->prepare($sql_nombre);
        $stmt_nombre->bind_param("sis", $nombre, $almacen_destino_id, $unidad);
        $stmt_nombre->execute();
        $result_nombre = $stmt_nombre->get_result();
        if ($result_nombre->num_rows > 0) {
            $producto_existente = $result_nombre->fetch_assoc();
        }
        $stmt_nombre->close();
    }
    
    if (!$producto_existente && !empty($codigo_original)) {
        $sql_check = "SELECT id, cantidad, unidad, sub_almacen_id FROM inventario 
                      WHERE codigo = ? 
                      AND sub_almacen_id = ?
                      LIMIT 1";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $codigo_original, $almacen_destino_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $row_found = $result_check->fetch_assoc();
            // Si la unidad es diferente, tratar como producto nuevo
            if (!empty($unidad) && strtolower(trim($row_found['unidad'])) !== strtolower($unidad)) {
                $producto_existente = null;
                $codigo_original = 'ALM-' . $almacen_destino_id . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            } else {
                $producto_existente = $row_found;
            }
        } else {
            // Verificar si el código existe en otro sub-almacén
            $sql_check_global = "SELECT id FROM inventario WHERE codigo = ? LIMIT 1";
            $stmt_check_global = $conn->prepare($sql_check_global);
            $stmt_check_global->bind_param("s", $codigo_original);
            $stmt_check_global->execute();
            $result_check_global = $stmt_check_global->get_result();
            
            // Si el código existe en otro sub-almacén, generar uno nuevo unico
            if ($result_check_global->num_rows > 0) {
                $codigo_original = 'ALM-' . $almacen_destino_id . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            }
            $stmt_check_global->close();
        }
        $stmt_check->close();
    }
    
    // Si el producto existe en el almacén destino, actualizar cantidad
    if ($producto_existente) {
        $nueva_cantidad = $producto_existente['cantidad'] + $cantidad;
        
        $sql_update = "UPDATE inventario 
                       SET cantidad = ?, 
                           precio_unitario = ?,
                           unidad = ?,
                           updated_at = NOW() 
                       WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("idsi", $nueva_cantidad, $precio, $unidad, $producto_existente['id']);
        
        if ($stmt_update->execute()) {
            $productos_actualizados++;
        } else {
            $errores[] = "Error al actualizar: " . $nombre;
        }
        $stmt_update->close();
    } else {
        // Si no existe, crear nuevo producto en el almacén destino
        // Generar codigo unico garantizado
        $codigo_unico = false;
        $intentos = 0;
        while (!$codigo_unico && $intentos < 10) {
            if (empty($codigo_original) || $intentos > 0) {
                $codigo_original = 'ALM-' . $almacen_destino_id . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            }
            // Verificar que no exista
            $sql_verify = "SELECT id FROM inventario WHERE codigo = ? AND sub_almacen_id = ? LIMIT 1";
            $stmt_verify = $conn->prepare($sql_verify);
            $stmt_verify->bind_param("si", $codigo_original, $almacen_destino_id);
            $stmt_verify->execute();
            $result_verify = $stmt_verify->get_result();
            if ($result_verify->num_rows === 0) {
                $codigo_unico = true;
            }
            $stmt_verify->close();
            $intentos++;
        }
        
        $sql_insert = "INSERT INTO inventario (codigo, nombre, sub_almacen_id, cantidad, unidad, precio_unitario, stock_minimo) 
                       VALUES (?, ?, ?, ?, ?, ?, 10)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssiisd", $codigo_original, $nombre, $almacen_destino_id, $cantidad, $unidad, $precio);
        
        if ($stmt_insert->execute()) {
            $productos_agregados++;
        } else {
            $errores[] = "Error al agregar: " . $nombre . " - " . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
}

// Marcar la requisición como agregada al inventario
$sql_marcar = "UPDATE requisiciones SET agregado_a_inventario = 1 WHERE id = ?";
$stmt_marcar = $conn->prepare($sql_marcar);
$stmt_marcar->bind_param("i", $requisicion_id);
$stmt_marcar->execute();
$stmt_marcar->close();

$conn->close();

// Preparar mensaje de resultado
$mensaje_almacen = $productos_desde_almacen > 0 ? " (Se omitieron $productos_desde_almacen productos ya surtidos desde almacen)" : "";

if (count($errores) > 0) {
    $_SESSION['warning_message'] = "Algunos productos no pudieron ser procesados: " . implode(", ", $errores) . $mensaje_almacen;
} else {
    $_SESSION['success_message'] = "Requisicion agregada al inventario exitosamente. Productos agregados: $productos_agregados, Productos actualizados: $productos_actualizados" . $mensaje_almacen;
}

header('Location: ver-requisicion.php?id=' . $requisicion_id);
exit;
?>
