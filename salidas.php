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

// Obtener salidas según el rol
if ($user['rol'] === 'admin' || $user['rol'] === 'compras') {
    $salidas = $salidaController->obtenerSalidas($user['id']);
} else {
    $salidas = $salidaController->obtenerSalidas($user['id'], $user['sub_almacen_id']);
}

require 'views/salidas.view.php';
