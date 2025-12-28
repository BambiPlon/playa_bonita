<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$usuario_id = intval($_GET['id'] ?? 0);
if (!$usuario_id) {
    header('Location: usuarios.php');
    exit;
}

$usuarioModel = new Usuario();
$usuario = $usuarioModel->obtenerPorId($usuario_id);

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

// Módulos disponibles
$modulos_disponibles = [
    'dashboard' => ['nombre' => 'Dashboard', 'icono' => 'fa-home'],
    'requisiciones' => ['nombre' => 'Ver Requisiciones', 'icono' => 'fa-file-alt'],
    'nueva_requisicion' => ['nombre' => 'Crear Requisición', 'icono' => 'fa-plus-circle'],
    'salidas' => ['nombre' => 'Salidas de Almacén', 'icono' => 'fa-box-open'],
    'proveedores' => ['nombre' => 'Proveedores', 'icono' => 'fa-truck'],
    'notificaciones' => ['nombre' => 'Notificaciones', 'icono' => 'fa-bell']
];

if ($usuario['sub_almacen_id'] || $usuario['rol'] === 'compras') {
    $modulos_disponibles['agregar_producto'] = ['nombre' => 'Agregar a Inventario', 'icono' => 'fa-plus-square'];
}

// Obtener permisos actuales
$permisos_actuales = $usuarioModel->obtenerPermisos($usuario_id);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulos_seleccionados = $_POST['modulos'] ?? [];
    
    if ($usuarioModel->actualizarPermisos($usuario_id, $modulos_seleccionados)) {
        $_SESSION['success'] = "Permisos actualizados correctamente";
        header('Location: usuarios.php');
        exit;
    } else {
        $error = "Error al actualizar los permisos";
    }
}

require 'includes/header.php';
require 'views/permisos-usuario.view.php';
require 'includes/footer.php';
?>
