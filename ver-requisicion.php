<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$requisicionModel = new Requisicion();
$proveedorModel = new Proveedor();

$requisicion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($requisicion_id === 0) {
    header('Location: requisiciones.php');
    exit;
}

$requisicion = $requisicionModel->obtenerPorId($requisicion_id);
$detalles = $requisicionModel->obtenerDetalles($requisicion_id);

if (!$requisicion) {
    header('Location: requisiciones.php');
    exit;
}

// Verificar permisos
if ($user['rol'] === 'departamento' && $requisicion['usuario_id'] != $user['id']) {
    header('Location: requisiciones.php');
    exit;
}

require_once 'views/ver-requisicion.view.php';
