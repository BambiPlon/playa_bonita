<?php
class Producto {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function buscarPorCodigo($codigo, $sub_almacen_id = null) {
        if ($sub_almacen_id === null || $sub_almacen_id == 100) {
            // Para compras (almacén general ID 100)
            $sql = "SELECT * FROM inventario WHERE codigo = ? AND sub_almacen_id = 100";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $codigo);
        } else {
            $sql = "SELECT * FROM inventario WHERE codigo = ? AND sub_almacen_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $codigo, $sub_almacen_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        return $producto;
    }
    
    public function buscarPorCodigoGlobal($codigo) {
        $sql = "SELECT * FROM inventario WHERE codigo = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        return $producto;
    }
    
    public function incrementarCantidad($producto_id, $cantidad_adicional) {
        $sql = "UPDATE inventario SET cantidad = cantidad + ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cantidad_adicional, $producto_id);
        $result = $stmt->execute();
        
        // Obtener la nueva cantidad
        $nueva_cantidad = 0;
        if ($result) {
            $sql_cantidad = "SELECT cantidad FROM inventario WHERE id = ?";
            $stmt_cantidad = $this->conn->prepare($sql_cantidad);
            $stmt_cantidad->bind_param("i", $producto_id);
            $stmt_cantidad->execute();
            $resultado = $stmt_cantidad->get_result();
            if ($row = $resultado->fetch_assoc()) {
                $nueva_cantidad = $row['cantidad'];
            }
            $stmt_cantidad->close();
        }
        
        $stmt->close();
        return ['success' => $result, 'nueva_cantidad' => $nueva_cantidad];
    }
    
    public function crear($datos) {
        try {
            $sql = "INSERT INTO inventario (codigo, nombre, descripcion, cantidad, unidad, precio_unitario, stock_minimo, sub_almacen_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssisdii", 
                $datos['codigo'],
                $datos['nombre'],
                $datos['descripcion'],
                $datos['cantidad'],
                $datos['unidad'],
                $datos['precio_unitario'],
                $datos['stock_minimo'],
                $datos['sub_almacen_id']
            );
            
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                return ['error' => 'duplicate', 'message' => 'El código del producto ya existe en el inventario.'];
            }
            return ['error' => 'database', 'message' => 'Error al guardar el producto: ' . $e->getMessage()];
        }
    }
    
    public function obtenerPorAlmacen($sub_almacen_id) {
        $sql = "SELECT i.*, s.nombre as sub_almacen_nombre 
                FROM inventario i 
                INNER JOIN sub_almacenes s ON i.sub_almacen_id = s.id
                WHERE i.sub_almacen_id = ?
                ORDER BY i.nombre";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sub_almacen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $productos = [];
        
        while($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        
        $stmt->close();
        return $productos;
    }
    
    public function actualizarStock($producto_id, $nueva_cantidad) {
        $sql = "UPDATE inventario SET cantidad = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $nueva_cantidad, $producto_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function buscarPorNombre($nombre, $sub_almacen_id) {
        $sql = "SELECT * FROM inventario WHERE nombre = ? AND sub_almacen_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $sub_almacen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        return $producto;
    }
    
    public function buscarPorNombreParcial($nombre_parcial, $limit = 10) {
        $sql = "SELECT 
                    MIN(id) as primer_id,
                    codigo, 
                    nombre, 
                    unidad, 
                    precio_unitario, 
                    stock_minimo, 
                    descripcion, 
                    SUM(cantidad) as cantidad_total,
                    COUNT(DISTINCT sub_almacen_id) as almacenes_count
                FROM inventario 
                WHERE nombre LIKE ? AND (activo = 1 OR activo IS NULL)
                GROUP BY nombre, codigo, unidad, precio_unitario, stock_minimo, descripcion
                ORDER BY nombre
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $search_term = "%" . $nombre_parcial . "%";
        $stmt->bind_param("si", $search_term, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $productos = [];
        
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        
        $stmt->close();
        return $productos;
    }
    
    public function obtenerInventario($sub_almacen_id = null, $rol = null, $user_sub_almacen_id = null) {
        $sql = "SELECT i.*, s.nombre as sub_almacen_nombre 
                FROM inventario i 
                INNER JOIN sub_almacenes s ON i.sub_almacen_id = s.id";
        
        if ($rol === 'compras') {
            $sql .= " WHERE i.sub_almacen_id = 100";
        } elseif ($sub_almacen_id == 100) {
            $sql .= " WHERE i.sub_almacen_id = 100";
        } elseif ($rol !== 'admin' && $rol !== 'gerencia' && $rol !== 'gerencia_general') {
            $sql .= " WHERE i.sub_almacen_id = " . intval($user_sub_almacen_id);
        } elseif ($sub_almacen_id) {
            $sql .= " WHERE i.sub_almacen_id = " . intval($sub_almacen_id);
        }
        
        $sql .= " ORDER BY i.nombre";
        
        $result = $this->conn->query($sql);
        $inventario = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $inventario[] = $row;
            }
        }
        
        return $inventario;
    }
    
    public function obtenerTodos($sub_almacen_id = null) {
        $sql = "SELECT i.*, s.nombre as sub_almacen_nombre 
                FROM inventario i 
                INNER JOIN sub_almacenes s ON i.sub_almacen_id = s.id";
        
        if ($sub_almacen_id) {
            $sql .= " WHERE i.sub_almacen_id = " . intval($sub_almacen_id);
        }
        
        $sql .= " ORDER BY i.nombre";
        
        $result = $this->conn->query($sql);
        $productos = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
        }
        
        return $productos;
    }
    
    public function obtenerEstadisticas($rol = null, $user_sub_almacen_id = null) {
        $stats = [
            'total_productos' => 0,
            'valor_total' => 0,
            'productos_bajo_stock' => 0
        ];
        
        $where_clause = " WHERE sub_almacen_id = 100";
        
        $sql = "SELECT COUNT(*) as total, 
                SUM(cantidad * precio_unitario) as valor_total,
                SUM(CASE WHEN cantidad <= stock_minimo THEN 1 ELSE 0 END) as bajo_stock
                FROM inventario" . $where_clause;
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['total_productos'] = $row['total'];
            $stats['valor_total'] = $row['valor_total'] ?? 0;
            $stats['productos_bajo_stock'] = $row['bajo_stock'];
        }
        
        return $stats;
    }
    
    public function toggleEstadoActivo($producto_id, $nuevo_estado) {
        $sql = "UPDATE inventario SET activo = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $nuevo_estado, $producto_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function obtenerPorId($producto_id) {
        $sql = "SELECT i.*, s.nombre as sub_almacen_nombre 
                FROM inventario i 
                LEFT JOIN sub_almacenes s ON i.sub_almacen_id = s.id 
                WHERE i.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        return $producto;
    }
    
    public function actualizar($producto_id, $datos) {
        try {
            $sql = "UPDATE inventario 
                    SET nombre = ?, descripcion = ?, cantidad = ?, unidad = ?, 
                        precio_unitario = ?, stock_minimo = ?, sub_almacen_id = ?, 
                        updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssisdiis", 
                $datos['nombre'],
                $datos['descripcion'],
                $datos['cantidad'],
                $datos['unidad'],
                $datos['precio_unitario'],
                $datos['stock_minimo'],
                $datos['sub_almacen_id'],
                $producto_id
            );
            
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } catch (mysqli_sql_exception $e) {
            return ['error' => 'database', 'message' => 'Error al actualizar el producto: ' . $e->getMessage()];
        }
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
