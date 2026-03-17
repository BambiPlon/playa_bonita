<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();
$requisicionController = new RequisicionController();

$estado_filter = isset($_GET['estado']) ? $_GET['estado'] : null;
$mes_filter = isset($_GET['mes']) ? $_GET['mes'] : null;
$anio_filter = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$mostrar_ocultas = isset($_GET['mostrar_ocultas']) && $_GET['mostrar_ocultas'] == '1' ? true : false;

$requisiciones = $requisicionController->listar($user, $estado_filter, $mes_filter, $anio_filter, $mostrar_ocultas);

require_once 'views/listado-requisiciones.view.php';
?>
