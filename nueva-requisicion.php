<?php
require_once 'init.php';
require_once 'models/Bitacora.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$bitacora = new Bitacora();

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

// Cargar plantillas del usuario
$plantillaModel = new Plantilla();
$plantillas_usuario = $plantillaModel->obtenerPorUsuario($user['id']);

// Si viene con plantilla_id, pre-cargar productos de la plantilla
$plantilla_precarga = null;
if (isset($_GET['plantilla_id']) && intval($_GET['plantilla_id']) > 0) {
    $pid = intval($_GET['plantilla_id']);
    $plantilla_precarga = $plantillaModel->obtenerPorId($pid, $user['id']);
    if ($plantilla_precarga) {
        $plantilla_precarga['productos'] = $plantillaModel->obtenerProductos($pid);
    }
}

$notificacionModel = new Notificacion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_almacen_id = isset($_POST['sub_almacen_id']) ? intval($_POST['sub_almacen_id']) : 0;
    
    $roles_sin_subalmacen = ['admin', 'compras', 'gerencia', 'gerencia_general'];
    
    if (!in_array($user['rol'], $roles_sin_subalmacen) && $sub_almacen_id <= 0) {
        $mensaje = "Error: No se puede crear la requisición. El usuario no tiene un sub-almacén asignado. Por favor contacte al administrador.";
        $tipo_mensaje = "error";
    } else {
        $tipoRequisicion = $_POST['tipo_requisicion'] ?? 'producto';
        
        $datos = [
            'sub_almacen_id' => $sub_almacen_id > 0 ? $sub_almacen_id : null,
            'usuario_id' => $user['id'],
            'solicitante' => $_POST['solicitante'],
            'fecha_solicitud' => $_POST['fecha_solicitud'],
            'observaciones' => $_POST['justificacion'] ?? '',
            'tipo_requisicion' => $tipoRequisicion,
            'productos' => [],
            'cantidades' => [],
            'unidades' => [],
            'productos_nombre' => []
        ];
        
        if ($tipoRequisicion === 'servicio') {
            // Procesar servicios
            $servicios_descripcion = $_POST['servicios_descripcion'] ?? [];
            $servicios_tipo = $_POST['servicios_tipo'] ?? [];
            $servicios_ubicacion = $_POST['servicios_ubicacion'] ?? [];
            $servicios_fecha = $_POST['servicios_fecha'] ?? [];
            $servicios_prioridad = $_POST['servicios_prioridad'] ?? [];
            
            foreach ($servicios_descripcion as $index => $descripcion) {
                if (!empty($descripcion)) {
                    $datos['productos'][] = 'servicio';
                    $datos['cantidades'][] = 1;
                    $datos['unidades'][] = 'servicio';
                    
                    // Crear nombre del servicio con información adicional
                    $tipoServ = $servicios_tipo[$index] ?? 'otro';
                    $ubicacion = $servicios_ubicacion[$index] ?? '';
                    $fecha = $servicios_fecha[$index] ?? '';
                    $prioridad = $servicios_prioridad[$index] ?? 'normal';
                    
                    $nombreServicio = "[SERVICIO - " . strtoupper($tipoServ) . "] " . $descripcion;
                    if (!empty($ubicacion)) {
                        $nombreServicio .= " | Ubicación: " . $ubicacion;
                    }
                    if (!empty($fecha)) {
                        $nombreServicio .= " | Fecha req: " . $fecha;
                    }
                    if ($prioridad !== 'normal') {
                        $nombreServicio .= " | Prioridad: " . strtoupper($prioridad);
                    }
                    
                    $datos['productos_nombre'][$index] = $nombreServicio;
                }
            }
        } else {
            // Procesar productos (lógica existente)
            $datos['productos'] = $_POST['productos'] ?? [];
            $datos['cantidades'] = $_POST['cantidades'] ?? [];
            $datos['unidades'] = $_POST['unidades'] ?? [];
            $datos['productos_nombre'] = $_POST['productos_nombre_custom'] ?? [];
        }
        
        $resultado = $requisicionController->crear($datos, $user);
        
        if ($resultado['success']) {
            $tipoTexto = $tipoRequisicion === 'servicio' ? 'de servicio' : 'de producto';
            $mensaje = "Requisición $tipoTexto creada exitosamente con folio: " . $resultado['folio'] . ". Se ha notificado al departamento de compras.";
            $tipo_mensaje = "success";
            
            // Registrar en bitacora
            $bitacora->registrar(
                $user['id'],
                $user['nombre_completo'],
                'crear',
                'requisiciones',
                'Nueva requisicion ' . $tipoTexto . ' creada. Folio: ' . $resultado['folio'] . '. Productos: ' . count($datos['productos']),
                null,
                ['folio' => $resultado['folio'], 'tipo' => $tipoRequisicion, 'productos' => count($datos['productos'])]
            );
        } else {
            $mensaje = "Error al crear la requisición: " . ($resultado['error'] ?? 'Error desconocido');
            $tipo_mensaje = "error";
        }
    }
}

require 'views/nueva-requisicion.view.php';
?>
