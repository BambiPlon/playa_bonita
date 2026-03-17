<?php
class Plantilla {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Obtener todas las plantillas de un usuario
     */
    public function obtenerPorUsuario($usuario_id) {
        $sql = "SELECT p.*, 
                       (SELECT COUNT(*) FROM plantilla_productos pp WHERE pp.plantilla_id = p.id) as total_productos
                FROM plantillas p 
                WHERE p.usuario_id = ? 
                ORDER BY p.fecha_actualizacion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $plantillas = [];
        while ($row = $result->fetch_assoc()) {
            $plantillas[] = $row;
        }
        $stmt->close();
        return $plantillas;
    }
    
    /**
     * Obtener una plantilla por ID (con verificacion de usuario)
     */
    public function obtenerPorId($id, $usuario_id = null) {
        $sql = "SELECT * FROM plantillas WHERE id = ?";
        $params = [$id];
        $types = "i";
        
        if ($usuario_id !== null) {
            $sql .= " AND usuario_id = ?";
            $params[] = $usuario_id;
            $types .= "i";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $plantilla = $result->fetch_assoc();
        $stmt->close();
        return $plantilla;
    }
    
    /**
     * Obtener los productos de una plantilla
     */
    public function obtenerProductos($plantilla_id) {
        $sql = "SELECT pp.*, 
                       COALESCE(pr.nombre, pp.nombre_custom) as nombre_producto
                FROM plantilla_productos pp
                LEFT JOIN inventario pr ON pp.producto_id = pr.id
                WHERE pp.plantilla_id = ?
                ORDER BY pp.id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $plantilla_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        $stmt->close();
        return $productos;
    }
    
    /**
     * Crear una nueva plantilla con sus productos
     */
    public function crear($usuario_id, $nombre, $descripcion, $productos) {
        $this->conn->begin_transaction();
        try {
            // Insertar plantilla
            $sql = "INSERT INTO plantillas (usuario_id, nombre, descripcion) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iss", $usuario_id, $nombre, $descripcion);
            $stmt->execute();
            $plantilla_id = $this->conn->insert_id;
            $stmt->close();
            
            // Insertar productos
            $this->insertarProductos($plantilla_id, $productos);
            
            $this->conn->commit();
            return $plantilla_id;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    /**
     * Actualizar una plantilla existente
     */
    public function actualizar($id, $usuario_id, $nombre, $descripcion, $productos) {
        $this->conn->begin_transaction();
        try {
            // Actualizar plantilla
            $sql = "UPDATE plantillas SET nombre = ?, descripcion = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssii", $nombre, $descripcion, $id, $usuario_id);
            $stmt->execute();
            $stmt->close();
            
            // Eliminar productos anteriores
            $sql = "DELETE FROM plantilla_productos WHERE plantilla_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            
            // Insertar nuevos productos
            $this->insertarProductos($id, $productos);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
    
    /**
     * Eliminar una plantilla
     */
    public function eliminar($id, $usuario_id) {
        $sql = "DELETE FROM plantillas WHERE id = ? AND usuario_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
    
    /**
     * Insertar productos en una plantilla
     */
    private function insertarProductos($plantilla_id, $productos) {
        $sql = "INSERT INTO plantilla_productos (plantilla_id, producto_id, nombre_custom, cantidad, unidad) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        foreach ($productos as $prod) {
            $producto_id = !empty($prod['producto_id']) && $prod['producto_id'] !== 'otro' ? intval($prod['producto_id']) : null;
            $nombre_custom = !empty($prod['nombre_custom']) ? $prod['nombre_custom'] : null;
            $cantidad = intval($prod['cantidad']);
            $unidad = $prod['unidad'];
            
            $stmt->bind_param("iisis", $plantilla_id, $producto_id, $nombre_custom, $cantidad, $unidad);
            $stmt->execute();
        }
        $stmt->close();
    }
}
