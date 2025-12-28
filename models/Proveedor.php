<?php
class Proveedor {
    private $db;
    
    public function __construct() {
        $this->db = getConnection();
    }
    
    public function obtenerTodos($activos_solo = false) {
        $sql = "SELECT * FROM proveedores";
        if ($activos_solo) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre ASC";
        
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function crear($datos) {
        $stmt = $this->db->prepare(
            "INSERT INTO proveedores (nombre, contacto, telefono, email, direccion, rfc) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssss",
            $datos['nombre'],
            $datos['contacto'],
            $datos['telefono'],
            $datos['email'],
            $datos['direccion'],
            $datos['rfc']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
    public function actualizar($id, $datos) {
        $stmt = $this->db->prepare(
            "UPDATE proveedores 
             SET nombre = ?, contacto = ?, telefono = ?, email = ?, direccion = ?, rfc = ?, activo = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            "ssssssii",
            $datos['nombre'],
            $datos['contacto'],
            $datos['telefono'],
            $datos['email'],
            $datos['direccion'],
            $datos['rfc'],
            $datos['activo'],
            $id
        );
        
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        // Soft delete
        $stmt = $this->db->prepare("UPDATE proveedores SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE proveedores SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
}
