<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

if ($user && isset($user['sub_almacen_nombre'])) {
    $_SESSION['user_sub_almacen_nombre'] = $user['sub_almacen_nombre'];
}
if ($user && isset($user['sub_almacen_id'])) {
    $_SESSION['user_sub_almacen_id'] = $user['sub_almacen_id'];
}

$dashboardController = new DashboardController();

$sub_almacen_filter = null;

if ($user['rol'] === 'compras') {
    $sub_almacen_filter = 100;
} elseif (in_array($user['rol'], ['gerencia', 'gerencia_general'])) {
    // Para gerencia, pueden ver otros almacenes pero por defecto ven el general
    $sub_almacen_filter = isset($_GET['sub_almacen']) ? intval($_GET['sub_almacen']) : 100;
} elseif ($user['rol'] === 'admin' && isset($_GET['sub_almacen'])) {
    $sub_almacen_filter = $_GET['sub_almacen'];
}

$data = $dashboardController->index($user, $sub_almacen_filter);

require 'views/dashboard.view.php';
?>
