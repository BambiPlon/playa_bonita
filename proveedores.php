<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

// Solo compras puede gestionar proveedores
if ($user['rol'] !== 'compras' && $user['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$proveedorModel = new Proveedor();
$proveedores = $proveedorModel->obtenerTodos();

$pageTitle = "Proveedores";
require_once 'includes/header.php';
require_once 'views/proveedores.view.php';
require_once 'includes/footer.php';
