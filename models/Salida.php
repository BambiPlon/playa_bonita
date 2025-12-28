<?php
class Salida {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function crear($datos) {
        $folio = 'SAL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $query = "INSERT INTO salidas_almacen (folio, usuario_id, sub_almacen_id, producto_id, cantidad, motivo, destino, fecha_salida) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("siiissss", 
            $folio,
            $datos['usuario_id'],
            $datos['sub_almacen_id'],
            $datos['producto_id'],
            $datos['cantidad'],
            $datos['motivo'],
            $datos['destino'],
            $datos['fecha_salida']
        );
        
        if ($stmt->execute()) {
            // Actualizar inventario
            $this->actualizarInventario($datos['producto_id'], $datos['cantidad']);
            return $folio;
        }
        
        return false;
    }
    
    private function actualizarInventario($producto_id, $cantidad) {
        $query = "UPDATE inventario SET cantidad = cantidad - ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $cantidad, $producto_id);
        $stmt->execute();
    }
    
    public function obtenerPorUsuario($usuario_id, $sub_almacen_id = null) {
        $query = "SELECT s.*, p.nombre as producto_nombre, p.codigo as producto_codigo, 
                  sa.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                  FROM salidas_almacen s
                  INNER JOIN inventario p ON s.producto_id = p.id
                  INNER JOIN sub_almacenes sa ON s.sub_almacen_id = sa.id
                  INNER JOIN usuarios u ON s.usuario_id = u.id";
        
        if ($sub_almacen_id) {
            $query .= " WHERE s.sub_almacen_id = ?";
        }
        
        $query .= " ORDER BY s.created_at DESC";
        
        if ($sub_almacen_id) {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $sub_almacen_id);
        } else {
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT s.*, p.nombre as producto_nombre, p.codigo as producto_codigo, p.unidad,
                  sa.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                  FROM salidas_almacen s
                  INNER JOIN inventario p ON s.producto_id = p.id
                  INNER JOIN sub_almacenes sa ON s.sub_almacen_id = sa.id
                  INNER JOIN usuarios u ON s.usuario_id = u.id
                  WHERE s.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
