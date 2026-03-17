<?php
require_once 'init.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$usuarioData = [
    'id' => $_SESSION['user_id'],
    'rol' => $_SESSION['user_rol'],
    'nombre' => $_SESSION['user_nombre']
];

// Obtener productos del inventario para el datalist
$conn = getConnection();
$productos_inventario = [];
$sql = "SELECT p.id, p.nombre, sa.nombre as sub_almacen_nombre 
        FROM inventario p 
        LEFT JOIN sub_almacenes sa ON p.sub_almacen_id = sa.id
        ORDER BY p.nombre";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productos_inventario[] = $row;
    }
}

// Obtener unidades
$unidades_lista = [];
$query_unidades = $conn->query("SELECT nombre FROM unidades WHERE activo = 1 ORDER BY nombre");
if ($query_unidades) {
    while ($row = $query_unidades->fetch_assoc()) {
        $unidades_lista[] = $row['nombre'];
    }
}
if (empty($unidades_lista)) {
    $unidades_lista = ['pieza', 'unidad', 'caja', 'paquete', 'bolsa', 'rollo', 'metro', 'litro', 'kilogramo', 'gramo', 'juego', 'par', 'docena', 'cubeta', 'bote', 'botella', 'servicio'];
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_plantilla'] ?? '');
    $descripcion = trim($_POST['descripcion_plantilla'] ?? '');
    $productos_ids = $_POST['productos'] ?? [];
    $productos_nombres = $_POST['productos_nombre_custom'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    $unidades = $_POST['unidades'] ?? [];
    
    if (empty($nombre)) {
        $mensaje = 'El nombre de la plantilla es obligatorio.';
        $tipo_mensaje = 'danger';
    } elseif (empty($productos_ids)) {
        $mensaje = 'Debes agregar al menos un producto a la plantilla.';
        $tipo_mensaje = 'danger';
    } else {
        $productos = [];
        for ($i = 0; $i < count($productos_ids); $i++) {
            if (!empty($productos_ids[$i]) && !empty($cantidades[$i])) {
                $productos[] = [
                    'producto_id' => $productos_ids[$i],
                    'nombre_custom' => $productos_nombres[$i] ?? '',
                    'cantidad' => $cantidades[$i],
                    'unidad' => $unidades[$i] ?? ''
                ];
            }
        }
        
        if (empty($productos)) {
            $mensaje = 'Debes agregar al menos un producto valido.';
            $tipo_mensaje = 'danger';
        } else {
            $plantillaModel = new Plantilla();
            $result = $plantillaModel->crear($usuarioData['id'], $nombre, $descripcion, $productos);
            
            if ($result) {
                $_SESSION['mensaje'] = 'Plantilla creada correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: plantillas.php');
                exit;
            } else {
                $mensaje = 'Error al crear la plantilla.';
                $tipo_mensaje = 'danger';
            }
        }
    }
}

$modoEdicion = false;
$plantilla = null;
$plantilla_productos = [];

require 'includes/header.php';
require 'views/plantilla-form.view.php';
require 'includes/footer.php';
