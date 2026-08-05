<?php
require_once __DIR__ . '/../models/Bitacora.php';

class AuthController {
    private $usuarioModel;
    private $bitacora;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->bitacora = new Bitacora();
    }
    
    public function login($username, $password) {
        $user = $this->usuarioModel->autenticar($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_nombre'] = $user['nombre_completo'];
            $_SESSION['user_rol'] = $user['rol'];
            $_SESSION['user_sub_almacen_id'] = $user['sub_almacen_id'];
            $_SESSION['user_sub_almacen_nombre'] = $user['sub_almacen_nombre'] ?? null;
            
            // Registrar en bitacora
            $this->bitacora->registrar(
                $user['id'],
                $user['nombre_completo'],
                'login',
                'autenticacion',
                'Inicio de sesion exitoso'
            );
            
            return true;
        }
        
        // Registrar intento fallido
        $this->bitacora->registrar(
            null,
            $username,
            'login_fallido',
            'autenticacion',
            'Intento de inicio de sesion fallido para usuario: ' . $username
        );
        
        return false;
    }
    
    public function logout() {
        // Registrar en bitacora antes de destruir la sesion
        if (isset($_SESSION['user_id'])) {
            $this->bitacora->registrar(
                $_SESSION['user_id'],
                $_SESSION['user_nombre'] ?? 'Usuario',
                'logout',
                'autenticacion',
                'Cierre de sesion'
            );
        }
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->usuarioModel->obtenerPorId($_SESSION['user_id']);
    }
    
    public function checkPermission($required_role = null) {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        if ($required_role && $_SESSION['user_rol'] !== 'admin' && $_SESSION['user_rol'] !== $required_role) {
            header('Location: index.php?error=no_permission');
            exit();
        }
    }
}
?>
