<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/SalidaController.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['user_username'],
    'nombre' => $_SESSION['user_nombre'],
    'rol' => $_SESSION['user_rol'],
    'sub_almacen_id' => $_SESSION['user_sub_almacen_id']
];

// Verificar que no sea gerencia
if ($user['rol'] === 'gerencia') {
    header('Location: index.php');
    exit;
}

$db = getConnection();
$salidaController = new SalidaController($db);

$mensaje = '';
$tipo_mensaje = 'success';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_almacen_id = $user['rol'] === 'admin' || $user['rol'] === 'compras' ? $_POST['sub_almacen_id'] : $user['sub_almacen_id'];
    
    // Verificar si es salida multiple
    if (isset($_POST['productos_ids']) && is_array($_POST['productos_ids'])) {
        $productos = [];
        foreach ($_POST['productos_ids'] as $i => $prod_id) {
            if (!empty($prod_id) && !empty($_POST['cantidades'][$i])) {
                $productos[] = [
                    'producto_id' => intval($prod_id),
                    'cantidad' => intval($_POST['cantidades'][$i])
                ];
            }
        }
        
        if (!empty($productos)) {
            $datos_base = [
                'usuario_id' => $user['id'],
                'sub_almacen_id' => $sub_almacen_id,
                'motivo' => $_POST['motivo'],
                'destino' => $_POST['destino'],
                'fecha_salida' => $_POST['fecha_salida']
            ];
            
            $resultado = $salidaController->crearMultiple($datos_base, $productos);
            
            if ($resultado['success']) {
                $mensaje = "Salida registrada exitosamente. Folio: {$resultado['folio']} ({$resultado['exitosos']} productos)";
                if (!empty($resultado['errores'])) {
                    $mensaje .= " - Algunos productos no se procesaron: " . implode(', ', $resultado['errores']);
                }
                $tipo_mensaje = 'success';
            } else {
                $mensaje = "Error al registrar la salida: " . implode(', ', $resultado['errores']);
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = "Debes agregar al menos un producto.";
            $tipo_mensaje = 'danger';
        }
    } else {
        // Salida individual (compatibilidad)
        $datos = [
            'usuario_id' => $user['id'],
            'sub_almacen_id' => $sub_almacen_id,
            'producto_id' => $_POST['producto_id'],
            'cantidad' => $_POST['cantidad'],
            'motivo' => $_POST['motivo'],
            'destino' => $_POST['destino'],
            'fecha_salida' => $_POST['fecha_salida']
        ];
        
        $folio = $salidaController->crear($datos);
        
        if ($folio === 'SIN_STOCK') {
            $mensaje = "Error: No hay suficiente stock disponible para realizar esta salida.";
            $tipo_mensaje = 'danger';
        } elseif ($folio) {
            $mensaje = "Salida registrada exitosamente. Folio: $folio";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al registrar la salida";
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener datos para el formulario
if ($user['rol'] === 'admin' || $user['rol'] === 'compras') {
    $productos = $salidaController->obtenerProductos();
    $sub_almacenes = $salidaController->obtenerSubAlmacenes();
    $requisiciones_completadas = $salidaController->obtenerRequisicionesCompletadas();
} else {
    $productos = $salidaController->obtenerProductos($user['sub_almacen_id']);
    $requisiciones_completadas = $salidaController->obtenerRequisicionesCompletadas($user['sub_almacen_id']);
}

require 'views/nueva-salida.view.php';
