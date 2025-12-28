<?php
require_once __DIR__ . '/../models/Usuario.php';

$current_page = basename($_SERVER['PHP_SELF']);

$user = [
    'id' => $_SESSION['user_id'] ?? 0,
    'rol' => $_SESSION['user_rol'] ?? 'usuario',
    'nombre' => $_SESSION['user_nombre'] ?? 'Usuario'
];

// Obtener permisos del usuario
$usuarioModel = new Usuario();
$permisos_usuario = [];

$roles_privilegiados = ['admin', 'gerencia', 'gerencia_general', 'compras'];

if (!in_array($user['rol'], $roles_privilegiados)) {
    $permisos_usuario = $usuarioModel->obtenerPermisos($user['id']);
}

// Función helper para verificar si el usuario tiene permiso
if (!function_exists('tienePermiso')) {
    function tienePermiso($modulo) {
        global $user, $permisos_usuario, $roles_privilegiados;
        
        if (in_array($user['rol'], $roles_privilegiados)) {
            return true;
        }
        
        // Verificar si el usuario tiene el permiso específico
        return in_array($modulo, $permisos_usuario);
    }
}
?>
<!-- Sidebar colapsable con navegación -->
<aside class="sidebar" id="sidebar">
    <nav class="nav-menu">
        <?php if (tienePermiso('dashboard')): ?>
        <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-home nav-icon"></i>
            <span>Dashboard</span>
        </a>
        <?php endif; ?>
        
        <?php if (tienePermiso('requisiciones')): ?>
        <a href="requisiciones.php" class="nav-item <?php echo ($current_page == 'requisiciones.php') ? 'active' : ''; ?>">
            <i class="fas fa-file-alt nav-icon"></i>
            <span>Requisiciones</span>
        </a>
        <?php endif; ?>
        
        <?php if (tienePermiso('nueva_requisicion')): ?>
        <a href="nueva-requisicion.php" class="nav-item <?php echo ($current_page == 'nueva-requisicion.php') ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle nav-icon"></i>
            <span>Nueva Requisición</span>
        </a>
        <?php endif; ?>
        
        <?php if (tienePermiso('salidas')): ?>
        <a href="salidas.php" class="nav-item <?php echo ($current_page == 'salidas.php' || $current_page == 'nueva-salida.php') ? 'active' : ''; ?>">
            <i class="fas fa-box-open nav-icon"></i>
            <span>Salidas de Almacén</span>
        </a>
        <?php endif; ?>
        
        <?php if (tienePermiso('agregar_producto')): ?>
        <a href="agregar-producto.php" class="nav-item <?php echo ($current_page == 'agregar-producto.php') ? 'active' : ''; ?>">
            <i class="fas fa-plus-square nav-icon"></i>
            <span>Agregar Producto</span>
        </a>
        <?php endif; ?>
        
        <?php if (tienePermiso('proveedores')): ?>
        <a href="proveedores.php" class="nav-item <?php echo ($current_page == 'proveedores.php' || $current_page == 'agregar-proveedor.php') ? 'active' : ''; ?>">
            <i class="fas fa-truck nav-icon"></i>
            <span>Proveedores</span>
        </a>
        <?php endif; ?>
        
        <!-- Solo admin puede acceder a usuarios -->
        <?php if ($user['rol'] === 'admin'): ?>
        <a href="usuarios.php" class="nav-item <?php echo ($current_page == 'usuarios.php' || $current_page == 'agregar-usuario.php' || $current_page == 'permisos-usuario.php') ? 'active' : ''; ?>">
            <i class="fas fa-users nav-icon"></i>
            <span>Usuarios</span>
        </a>
        <?php endif; ?>
        
        <?php if (tienePermiso('notificaciones')): ?>
        <a href="notificaciones.php" class="nav-item <?php echo ($current_page == 'notificaciones.php') ? 'active' : ''; ?>">
            <i class="fas fa-bell nav-icon"></i>
            <span>Notificaciones</span>
            <?php if ($no_leidas > 0): ?>
                <span class="badge badge-danger" style="margin-left: auto; font-size: 11px;">
                    <?php echo $no_leidas; ?>
                </span>
            <?php endif; ?>
        </a>
        <?php endif; ?>
    </nav>
</aside>
