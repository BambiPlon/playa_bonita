<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$requisicionController = new RequisicionController();

$estado_filter = isset($_GET['estado']) ? $_GET['estado'] : null;
$mes_filter = isset($_GET['mes']) ? $_GET['mes'] : null;
$anio_filter = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$requisiciones = $requisicionController->listar($user, $estado_filter, $mes_filter, $anio_filter);

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    $roles_privilegiados = ['admin', 'compras', 'gerencia', 'gerencia_general'];
    
    if (!in_array($user['rol'], $roles_privilegiados)) {
        $mensaje = "No tiene permisos para cambiar estados";
        $tipo_mensaje = "error";
    } else {
        $requisicion_id = intval($_POST['requisicion_id']);
        $nuevo_estado = $_POST['nuevo_estado'];
        
        if ($requisicionController->cambiarEstado($requisicion_id, $nuevo_estado)) {
            $mensaje = "Estado actualizado correctamente";
            $tipo_mensaje = "success";
            $requisiciones = $requisicionController->listar($user, $estado_filter, $mes_filter, $anio_filter);
        } else {
            $mensaje = "Error al actualizar el estado";
            $tipo_mensaje = "error";
        }
    }
}

require_once 'views/requisiciones.view.php';
?>

