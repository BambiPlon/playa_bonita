<?php
session_start();

// Cargar configuración
require_once 'config/database.php';

// Cargar modelos
require_once 'models/Usuario.php';
require_once 'models/Producto.php';
require_once 'models/Requisicion.php';
require_once 'models/Notificacion.php';
require_once 'models/SubAlmacen.php';
require_once 'models/Proveedor.php'; // agregando modelo Proveedor

// Cargar controladores
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/RequisicionController.php';
require_once 'controllers/NotificacionController.php';
