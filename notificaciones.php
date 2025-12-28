<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$notificacionController = new NotificacionController();

$notificaciones = $notificacionController->listar($user['id']);

if (isset($_GET['marcar_leida'])) {
    $notif_id = intval($_GET['marcar_leida']);
    $notificacionController->marcarLeida($notif_id);
    header('Location: notificaciones.php');
    exit();
}

require 'views/notificaciones.view.php';
?>
