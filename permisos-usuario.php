<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'models/Permiso.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$usuario_id = intval($_GET['id'] ?? 0);
if (!$usuario_id) {
    header('Location: usuarios.php');
    exit;
}

$usuarioModel = new Usuario();
$usuario = $usuarioModel->obtenerPorId($usuario_id);

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

$modulos_disponibles = [
    'dashboard' => ['nombre' => 'Dashboard', 'icono' => 'fa-home'],
    'requisiciones' => ['nombre' => 'Ver Requisiciones', 'icono' => 'fa-file-alt'],
    'nueva_requisicion' => ['nombre' => 'Crear Requisición', 'icono' => 'fa-plus-circle'],
    'salidas' => ['nombre' => 'Salidas de Almacén', 'icono' => 'fa-box-open'],
    'proveedores' => ['nombre' => 'Proveedores', 'icono' => 'fa-truck'],
    'notificaciones' => ['nombre' => 'Notificaciones', 'icono' => 'fa-bell'],
    'editar_productos' => ['nombre' => 'Editar Productos', 'icono' => 'fa-edit']
];

if ($usuario['sub_almacen_id'] || $usuario['rol'] === 'compras') {
    $modulos_disponibles['agregar_producto'] = ['nombre' => 'Agregar a Inventario', 'icono' => 'fa-plus-square'];
}

// Obtener permisos actuales usando el nuevo modelo de Permiso
$permisoModel = new Permiso();
$permisos_actuales = $permisoModel->obtenerPermisos($usuario_id);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modulos_seleccionados = $_POST['modulos'] ?? [];
    
    // Eliminar todos los permisos actuales
    $conn = getConnection();
    $sql_delete = "DELETE FROM permisos WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    
    // Agregar los nuevos permisos
    $success = true;
    foreach ($modulos_seleccionados as $modulo) {
        if (!$permisoModel->agregarPermiso($usuario_id, $modulo)) {
            $success = false;
            break;
        }
    }
    
    if ($success) {
        $_SESSION['mensaje'] = "Permisos actualizados correctamente";
        $_SESSION['tipo_mensaje'] = "success";
        header('Location: usuarios.php');
        exit;
    } else {
        $error = "Error al actualizar los permisos";
    }
}

require 'includes/header.php';
require 'views/permisos-usuario.view.php';
require 'includes/footer.php';
?>
