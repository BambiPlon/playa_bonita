<?php
class Requisicion {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Verifica si todos los productos aprobados de una requisicion son surtidos desde almacen
     */
    public function todosProductosDeAlmacen($requisicion_id) {
        $sql = "SELECT 
                    COUNT(*) as total_aprobados,
                    SUM(CASE WHEN COALESCE(surtido_almacen, 0) = 1 THEN 1 ELSE 0 END) as total_almacen
                FROM requisicion_detalles 
                WHERE requisicion_id = ? AND aprobado = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $requisicion_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        // Si no hay productos aprobados, retornar false
        if ($row['total_aprobados'] == 0) {
            return false;
        }
        
        // Retorna true si todos los productos aprobados son de almacen
        return $row['total_aprobados'] == $row['total_almacen'];
    }
    
    public function crear($datos) {
        $folio = 'REQ-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $sub_almacen_id = isset($datos['sub_almacen_id']) && $datos['sub_almacen_id'] > 0 ? $datos['sub_almacen_id'] : null;
        $tipo_requisicion = isset($datos['tipo_requisicion']) ? $datos['tipo_requisicion'] : 'producto';
        
        // Prefijo diferente para servicios
        if ($tipo_requisicion === 'servicio') {
            $folio = 'SRV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        // Verificar si existe la columna tipo_requisicion
        $columnExists = $this->conn->query("SHOW COLUMNS FROM requisiciones LIKE 'tipo_requisicion'");
        $hasTipoColumn = $columnExists && $columnExists->num_rows > 0;
        
        try {
            if ($sub_almacen_id === null) {
                if ($hasTipoColumn) {
                    $sql = "INSERT INTO requisiciones (folio, tipo_requisicion, usuario_id, solicitante, fecha_solicitud, observaciones, estado) 
                            VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ssisss", 
                        $folio, 
                        $tipo_requisicion,
                        $datos['usuario_id'], 
                        $datos['solicitante'], 
                        $datos['fecha_solicitud'], 
                        $datos['observaciones']
                    );
                } else {
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
                }
            } else {
                if ($hasTipoColumn) {
                    $sql = "INSERT INTO requisiciones (folio, tipo_requisicion, sub_almacen_id, usuario_id, solicitante, fecha_solicitud, observaciones, estado) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ssiisss", 
                        $folio, 
                        $tipo_requisicion,
                        $sub_almacen_id, 
                        $datos['usuario_id'], 
                        $datos['solicitante'], 
                        $datos['fecha_solicitud'], 
                        $datos['observaciones']
                    );
                } else {
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
            }
            
            if ($stmt->execute()) {
                $requisicion_id = $this->conn->insert_id;
                $stmt->close();
                return ['success' => true, 'id' => $requisicion_id, 'folio' => $folio];
            }
            
            $error = $this->conn->error;
            $stmt->close();
            return ['success' => false, 'error' => $error];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function agregarDetalle($requisicion_id, $detalle) {
        $justificacion = isset($detalle['justificacion']) ? $detalle['justificacion'] : '';
        $producto_id = isset($detalle['producto_id']) ? $detalle['producto_id'] : null;
        $unidad = isset($detalle['unidad']) ? $detalle['unidad'] : 'pieza';
        
        $sql = "INSERT INTO requisicion_detalles (requisicion_id, producto_id, producto_nombre, cantidad, unidad, justificacion) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisiss", 
            $requisicion_id, 
            $producto_id, 
            $detalle['producto_nombre'], 
            $detalle['cantidad'], 
            $unidad, 
            $justificacion
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function obtenerTodas($estado = null, $rol = null, $user_id = null, $mes = null, $anio = null, $mostrar_ocultas = false, $usuario_filter = null) {
        $sql = "SELECT r.*, r.porcentaje_iva, s.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                FROM requisiciones r 
                LEFT JOIN sub_almacenes s ON r.sub_almacen_id = s.id
                INNER JOIN usuarios u ON r.usuario_id = u.id";
        
        $conditions = [];
        
        if (!$mostrar_ocultas && $user_id) {
            $checkTable = $this->conn->query("SHOW TABLES LIKE 'requisiciones_ocultas'");
            if ($checkTable->num_rows > 0) {
                $sql .= " LEFT JOIN requisiciones_ocultas ro ON r.id = ro.requisicion_id AND ro.usuario_id = " . intval($user_id);
                $conditions[] = "ro.id IS NULL";
            } else {
                $conditions[] = "(r.oculta IS NULL OR r.oculta = 0)";
            }
        } elseif (!$mostrar_ocultas) {
            $conditions[] = "(r.oculta IS NULL OR r.oculta = 0)";
        }
        
        if ($rol === 'departamento' || $rol === 'solo_lectura') {
            $conditions[] = "r.usuario_id = " . intval($user_id);
            if (!$estado) {
                $conditions[] = "r.estado NOT IN ('completada')";
            }
        } elseif ($rol === 'compras') {
            if (!$estado) {
                $conditions[] = "r.estado NOT IN ('completada')";
            }
        } elseif ($rol === 'gerencia') {
            if (!$estado) {
                $conditions[] = "r.estado NOT IN ('completada', 'aprobada', 'rechazada', 'pendiente')";
            }
        } elseif ($rol === 'gerencia_general') {
            if (!$estado) {
                $conditions[] = "r.estado NOT IN ('completada', 'aprobada', 'rechazada', 'pendiente', 'cotizada')";
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
        
        // Filtro por usuario solicitante
        if ($usuario_filter) {
            $conditions[] = "r.usuario_id = " . intval($usuario_filter);
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        $result = $this->conn->query($sql);
        $requisiciones = [];
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $requisiciones[] = $row;
            }
        }
        
        return $requisiciones;
    }
    
    // Obtener usuarios que han creado requisiciones (para el filtro)
    public function obtenerUsuariosConRequisiciones() {
        $sql = "SELECT DISTINCT u.id, u.nombre_completo, u.rol 
                FROM usuarios u 
                INNER JOIN requisiciones r ON u.id = r.usuario_id 
                WHERE u.activo = 1 
                ORDER BY u.nombre_completo ASC";
        $result = $this->conn->query($sql);
        $usuarios = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        return $usuarios;
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
        
        $base_where = "(oculta IS NULL OR oculta = 0)";
        
        $req_where = "";
        if ($rol === 'departamento' || $rol === 'solo_lectura') {
            $req_where = " WHERE " . $base_where . " AND usuario_id = " . intval($user_id) . " AND estado NOT IN ('completada')";
        } elseif ($rol === 'compras') {
            $req_where = " WHERE " . $base_where . " AND estado NOT IN ('completada')";
        } elseif ($rol === 'gerencia') {
            $req_where = " WHERE " . $base_where . " AND estado NOT IN ('completada', 'aprobada', 'rechazada', 'pendiente')";
        } elseif ($rol === 'gerencia_general') {
            $req_where = " WHERE " . $base_where . " AND estado NOT IN ('completada', 'aprobada', 'rechazada', 'pendiente', 'cotizada')";
        } else {
            $req_where = " WHERE " . $base_where;
        }
        
        $sql = "SELECT COUNT(*) as total FROM requisiciones" . $req_where;
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['total_requisiciones'] = $row['total'];
        }
        
        $sql = "SELECT COUNT(*) as total FROM requisiciones" . $req_where . " AND estado = 'pendiente'";
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['requisiciones_pendientes'] = $row['total'];
        }
        
        $sql = "SELECT COUNT(*) as total FROM requisiciones" . $req_where . " AND estado = 'aprobada'";
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            $stats['requisiciones_aprobadas'] = $row['total'];
        }
        
        return $stats;
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT r.*, r.porcentaje_iva, s.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
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
                       rd.unidad as unidad,
                       rd.precio_cotizado,
                       COALESCE(rd.surtido_almacen, 0) as surtido_almacen,
                       i.precio_unitario,
                       inv_original.codigo as codigo_original,
                       p.nombre as proveedor_nombre
                FROM requisicion_detalles rd
                LEFT JOIN inventario i ON rd.producto_id = i.id
                LEFT JOIN inventario inv_original ON rd.producto_id = inv_original.id
                LEFT JOIN proveedores p ON rd.proveedor_id = p.id
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
    
    public function ocultarRequisicionParaUsuario($requisicion_id, $usuario_id) {
        // Primero verificar si la tabla existe, si no usar el campo legacy
        $checkTable = $this->conn->query("SHOW TABLES LIKE 'requisiciones_ocultas'");
        
        if ($checkTable->num_rows > 0) {
            // Usar la nueva tabla de ocultas por usuario
            $sql = "INSERT IGNORE INTO requisiciones_ocultas (requisicion_id, usuario_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $requisicion_id, $usuario_id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        } else {
            // Fallback al campo legacy
            return $this->ocultarRequisicion($requisicion_id);
        }
    }
    
    public function estaOcultaParaUsuario($requisicion_id, $usuario_id) {
        $checkTable = $this->conn->query("SHOW TABLES LIKE 'requisiciones_ocultas'");
        
        if ($checkTable->num_rows > 0) {
            $sql = "SELECT 1 FROM requisiciones_ocultas WHERE requisicion_id = ? AND usuario_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $requisicion_id, $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $oculta = $result->num_rows > 0;
            $stmt->close();
            return $oculta;
        } else {
            // Fallback al campo legacy
            $sql = "SELECT oculta FROM requisiciones WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $requisicion_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row && $row['oculta'] == 1;
        }
    }
    
    public function ocultarRequisicion($requisicion_id) {
        $sql = "UPDATE requisiciones SET oculta = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $requisicion_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /*
    // Conexión manejada por singleton - no cerrar aquí
    */
}
?>
