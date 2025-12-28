<?php
class Usuario {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function autenticar($username, $password) {
        $username = $this->conn->real_escape_string($username);
        $password = $this->conn->real_escape_string($password);
        
        $sql = "SELECT u.*, s.nombre as sub_almacen_nombre 
                FROM usuarios u 
                LEFT JOIN sub_almacenes s ON u.sub_almacen_id = s.id 
                WHERE u.username = '$username' AND u.password = '$password' AND u.activo = 1";
        $result = $this->conn->query($sql);
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    public function obtenerPorId($id) {
        $id = intval($id);
        $sql = "SELECT u.*, s.nombre as sub_almacen_nombre 
                FROM usuarios u 
                LEFT JOIN sub_almacenes s ON u.sub_almacen_id = s.id 
                WHERE u.id = $id";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }
    
    public function obtenerPorRol($rol) {
        $sql = "SELECT id FROM usuarios WHERE rol = ? AND activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $rol);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuarios = [];
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        
        $stmt->close();
        return $usuarios;
    }
    
    public function obtenerTodos() {
        $sql = "SELECT u.*, s.nombre as sub_almacen_nombre 
                FROM usuarios u 
                LEFT JOIN sub_almacenes s ON u.sub_almacen_id = s.id 
                ORDER BY u.created_at DESC";
        $result = $this->conn->query($sql);
        
        $usuarios = [];
        while($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        
        return $usuarios;
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO usuarios (username, password, nombre_completo, email, rol, sub_almacen_id, activo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", 
            $datos['username'],
            $datos['password'],
            $datos['nombre_completo'],
            $datos['email'],
            $datos['rol'],
            $datos['sub_almacen_id']
        );
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    public function actualizar($id, $datos) {
        if ($datos['sub_almacen_id'] === null) {
            $sql = "UPDATE usuarios SET 
                    username = ?, 
                    nombre_completo = ?, 
                    email = ?, 
                    rol = ?, 
                    sub_almacen_id = NULL,
                    activo = ?
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssiii", 
                $datos['username'],
                $datos['nombre_completo'],
                $datos['email'],
                $datos['rol'],
                $datos['activo'],
                $id
            );
        } else {
            $sql = "UPDATE usuarios SET 
                    username = ?, 
                    nombre_completo = ?, 
                    email = ?, 
                    rol = ?, 
                    sub_almacen_id = ?,
                    activo = ?
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssiii", 
                $datos['username'],
                $datos['nombre_completo'],
                $datos['email'],
                $datos['rol'],
                $datos['sub_almacen_id'],
                $datos['activo'],
                $id
            );
        }
        
        return $stmt->execute();
    }
    
    public function actualizarPassword($id, $password) {
        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $password, $id);
        return $stmt->execute();
    }
    
    public function usernameExiste($username, $excluir_id = null) {
        $sql = "SELECT id FROM usuarios WHERE username = ?";
        if ($excluir_id) {
            $sql .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($sql);
        if ($excluir_id) {
            $stmt->bind_param("si", $username, $excluir_id);
        } else {
            $stmt->bind_param("s", $username);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    public function obtenerPermisos($usuario_id) {
        $sql = "SELECT modulo FROM permisos WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permisos = [];
        while($row = $result->fetch_assoc()) {
            $permisos[] = $row['modulo'];
        }
        
        return $permisos;
    }
    
    public function actualizarPermisos($usuario_id, $modulos) {
        // Eliminar permisos existentes
        $sql = "DELETE FROM permisos WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        
        // Insertar nuevos permisos
        if (!empty($modulos)) {
            $sql = "INSERT INTO permisos (usuario_id, modulo) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            foreach ($modulos as $modulo) {
                $stmt->bind_param("is", $usuario_id, $modulo);
                $stmt->execute();
            }
        }
        
        return true;
    }
    
    public function tienePermiso($usuario_id, $modulo) {
        // Admin siempre tiene todos los permisos
        $usuario = $this->obtenerPorId($usuario_id);
        if ($usuario['rol'] === 'admin') {
            return true;
        }
        
        $sql = "SELECT id FROM permisos WHERE usuario_id = ? AND modulo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $modulo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    public function cambiarEstado($id, $estado) {
        $stmt = $this->conn->prepare("UPDATE usuarios SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $estado, $id);
        return $stmt->execute();
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
