<?php
class Requisicion {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function crear($datos) {
        $folio = 'REQ-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $sub_almacen_id = isset($datos['sub_almacen_id']) && $datos['sub_almacen_id'] > 0 ? $datos['sub_almacen_id'] : null;
        
        if ($sub_almacen_id === null) {
            // Insertar sin sub_almacen_id
            $sql = "INSERT INTO requisiciones (folio, usuario_id, solicitante, fecha_solicitud, observaciones, estado) 
                    VALUES (?, ?, ?, ?, ?, 'pendiente')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sisss", 
                $folio, 
                $datos['usuario_id'], 
                $datos['solicitante'], 
                $datos['fecha_solicitud'], 
                $datos['observaciones']
            );
        } else {
            // Insertar con sub_almacen_id
            $sql = "INSERT INTO requisiciones (folio, sub_almacen_id, usuario_id, solicitante, fecha_solicitud, observaciones, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("siisss", 
                $folio, 
                $sub_almacen_id, 
                $datos['usuario_id'], 
                $datos['solicitante'], 
                $datos['fecha_solicitud'], 
                $datos['observaciones']
            );
        }
        
        if ($stmt->execute()) {
            $requisicion_id = $this->conn->insert_id;
            $stmt->close();
            return ['success' => true, 'id' => $requisicion_id, 'folio' => $folio];
        }
        
        $error = $this->conn->error;
        $stmt->close();
        return ['success' => false, 'error' => $error];
    }
    
    public function agregarDetalle($requisicion_id, $detalle) {
        $sql = "INSERT INTO requisicion_detalles (requisicion_id, producto_id, producto_nombre, cantidad, unidad, justificacion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisiss", 
            $requisicion_id, 
            $detalle['producto_id'], 
            $detalle['producto_nombre'], 
            $detalle['cantidad'], 
            $detalle['unidad'], 
            $detalle['justificacion']
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function obtenerTodas($estado = null, $rol = null, $user_id = null, $mes = null, $anio = null) {
        $sql = "SELECT r.*, s.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                FROM requisiciones r 
                LEFT JOIN sub_almacenes s ON r.sub_almacen_id = s.id
                INNER JOIN usuarios u ON r.usuario_id = u.id";
        
        $conditions = [];
        
        if ($rol === 'departamento' || $rol === 'solo_lectura') {
            $conditions[] = "r.usuario_id = " . intval($user_id);
        } elseif ($rol === 'compras') {
            if (!$estado) {
                $conditions[] = "(r.estado = 'pendiente' OR r.estado = 'en_compras' OR r.estado = 'en_gerencia' OR r.estado = 'aprobada')";
            }
        } elseif ($rol === 'gerencia' || $rol === 'gerencia_general') {
            if (!$estado) {
                $conditions[] = "(r.estado = 'en_gerencia' OR r.estado = 'en_gerencia_general' OR r.estado = 'aprobada' OR r.estado = 'rechazada')";
            }
        }
        
        if ($estado) {
            $conditions[] = "r.estado = '" . $this->conn->real_escape_string($estado) . "'";
        }
        
        if ($mes) {
            $conditions[] = "MONTH(r.fecha_solicitud) = " . intval($mes);
        }
        
        if ($anio) {
            $conditions[] = "YEAR(r.fecha_solicitud) = " . intval($anio);
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        $result = $this->conn->query($sql);
        $requisiciones = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $requisiciones[] = $row;
            }
        }
        
        return $requisiciones;
    }
    
    public function cambiarEstado($id, $nuevo_estado) {
        $sql = "UPDATE requisiciones SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nuevo_estado, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function obtenerEstadisticas($rol = null, $user_id = null) {
        $stats = [
            'total_requisiciones' => 0,
            'requisiciones_pendientes' => 0,
            'requisiciones_aprobadas' => 0
        ];
        
        $req_where = "";
        if ($rol === 'departamento' || $rol === 'solo_lectura') {
            $req_where = " WHERE usuario_id = " . intval($user_id);
        } elseif ($rol === 'compras') {
            $req_where = " WHERE estado IN ('pendiente', 'en_compras', 'en_gerencia', 'aprobada')";
        } elseif ($rol === 'gerencia' || $rol === 'gerencia_general') {
            $req_where = " WHERE estado IN ('en_gerencia', 'en_gerencia_general', 'aprobada', 'rechazada')";
        }
        
        $sql = "SELECT COUNT(*) as total FROM requisiciones" . $req_where;
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['total_requisiciones'] = $row['total'];
        }
        
        $sql = "SELECT COUNT(*) as total FROM requisiciones" . $req_where . 
               ($req_where ? " AND" : " WHERE") . " estado = 'pendiente'";
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['requisiciones_pendientes'] = $row['total'];
        }
        
        $sql = "SELECT COUNT(*) as total FROM requisiciones" . $req_where . 
               ($req_where ? " AND" : " WHERE") . " estado = 'aprobada'";
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['requisiciones_aprobadas'] = $row['total'];
        }
        
        return $stats;
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT r.*, s.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                FROM requisiciones r 
                LEFT JOIN sub_almacenes s ON r.sub_almacen_id = s.id
                INNER JOIN usuarios u ON r.usuario_id = u.id
                WHERE r.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $requisicion = $result->fetch_assoc();
        $stmt->close();
        return $requisicion;
    }
    
    public function obtenerDetalles($requisicion_id) {
        $sql = "SELECT rd.*, 
                i.precio_unitario,
                inv_original.codigo as codigo_original
                FROM requisicion_detalles rd
                LEFT JOIN inventario i ON rd.producto_id = i.id
                LEFT JOIN inventario inv_original ON rd.producto_id = inv_original.id
                WHERE rd.requisicion_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $requisicion_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $detalles = [];
        while($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
        $stmt->close();
        return $detalles;
    }
    
    public function cotizar($requisicion_id, $monto_cotizado, $usuario_id) {
        $sql = "UPDATE requisiciones 
                SET monto_cotizado = ?, 
                    fecha_cotizacion = NOW(), 
                    cotizado_por = ?,
                    estado = 'en_gerencia'
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("dii", $monto_cotizado, $usuario_id, $requisicion_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function aprobarPorGerencia($requisicion_id, $usuario_id) {
        $sql = "UPDATE requisiciones 
                SET estado = 'aprobada',
                    aprobado_por_gerencia = ?,
                    fecha_aprobacion_gerencia = NOW()
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $requisicion_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /*
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    */
}
?>
