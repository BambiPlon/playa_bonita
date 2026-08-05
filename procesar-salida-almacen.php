<?php
/**
 * Procesa la salida de almacén para productos surtidos desde inventario
 * Solo procesa UNA VEZ - si ya fue procesado, solo genera el PDF
 */
require_once 'config/database.php';
require_once 'models/Bitacora.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$requisicion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($requisicion_id)) {
    $_SESSION['error_message'] = 'No se especificó la requisición';
    header('Location: requisiciones.php');
    exit;
}

$conn = getConnection();
$bitacora = new Bitacora();

// Verificar si la requisición existe y obtener su estado
$sql = "SELECT r.*, 
        COALESCE(r.salida_almacen_procesada, 0) as salida_procesada,
        r.folio_salida_almacen
        FROM requisiciones r WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requisicion_id);
$stmt->execute();
$requisicion = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$requisicion) {
    $_SESSION['error_message'] = 'Requisición no encontrada';
    header('Location: requisiciones.php');
    exit;
}

// Verificar si ya fue procesada
if ($requisicion['salida_procesada'] == 1) {
    // Ya fue procesada, solo mostrar el PDF
    header('Location: imprimir-salida-almacen-requisicion.php?id=' . $requisicion_id);
    exit;
}

// Obtener productos de almacén que necesitan salida
// Condición: surtido_almacen = 1 O (precio 0 sin proveedor y aprobado - datos antiguos)
$sqlProductos = "SELECT 
    rd.id,
    rd.producto_id,
    COALESCE(i.nombre, rd.producto_nombre, 'Producto') as producto_nombre,
    COALESCE(i.codigo, CONCAT('PROD-', LPAD(rd.id, 4, '0'))) as codigo,
    rd.cantidad,
    COALESCE(rd.unidad, i.unidad, 'pieza') as unidad,
    i.sub_almacen_id,
    COALESCE(s.nombre, 'Almacen General') as almacen_nombre,
    COALESCE(rd.surtido_almacen, 0) as surtido_almacen,
    COALESCE(rd.precio_cotizado, 0) as precio_cotizado,
    rd.proveedor_id
FROM requisicion_detalles rd
LEFT JOIN inventario i ON rd.producto_id = i.id
LEFT JOIN sub_almacenes s ON i.sub_almacen_id = s.id
WHERE rd.requisicion_id = ?
AND rd.aprobado = 1
AND (
    rd.surtido_almacen = 1 
    OR (COALESCE(rd.precio_cotizado, 0) = 0 AND rd.proveedor_id IS NULL)
)";

