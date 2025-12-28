<?php
require_once 'init.php';

$authController = new AuthController();
$authController->checkPermission();

$user = $authController->getCurrentUser();

// Solo compras puede agregar/editar proveedores
if ($user['rol'] !== 'compras' && $user['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$proveedorModel = new Proveedor();

$modo_edicion = false;
$proveedor = null;
if (isset($_GET['id'])) {
    $modo_edicion = true;
    $proveedor = $proveedorModel->obtenerPorId($_GET['id']);
    if (!$proveedor) {
        $_SESSION['mensaje'] = 'Proveedor no encontrado';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: proveedores.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => $_POST['nombre'],
        'contacto' => $_POST['contacto'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'email' => $_POST['email'] ?? '',
        'direccion' => $_POST['direccion'] ?? '',
        'rfc' => $_POST['rfc'] ?? '',
        'activo' => isset($_POST['activo']) ? 1 : 1 // Por defecto activo
    ];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Modo actualización
        $success = $proveedorModel->actualizar($_POST['id'], $datos);
        $mensaje = $success ? 'Proveedor actualizado exitosamente' : 'Error al actualizar proveedor';
    } else {
        // Modo creación
        $proveedor_id = $proveedorModel->crear($datos);
        $success = $proveedor_id ? true : false;
        $mensaje = $success ? 'Proveedor agregado exitosamente' : 'Error al agregar proveedor';
    }
    
    if ($success) {
        // Si es una petición AJAX, devolver JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'proveedor_id' => isset($proveedor_id) ? $proveedor_id : $_POST['id'],
                'proveedor_nombre' => $datos['nombre']
            ]);
            exit;
        }
        
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = 'success';
        header('Location: proveedores.php');
        exit;
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $mensaje]);
            exit;
        }
        
        $_SESSION['mensaje'] = $mensaje;
        $_SESSION['tipo_mensaje'] = 'error';
    }
}

$pageTitle = $modo_edicion ? "Editar Proveedor" : "Agregar Proveedor";
require_once 'includes/header.php';
require_once 'views/agregar-proveedor.view.php';
require_once 'includes/footer.php';
