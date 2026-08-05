<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$requisicionController = new RequisicionController();

$estado_filter = isset($_GET['estado']) ? $_GET['estado'] : null;
$mes_filter = isset($_GET['mes']) ? $_GET['mes'] : null;
$anio_filter = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
$usuario_filter = isset($_GET['usuario']) ? $_GET['usuario'] : null;

$mostrar_ocultas = isset($_GET['mostrar_ocultas']) && $_GET['mostrar_ocultas'] == '1' ? true : false;

// Obtener usuarios para el filtro (solo para roles con permisos)
$usuarios_filtro = [];
if (in_array($user['rol'], ['compras', 'gerencia', 'gerencia_general', 'admin'])) {
    $usuarios_filtro = $requisicionController->obtenerUsuariosConRequisiciones();
}

$requisiciones = $requisicionController->listar($user, $estado_filter, $mes_filter, $anio_filter, $mostrar_ocultas, $usuario_filter);

$mensaje = '';
$tipo_mensaje = '';

// Verificar si hay mensaje en sesión
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
}

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
            $requisiciones = $requisicionController->listar($user, $estado_filter, $mes_filter, $anio_filter, $mostrar_ocultas);
        } else {
            $mensaje = "Error al actualizar el estado";
            $tipo_mensaje = "error";
        }
    }
}

require_once 'views/requisiciones.view.php';
?>
