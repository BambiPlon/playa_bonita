<?php
session_start();
require_once 'config/database.php';
require_once 'models/Producto.php';
require_once 'models/SubAlmacen.php';
require_once 'models/Usuario.php';
require_once 'models/Bitacora.php';

$bitacora = new Bitacora();

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

// Todos los productos se agregan al almacen general (ID 100)

$mensaje = '';
$tipo_mensaje = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_almacen_id = 100; // Siempre almacen general
    $puede_agregar = true;
    $mensaje_error = '';
    
    if ($puede_agregar) {
        $codigo = trim($_POST['codigo']);
        $cantidad_nueva = intval($_POST['cantidad'] ?? 0);
        
        $productoModel = new Producto();
        $unidad_enviada = !empty(trim($_POST['unidad'])) ? trim($_POST['unidad']) : 'pieza';
        
        $producto_existente = $productoModel->buscarPorCodigo($codigo, $sub_almacen_id);
        
        // Si el producto existe pero la unidad es diferente, crear uno nuevo
        if ($producto_existente && strtolower(trim($producto_existente['unidad'])) !== strtolower($unidad_enviada)) {
            $producto_existente = null; // Forzar creacion de nuevo registro
            $codigo = 'ALM-100-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6)); // Nuevo codigo unico
        }
        
        if ($producto_existente) {
            $resultado = $productoModel->incrementarCantidad($producto_existente['id'], $cantidad_nueva);
            
            if ($resultado['success']) {
                $mensaje = "Producto actualizado exitosamente. Se agregaron {$cantidad_nueva} unidades. Nueva cantidad: {$resultado['nueva_cantidad']}.";
                $tipo_mensaje = 'success';
                
                // Registrar en bitacora
                $bitacora->registrar(
                    $_SESSION['user_id'],
                    $_SESSION['user_nombre'] ?? 'Usuario',
                    'entrada_almacen',
                    'inventario',
                    'Entrada de ' . $cantidad_nueva . ' unidades de ' . $producto_existente['nombre'] . '. Stock: ' . $resultado['nueva_cantidad'],
                    ['cantidad_anterior' => $producto_existente['cantidad']],
                    ['cantidad_agregada' => $cantidad_nueva, 'nueva_cantidad' => $resultado['nueva_cantidad']]
                );
            } else {
                $mensaje = 'Error al actualizar la cantidad del producto.';
                $tipo_mensaje = 'danger';
            }
        } else {
            $datos = [
                'codigo' => $codigo,
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'unidad' => !empty(trim($_POST['unidad'])) ? trim($_POST['unidad']) : 'pieza',
                'cantidad' => $cantidad_nueva,
                'precio_unitario' => floatval($_POST['precio_unitario'] ?? 0),
                'stock_minimo' => intval($_POST['stock_minimo'] ?? 0),
                'sub_almacen_id' => $sub_almacen_id
            ];
            
            $resultado = $productoModel->crear($datos);
            
            if ($resultado === true) {
                $mensaje = 'Producto agregado exitosamente al inventario.';
                $tipo_mensaje = 'success';
                
                // Registrar en bitacora
                $bitacora->registrar(
                    $_SESSION['user_id'],
                    $_SESSION['user_nombre'] ?? 'Usuario',
                    'crear',
                    'inventario',
                    'Nuevo producto creado: ' . $datos['nombre'] . ' (' . $datos['codigo'] . '). Cantidad inicial: ' . $datos['cantidad'],
                    null,
                    $datos
                );
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
