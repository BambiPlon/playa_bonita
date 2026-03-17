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

$plantilla_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$plantilla_id) {
    header('Location: plantillas.php');
    exit;
}

$plantillaModel = new Plantilla();
$plantilla = $plantillaModel->obtenerPorId($plantilla_id, $usuarioData['id']);

if (!$plantilla) {
    $_SESSION['mensaje'] = 'Plantilla no encontrada.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: plantillas.php');
    exit;
}

$plantilla_productos = $plantillaModel->obtenerProductos($plantilla_id);

// Obtener productos del inventario
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
    $unidades_post = $_POST['unidades'] ?? [];
    
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
                    'unidad' => $unidades_post[$i] ?? ''
                ];
            }
        }
        
        if (empty($productos)) {
            $mensaje = 'Debes agregar al menos un producto valido.';
            $tipo_mensaje = 'danger';
        } else {
            $result = $plantillaModel->actualizar($plantilla_id, $usuarioData['id'], $nombre, $descripcion, $productos);
            
            if ($result) {
                $_SESSION['mensaje'] = 'Plantilla actualizada correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
                header('Location: plantillas.php');
                exit;
            } else {
                $mensaje = 'Error al actualizar la plantilla.';
                $tipo_mensaje = 'danger';
            }
        }
    }
}

$modoEdicion = true;

require 'includes/header.php';
require 'views/plantilla-form.view.php';
require 'includes/footer.php';
