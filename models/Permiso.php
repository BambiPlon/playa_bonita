<?php
class Permiso {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Verificar si un usuario tiene un permiso específico
     */
    public function tienePermiso($usuario_id, $modulo) {
        $sql = "SELECT COUNT(*) as count FROM permisos WHERE usuario_id = ? AND modulo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $modulo);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['count'] > 0;
    }
    
    /**
     * Agregar un permiso a un usuario
     */
    public function agregarPermiso($usuario_id, $modulo) {
        $sql = "INSERT INTO permisos (usuario_id, modulo) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE usuario_id = usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $modulo);
        return $stmt->execute();
    }
    
    /**
     * Eliminar un permiso de un usuario
     */
    public function eliminarPermiso($usuario_id, $modulo) {
        $sql = "DELETE FROM permisos WHERE usuario_id = ? AND modulo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $modulo);
        return $stmt->execute();
    }
    
    /**
     * Obtener todos los permisos de un usuario
     */
    public function obtenerPermisos($usuario_id) {
        $sql = "SELECT modulo FROM permisos WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $permisos = [];
        while ($row = $resultado->fetch_assoc()) {
            $permisos[] = $row['modulo'];
        }
        
        return $permisos;
    }
}