$stmt = $conn->prepare($sqlProductos);
$stmt->bind_param("i", $requisicion_id);
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($productos)) {
    $_SESSION['warning_message'] = 'No hay productos de almacén para procesar en esta requisición';
    header('Location: ver-requisicion.php?id=' . $requisicion_id);
    exit;
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Generar folio de salida
    $folio_salida = 'SAL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Obtener el ID del almacén general
    $sqlAlmacen = "SELECT id FROM sub_almacenes WHERE nombre LIKE '%general%' LIMIT 1";
    $resultAlmacen = $conn->query($sqlAlmacen);
    $almacen_general_id = 1;
    if ($resultAlmacen && $row = $resultAlmacen->fetch_assoc()) {
        $almacen_general_id = $row['id'];
    }
    
    $productos_procesados = 0;
    $errores = [];
    
    foreach ($productos as $index => $prod) {
        // Verificar stock disponible
        $sqlStock = "SELECT cantidad FROM inventario WHERE id = ?";
        $stmtStock = $conn->prepare($sqlStock);
        $stmtStock->bind_param("i", $prod['producto_id']);
        $stmtStock->execute();
        $stockResult = $stmtStock->get_result()->fetch_assoc();
        $stmtStock->close();
        
        $stock_actual = $stockResult ? intval($stockResult['cantidad']) : 0;
        $cantidad_solicitada = intval($prod['cantidad']);
        
        if ($stock_actual < $cantidad_solicitada) {
            $errores[] = "Producto '{$prod['producto_nombre']}': Stock insuficiente (disponible: {$stock_actual}, solicitado: {$cantidad_solicitada})";
            continue;
        }
        
        // Crear registro de salida
        $folio_item = $folio_salida . '-' . ($index + 1);
        $sub_almacen_id = $prod['sub_almacen_id'] ?? $almacen_general_id;
        
        // Verificar si existe la columna folio_grupo
        $checkColumn = $conn->query("SHOW COLUMNS FROM salidas_almacen LIKE 'folio_grupo'");
        $hasFolioGrupo = $checkColumn && $checkColumn->num_rows > 0;
        
        if ($hasFolioGrupo) {
            $sqlSalida = "INSERT INTO salidas_almacen 
                (folio, folio_grupo, usuario_id, sub_almacen_id, producto_id, cantidad, motivo, destino, fecha_salida) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $motivo = "Salida automatica por requisicion " . $requisicion['folio'];
            $destino = $requisicion['solicitante'] ?? 'Solicitante';
            
            $stmtSalida = $conn->prepare($sqlSalida);
            $stmtSalida->bind_param("ssiissss", 
                $folio_item,
                $folio_salida,
                $_SESSION['user_id'],
                $sub_almacen_id,
                $prod['producto_id'],
                $cantidad_solicitada,
                $motivo,
                $destino
            );
        } else {
            $sqlSalida = "INSERT INTO salidas_almacen 
                (folio, usuario_id, sub_almacen_id, producto_id, cantidad, motivo, destino, fecha_salida) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $motivo = "Salida automática por requisición " . $requisicion['folio'];
            $destino = $requisicion['solicitante'] ?? 'Solicitante';
            
            $stmtSalida = $conn->prepare($sqlSalida);
            $stmtSalida->bind_param("siissss", 
                $folio_item,
                $_SESSION['user_id'],
                $sub_almacen_id,
                $prod['producto_id'],
                $cantidad_solicitada,
                $motivo,
                $destino
            );
        }
        
        if ($stmtSalida->execute()) {
            // Descontar del inventario
            $sqlUpdate = "UPDATE inventario SET cantidad = cantidad - ? WHERE id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $cantidad_solicitada, $prod['producto_id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();
            
            $productos_procesados++;
        }
        $stmtSalida->close();
    }
    
    if ($productos_procesados == 0) {
        throw new Exception("No se pudo procesar ningún producto: " . implode(", ", $errores));
    }
    
    // Marcar la requisición como procesada
    // Verificar qué columnas existen
    $checkCol1 = $conn->query("SHOW COLUMNS FROM requisiciones LIKE 'salida_almacen_procesada'");
    $checkCol2 = $conn->query("SHOW COLUMNS FROM requisiciones LIKE 'folio_salida_almacen'");
    $checkCol3 = $conn->query("SHOW COLUMNS FROM requisiciones LIKE 'fecha_salida_almacen'");
    
    if ($checkCol1 && $checkCol1->num_rows > 0) {
        $sqlMark = "UPDATE requisiciones SET salida_almacen_procesada = 1";
        
        if ($checkCol2 && $checkCol2->num_rows > 0) {
            $sqlMark .= ", folio_salida_almacen = ?";
        }
        if ($checkCol3 && $checkCol3->num_rows > 0) {
            $sqlMark .= ", fecha_salida_almacen = NOW()";
        }
        $sqlMark .= " WHERE id = ?";
        
        $stmtMark = $conn->prepare($sqlMark);
        if ($checkCol2 && $checkCol2->num_rows > 0) {
            $stmtMark->bind_param("si", $folio_salida, $requisicion_id);
        } else {
            $stmtMark->bind_param("i", $requisicion_id);
        }
        $stmtMark->execute();
        $stmtMark->close();
    }
    
    $conn->commit();
    
    // Registrar en bitácora
    $bitacora->registrar(
        $_SESSION['user_id'],
        $_SESSION['user_nombre'] ?? 'Usuario',
        'salida_almacen',
        'inventario',
        'Salida de almacén procesada para requisición ' . $requisicion['folio'] . '. Productos: ' . $productos_procesados . '. Folio: ' . $folio_salida,
        null,
        ['requisicion_id' => $requisicion_id, 'folio_salida' => $folio_salida, 'productos' => $productos_procesados]
    );
    
    $_SESSION['success_message'] = "Salida de almacén procesada correctamente. Se descontaron {$productos_procesados} producto(s) del inventario.";
    
    if (!empty($errores)) {
        $_SESSION['warning_message'] = "Algunos productos no se procesaron: " . implode(", ", $errores);
    }
    
    // Redirigir al PDF
    header('Location: imprimir-salida-almacen-requisicion.php?id=' . $requisicion_id);
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = 'Error al procesar la salida: ' . $e->getMessage();
    header('Location: ver-requisicion.php?id=' . $requisicion_id);
    exit;
}
