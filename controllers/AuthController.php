<?php
class AuthController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
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
            return true;
        }
        
        return false;
    }
    
    public function logout() {
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
