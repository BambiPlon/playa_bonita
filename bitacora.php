<?php
require_once 'init.php';
require_once 'models/Bitacora.php';
require_once 'models/Usuario.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = [
    'id' => $_SESSION['user_id'],
    'nombre_completo' => $_SESSION['user_nombre'] ?? 'Usuario',
    'rol' => $_SESSION['user_rol'] ?? ''
];

// Solo admin puede ver la bitácora
if ($user['rol'] !== 'admin') {
    $_SESSION['mensaje'] = 'No tienes permisos para acceder a esta sección';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

$bitacoraModel = new Bitacora();
$usuarioModel = new Usuario();

// Obtener filtros
$filtros = [
    'accion' => $_GET['accion'] ?? null,
    'modulo' => $_GET['modulo'] ?? null,
    'usuario_id' => $_GET['usuario'] ?? null,
    'fecha_desde' => $_GET['fecha_desde'] ?? null,
    'fecha_hasta' => $_GET['fecha_hasta'] ?? null,
    'busqueda' => $_GET['busqueda'] ?? null,
    'limite' => $_GET['limite'] ?? 100
];

// Limpiar filtros vacíos
$filtros = array_filter($filtros);
if (!isset($filtros['limite'])) {
    $filtros['limite'] = 100;
}

// Obtener datos
$registros = $bitacoraModel->obtenerRegistros($filtros);
$estadisticas = $bitacoraModel->obtenerEstadisticas();
$acciones = $bitacoraModel->obtenerAcciones();
$modulos = $bitacoraModel->obtenerModulos();
$usuarios_bitacora = $bitacoraModel->obtenerUsuariosBitacora();

$pageTitle = 'Bitacora del Sistema';
include 'includes/header.php';
include 'views/bitacora.view.php';
include 'includes/footer.php';
