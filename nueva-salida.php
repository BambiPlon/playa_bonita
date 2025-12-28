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
    $datos = [
        'usuario_id' => $user['id'],
        'sub_almacen_id' => $user['rol'] === 'admin' || $user['rol'] === 'compras' ? $_POST['sub_almacen_id'] : $user['sub_almacen_id'],
        'producto_id' => $_POST['producto_id'],
        'cantidad' => $_POST['cantidad'],
        'motivo' => $_POST['motivo'],
        'destino' => $_POST['destino'],
        'fecha_salida' => $_POST['fecha_salida']
    ];
    
    $folio = $salidaController->crear($datos);
    
    if ($folio) {
        $mensaje = "Salida registrada exitosamente. Folio: $folio";
        $tipo_mensaje = 'success';
    } else {
        $mensaje = "Error al registrar la salida";
        $tipo_mensaje = 'danger';
    }
}

// Obtener datos para el formulario
if ($user['rol'] === 'admin' || $user['rol'] === 'compras') {
    $productos = $salidaController->obtenerProductos();
    $sub_almacenes = $salidaController->obtenerSubAlmacenes();
} else {
    $productos = $salidaController->obtenerProductos($user['sub_almacen_id']);
}

require 'views/nueva-salida.view.php';
