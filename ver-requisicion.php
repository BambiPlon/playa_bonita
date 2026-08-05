<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$requisicionModel = new Requisicion();
$proveedorModel = new Proveedor();

$requisicion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($requisicion_id === 0) {
    header('Location: requisiciones.php');
    exit;
}

$requisicion = $requisicionModel->obtenerPorId($requisicion_id);
$detalles = $requisicionModel->obtenerDetalles($requisicion_id);

if (!$requisicion) {
    header('Location: requisiciones.php');
    exit;
}

// Verificar permisos
if ($user['rol'] === 'departamento' && $requisicion['usuario_id'] != $user['id']) {
    header('Location: requisiciones.php');
    exit;
}

// Para compras: verificar stock disponible en almacen para cada producto
$stockDisponible = [];
if ($user['rol'] === 'compras' && $requisicion['estado'] === 'pendiente') {
    $db = getConnection();
    foreach ($detalles as $detalle) {
        $nombreProducto = trim($detalle['producto_nombre']);
        
        // Buscar producto en inventario - primero coincidencia exacta, luego similares
        // Buscar en TODOS los almacenes con stock disponible
        $sql = "SELECT i.id, i.codigo, i.nombre, i.cantidad, i.unidad, i.sub_almacen_id,
                       COALESCE(s.nombre, 'Almacen General') as almacen_nombre,
                       CASE 
                           WHEN LOWER(TRIM(i.nombre)) = LOWER(?) THEN 1
                           WHEN LOWER(TRIM(i.nombre)) LIKE LOWER(CONCAT(?, '%')) THEN 2
                           WHEN LOWER(TRIM(i.nombre)) LIKE LOWER(CONCAT('%', ?, '%')) THEN 3
                           ELSE 4
                       END as match_priority
                FROM inventario i
                LEFT JOIN sub_almacenes s ON i.sub_almacen_id = s.id
                WHERE (LOWER(TRIM(i.nombre)) LIKE LOWER(CONCAT('%', ?, '%'))
                       OR LOWER(TRIM(i.nombre)) = LOWER(?))
                AND i.cantidad > 0
                AND (i.activo = 1 OR i.activo IS NULL)
                ORDER BY match_priority ASC, i.cantidad DESC
                LIMIT 10";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssss", $nombreProducto, $nombreProducto, $nombreProducto, $nombreProducto, $nombreProducto);
        $stmt->execute();
        $result = $stmt->get_result();
        $productosEnStock = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        $stockDisponible[$detalle['id']] = $productosEnStock;
    }
}

require_once 'views/ver-requisicion.view.php';
