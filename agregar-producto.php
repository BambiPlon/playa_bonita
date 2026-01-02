<?php
session_start();
require_once 'config/database.php';
require_once 'models/Producto.php';
require_once 'models/SubAlmacen.php';
require_once 'models/Usuario.php';

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

$_SESSION['user_nombre'] = $usuarioData['nombre'];
$_SESSION['user_rol'] = $usuarioData['rol'];
$_SESSION['user_sub_almacen_id'] = $usuarioData['sub_almacen_id'];
$_SESSION['user_sub_almacen_nombre'] = $usuarioData['sub_almacen_nombre'];

$roles_privilegiados = ['admin', 'gerencia', 'gerencia_general'];
$puede_seleccionar = in_array($usuarioData['rol'], $roles_privilegiados);

if ($usuarioData['rol'] === 'departamento' && empty($usuarioData['sub_almacen_id'])) {
    $_SESSION['mensaje'] = "Tu cuenta no tiene un sub-almacén asignado. Por favor contacta al administrador para que te asigne uno.";
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: index.php');
    exit;
}

$mensaje = '';
$tipo_mensaje = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_almacen_id = isset($_POST['sub_almacen_id']) ? intval($_POST['sub_almacen_id']) : null;
    
    $puede_agregar = false;
    $mensaje_error = '';
    
    if ($usuarioData['rol'] === 'compras') {
        $puede_agregar = true;
        $sub_almacen_id = 100;
    } elseif ($puede_seleccionar) {
        if ($sub_almacen_id) {
            $puede_agregar = true;
        } else {
            $mensaje_error = 'Debes seleccionar un sub-almacén.';
        }
    } else {
        if ($sub_almacen_id == $usuarioData['sub_almacen_id']) {
            $puede_agregar = true;
        } else {
            $mensaje_error = 'Solo puedes agregar productos a tu propio sub-almacén.';
        }
    }
    
    if ($puede_agregar) {
        $codigo = trim($_POST['codigo']);
        $cantidad_nueva = intval($_POST['cantidad']);
        
        $productoModel = new Producto();
        
        $producto_existente = $productoModel->buscarPorCodigo($codigo, $sub_almacen_id);
        
        if ($producto_existente) {
            $resultado = $productoModel->incrementarCantidad($producto_existente['id'], $cantidad_nueva);
            
            if ($resultado['success']) {
                $mensaje = "Producto actualizado exitosamente. Se agregaron {$cantidad_nueva} unidades. Nueva cantidad: {$resultado['nueva_cantidad']}.";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar la cantidad del producto.';
                $tipo_mensaje = 'danger';
            }
        } else {
            $datos = [
                'codigo' => $codigo,
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'unidad' => trim($_POST['unidad']),
                'cantidad' => $cantidad_nueva,
                'precio_unitario' => floatval($_POST['precio_unitario'] ?? 0),
                'stock_minimo' => intval($_POST['stock_minimo'] ?? 10),
                'sub_almacen_id' => $sub_almacen_id
            ];
            
            $resultado = $productoModel->crear($datos);
            
            if ($resultado === true) {
                $mensaje = 'Producto agregado exitosamente al inventario.';
                $tipo_mensaje = 'success';
            } elseif (is_array($resultado) && isset($resultado['error'])) {
                if ($resultado['error'] === 'duplicate') {
                    $mensaje = 'El producto ya existe en este sub-almacén. Usa el mismo código para agregar más unidades.';
                } else {
                    $mensaje = $resultado['message'];
                }
                $tipo_mensaje = 'danger';
            } else {
                $mensaje = 'Error al agregar el producto. Por favor intenta nuevamente.';
                $tipo_mensaje = 'danger';
            }
        }
    } else {
        $mensaje = $mensaje_error;
        $tipo_mensaje = 'danger';
    }
}

$sub_almacenes = [];
if ($puede_seleccionar) {
    $subAlmacenModel = new SubAlmacen(getConnection());
    $sub_almacenes = $subAlmacenModel->obtenerTodos();
}

$pageTitle = 'Agregar Producto al Inventario';
require_once 'views/agregar-producto.view.php';
