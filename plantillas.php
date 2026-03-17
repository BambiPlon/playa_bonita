<?php
require_once 'init.php';

// Verificar sesion
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$usuarioData = [
    'id' => $_SESSION['user_id'],
    'rol' => $_SESSION['user_rol'],
    'nombre' => $_SESSION['user_nombre']
];

$plantillaModel = new Plantilla();

// Manejar eliminacion
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $eliminado = $plantillaModel->eliminar(intval($_GET['eliminar']), $usuarioData['id']);
    if ($eliminado) {
        $_SESSION['mensaje'] = 'Plantilla eliminada correctamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'No se pudo eliminar la plantilla.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    header('Location: plantillas.php');
    exit;
}

$plantillas = $plantillaModel->obtenerPorUsuario($usuarioData['id']);

// Mensajes flash
$mensaje = '';
$tipo_mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
}

require 'includes/header.php';
require 'views/plantillas.view.php';
require 'includes/footer.php';
