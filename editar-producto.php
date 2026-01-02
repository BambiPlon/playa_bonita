<?php
session_start();
require_once 'config/database.php';
require_once 'models/Producto.php';
require_once 'models/SubAlmacen.php';
require_once 'models/Usuario.php';
require_once 'models/Permiso.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$usuarioModel = new Usuario();
$usuarioDB = $usuarioModel->obtenerPorId($_SESSION['user_id']);

if (!$usuarioDB) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$usuarioData = [
    'id' => $usuarioDB['id'],
    'nombre' => $usuarioDB['nombre_completo'],
    'rol' => $usuarioDB['rol'],
    'sub_almacen_id' => $usuarioDB['sub_almacen_id'],
    'sub_almacen_nombre' => $usuarioDB['sub_almacen_nombre']
];

$permisoModel = new Permiso();
$tiene_permiso = false;

// Admins y compras siempre tienen permiso
if (in_array($usuarioData['rol'], ['admin', 'compras'])) {
    $tiene_permiso = true;
} else {
    // Verificar si el usuario tiene el permiso específico de editar productos
    $tiene_permiso = $permisoModel->tienePermiso($usuarioData['id'], 'editar_productos');
}

if (!$tiene_permiso) {
    $_SESSION['mensaje'] = 'No tienes permisos para editar productos.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

$producto_id = $_GET['id'] ?? null;

if (!$producto_id) {
    $_SESSION['mensaje'] = 'ID de producto no especificado.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

// Obtener datos del producto
$productoModel = new Producto();
$producto = $productoModel->obtenerPorId($producto_id);

if (!$producto) {
    $_SESSION['mensaje'] = 'Producto no encontrado.';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: index.php');
    exit;
}

// Obtener lista de sub-almacenes
$subAlmacenModel = new SubAlmacen(getConnection());
$sub_almacenes = $subAlmacenModel->obtenerTodos();

$mensaje = '';
$tipo_mensaje = 'success';
$mensaje_error = '';
$mensaje_exito = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim($_POST['nombre']),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'cantidad' => intval($_POST['cantidad']),
        'unidad' => trim($_POST['unidad']),
        'precio_unitario' => floatval($_POST['precio_unitario']),
        'stock_minimo' => intval($_POST['stock_minimo']),
        'sub_almacen_id' => !empty($_POST['sub_almacen_id']) ? intval($_POST['sub_almacen_id']) : null
    ];
    
    // Validaciones
    if (empty($datos['nombre']) || empty($datos['unidad']) || $datos['precio_unitario'] < 0 || $datos['cantidad'] < 0) {
        $mensaje_error = 'Por favor completa todos los campos requeridos correctamente.';
    } else {
        $resultado = $productoModel->actualizar($producto_id, $datos);
        
        if ($resultado) {
            $_SESSION['mensaje'] = 'Producto actualizado exitosamente.';
            $_SESSION['tipo_mensaje'] = 'success';
            header('Location: index.php');
            exit;
        } else {
            $mensaje_error = 'Error al actualizar el producto. Por favor intenta nuevamente.';
        }
    }
}

$pageTitle = 'Editar Producto';
require_once 'views/editar-producto.view.php';
