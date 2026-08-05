<?php
class Salida {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function crear($datos) {
        // Verificar stock disponible antes de hacer la salida
        $stock_actual = $this->obtenerStock($datos['producto_id']);
        if ($stock_actual === false || intval($datos['cantidad']) > $stock_actual) {
            return 'SIN_STOCK';
        }
        
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
    
    private function obtenerStock($producto_id) {
        $query = "SELECT cantidad FROM inventario WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? intval($row['cantidad']) : false;
    }
    
    private function actualizarInventario($producto_id, $cantidad) {
        $query = "UPDATE inventario SET cantidad = cantidad - ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $cantidad, $producto_id);
        $stmt->execute();
    }
    
    public function crearMultiple($datos_base, $productos) {
        $folio_base = 'SAL-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $errores = [];
        $exitosos = 0;
        $primer_id = null;
        
        foreach ($productos as $prod) {
            $stock_actual = $this->obtenerStock($prod['producto_id']);
            if ($stock_actual === false || intval($prod['cantidad']) > $stock_actual) {
                $errores[] = "Producto ID {$prod['producto_id']}: stock insuficiente (disponible: {$stock_actual}, solicitado: {$prod['cantidad']})";
                continue;
            }
            
            // Usar el mismo folio base para todas las salidas del grupo
            $query = "INSERT INTO salidas_almacen (folio, folio_grupo, usuario_id, sub_almacen_id, producto_id, cantidad, motivo, destino, fecha_salida) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $folio_item = $folio_base . '-' . ($exitosos + 1);
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssiisssss", 
                $folio_item,
                $folio_base,
                $datos_base['usuario_id'],
                $datos_base['sub_almacen_id'],
                $prod['producto_id'],
                $prod['cantidad'],
                $datos_base['motivo'],
                $datos_base['destino'],
                $datos_base['fecha_salida']
            );
            
            if ($stmt->execute()) {
                if ($primer_id === null) {
                    $primer_id = $this->conn->insert_id;
                }
                $this->actualizarInventario($prod['producto_id'], $prod['cantidad']);
                $exitosos++;
            }
        }
        
        if ($exitosos > 0) {
            return ['success' => true, 'folio' => $folio_base, 'exitosos' => $exitosos, 'errores' => $errores, 'primer_id' => $primer_id];
        }
        return ['success' => false, 'errores' => $errores];
    }
    
    public function obtenerPorFolioGrupo($folio_grupo) {
        $query = "SELECT s.*, p.nombre as producto_nombre, p.codigo as producto_codigo, p.unidad,
                  sa.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                  FROM salidas_almacen s
                  INNER JOIN inventario p ON s.producto_id = p.id
                  INNER JOIN sub_almacenes sa ON s.sub_almacen_id = sa.id
                  INNER JOIN usuarios u ON s.usuario_id = u.id
                  WHERE s.folio_grupo = ?
                  ORDER BY s.id ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $folio_grupo);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function obtenerRequisicionesCompletadas($sub_almacen_id = null) {
        $sql = "SELECT r.id, r.folio, r.fecha_solicitud, r.solicitante, u.nombre_completo as usuario_nombre,
                       (SELECT COUNT(*) FROM requisicion_detalles rd WHERE rd.requisicion_id = r.id) as total_productos
                FROM requisiciones r
                LEFT JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.estado = 'completada'";
        if ($sub_almacen_id) {
            $sql .= " AND r.sub_almacen_id = " . intval($sub_almacen_id);
        }
        $sql .= " ORDER BY r.fecha_solicitud DESC LIMIT 50";
        
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function obtenerProductosRequisicion($requisicion_id) {
        // Primero obtener el sub_almacen de la requisicion
        $sql_req = "SELECT sub_almacen_id FROM requisiciones WHERE id = ?";
        $stmt_req = $this->conn->prepare($sql_req);
        $stmt_req->bind_param("i", $requisicion_id);
        $stmt_req->execute();
        $req_result = $stmt_req->get_result();
        $req_data = $req_result->fetch_assoc();
        $sub_almacen_id = $req_data ? $req_data['sub_almacen_id'] : null;
        
        // Buscar productos por nombre y unidad (ya que el ID puede no coincidir)
        $sql = "SELECT rd.producto_id, rd.producto_nombre, rd.cantidad, rd.unidad,
                       i.id as inventario_id, i.cantidad as stock_disponible, i.nombre as inv_nombre, i.unidad as inv_unidad
                FROM requisicion_detalles rd
                LEFT JOIN inventario i ON (
                    (rd.producto_id = i.id) OR 
                    (LOWER(TRIM(rd.producto_nombre)) = LOWER(TRIM(i.nombre)) AND LOWER(TRIM(rd.unidad)) = LOWER(TRIM(i.unidad)))
                )
                WHERE rd.requisicion_id = ?";
        
        if ($sub_almacen_id) {
            $sql .= " AND (i.sub_almacen_id = ? OR i.sub_almacen_id IS NULL)";
        }
        
        $sql .= " GROUP BY rd.id, rd.producto_id, rd.producto_nombre, rd.cantidad, rd.unidad";
        
        $stmt = $this->conn->prepare($sql);
        if ($sub_almacen_id) {
            $stmt->bind_param("ii", $requisicion_id, $sub_almacen_id);
        } else {
            $stmt->bind_param("i", $requisicion_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
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
