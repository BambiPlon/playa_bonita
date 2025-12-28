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

foreach ($detalles as $detalle) {
    if ($detalle['aprobado'] != 1) {
        continue; // Saltar productos no aprobados
    }
    
    $nombre = trim($detalle['producto_nombre']);
    $cantidad = intval($detalle['cantidad']);
    $unidad = trim($detalle['unidad']);
    $precio = floatval($detalle['precio_cotizado'] ?? 0);
    $codigo_original = trim($detalle['codigo_original'] ?? '');
    
    $producto_existente = null;
    
    if (!empty($codigo_original)) {
        $sql_check = "SELECT id, cantidad, sub_almacen_id FROM inventario 
                      WHERE codigo = ? 
                      AND sub_almacen_id = ?
                      LIMIT 1";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $codigo_original, $almacen_destino_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $producto_existente = $result_check->fetch_assoc();
        } else {
            // Verificar si el código existe en otro sub-almacén
            $sql_check_global = "SELECT id FROM inventario WHERE codigo = ? LIMIT 1";
            $stmt_check_global = $conn->prepare($sql_check_global);
            $stmt_check_global->bind_param("s", $codigo_original);
            $stmt_check_global->execute();
            $result_check_global = $stmt_check_global->get_result();
            
            // Si el código existe en otro sub-almacén, generar uno nuevo con sufijo
            if ($result_check_global->num_rows > 0) {
                $codigo_original = $codigo_original . '-SA' . $almacen_destino_id;
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
        if (empty($codigo_original)) {
            $codigo_original = 'ALM-' . $almacen_destino_id . '-' . time() . rand(100, 999);
        }
        
        $sql_insert = "INSERT INTO inventario (codigo, nombre, sub_almacen_id, cantidad, unidad, precio_unitario, stock_minimo) 
                       VALUES (?, ?, ?, ?, ?, ?, 10)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssiids", $codigo_original, $nombre, $almacen_destino_id, $cantidad, $unidad, $precio);
        
        if ($stmt_insert->execute()) {
            $productos_agregados++;
        } else {
            $errores[] = "Error al agregar: " . $nombre . " - " . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
}

// Marcar la requisición como agregada al inventario
$sql_marcar = "UPDATE requisiciones SET agregado_a_inventario = 1, estado = 'completada' WHERE id = ?";
$stmt_marcar = $conn->prepare($sql_marcar);
$stmt_marcar->bind_param("i", $requisicion_id);
$stmt_marcar->execute();
$stmt_marcar->close();

$conn->close();

// Preparar mensaje de resultado
if (count($errores) > 0) {
    $_SESSION['warning_message'] = "Algunos productos no pudieron ser procesados: " . implode(", ", $errores);
} else {
    $_SESSION['success_message'] = "Requisición agregada al inventario exitosamente. Productos agregados: $productos_agregados, Productos actualizados: $productos_actualizados";
}

header('Location: ver-requisicion.php?id=' . $requisicion_id);
exit;
?>
