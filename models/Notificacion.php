<?php
class Notificacion {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function crear($usuario_id, $tipo, $titulo, $mensaje, $requisicion_id = null) {
        $sql = "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, requisicion_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssi", $usuario_id, $tipo, $titulo, $mensaje, $requisicion_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function obtenerPorUsuario($usuario_id, $solo_no_leidas = false) {
        $sql = "SELECT n.*, r.folio as requisicion_folio 
                FROM notificaciones n 
                LEFT JOIN requisiciones r ON n.requisicion_id = r.id 
                WHERE n.usuario_id = ?";
        
        if ($solo_no_leidas) {
            $sql .= " AND n.leida = 0";
        }
        
        $sql .= " ORDER BY n.created_at DESC LIMIT 50";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notificaciones = [];
        while($row = $result->fetch_assoc()) {
            $notificaciones[] = $row;
        }
        
        $stmt->close();
        return $notificaciones;
    }
    
    public function contarNoLeidas($usuario_id) {
        $sql = "SELECT COUNT(*) as total FROM notificaciones WHERE usuario_id = ? AND leida = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = $row['total'];
        $stmt->close();
        return $total;
    }
    
    public function marcarLeida($notificacion_id) {
        $sql = "UPDATE notificaciones SET leida = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $notificacion_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function eliminar($notificacion_id) {
        $sql = "DELETE FROM notificaciones WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $notificacion_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function notificarRol($rol, $titulo, $mensaje, $tipo, $requisicion_id = null) {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->obtenerPorRol($rol);
        
        foreach($usuarios as $usuario) {
            $this->crear($usuario['id'], $tipo, $titulo, $mensaje, $requisicion_id);
        }
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
