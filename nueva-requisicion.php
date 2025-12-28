<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

$mensaje = '';
$tipo_mensaje = '';

if ($user && isset($user['sub_almacen_nombre'])) {
    $_SESSION['user_sub_almacen_nombre'] = $user['sub_almacen_nombre'];
}
if ($user && isset($user['sub_almacen_id'])) {
    $_SESSION['user_sub_almacen_id'] = $user['sub_almacen_id'];
}

if ($user['rol'] === 'solo_lectura') {
    header('Location: index.php?error=no_permission');
    exit();
}

$requisicionController = new RequisicionController();
$datosFormulario = $requisicionController->obtenerDatosFormulario($user);

$notificacionModel = new Notificacion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_almacen_id = isset($_POST['sub_almacen_id']) ? intval($_POST['sub_almacen_id']) : 0;
    
    // Roles que pueden crear requisiciones sin sub-almacén
    $roles_sin_subalmacen = ['admin', 'compras', 'gerencia', 'gerencia_general'];
    
    // Solo validar sub-almacén para roles que lo requieren
    if (!in_array($user['rol'], $roles_sin_subalmacen) && $sub_almacen_id <= 0) {
        $mensaje = "Error: No se puede crear la requisición. El usuario no tiene un sub-almacén asignado. Por favor contacte al administrador.";
        $tipo_mensaje = "error";
    } else {
        // Proceder con la creación
        $datos = [
            'sub_almacen_id' => $sub_almacen_id > 0 ? $sub_almacen_id : null,
            'usuario_id' => $user['id'],
            'solicitante' => $_POST['solicitante'],
            'fecha_solicitud' => $_POST['fecha_solicitud'],
            'observaciones' => $_POST['observaciones'],
            'productos' => $_POST['productos'],
            'cantidades' => $_POST['cantidades'],
            'unidades' => $_POST['unidades'],
            'productos_nombre' => []
        ];
        
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'producto_nombre_') === 0) {
                $index = str_replace('producto_nombre_', '', $key);
                $datos['productos_nombre'][$index] = $value;
            }
        }
        
        $resultado = $requisicionController->crear($datos, $user);
        
        if ($resultado['success']) {
            $mensaje = "Requisición creada exitosamente con folio: " . $resultado['folio'] . ". Se ha notificado al departamento de compras.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al crear la requisición: " . ($resultado['error'] ?? 'Error desconocido');
            $tipo_mensaje = "error";
        }
    }
}

require 'views/nueva-requisicion.view.php';
?>
