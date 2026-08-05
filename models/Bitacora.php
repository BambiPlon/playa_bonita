<?php
require_once __DIR__ . '/../config/database.php';

class Bitacora {
    private $conn;
    
    // Tipos de acciones disponibles
    const ACCION_LOGIN = 'login';
    const ACCION_LOGOUT = 'logout';
    const ACCION_CREAR = 'crear';
    const ACCION_EDITAR = 'editar';
    const ACCION_ELIMINAR = 'eliminar';
    const ACCION_APROBAR = 'aprobar';
    const ACCION_RECHAZAR = 'rechazar';
    const ACCION_COTIZAR = 'cotizar';
    const ACCION_SALIDA = 'salida_almacen';
    const ACCION_ENTRADA = 'entrada_almacen';
    const ACCION_IMPRIMIR = 'imprimir';
    
    // Módulos del sistema
    const MODULO_AUTH = 'autenticacion';
    const MODULO_REQUISICIONES = 'requisiciones';
    const MODULO_INVENTARIO = 'inventario';
    const MODULO_USUARIOS = 'usuarios';
    const MODULO_PROVEEDORES = 'proveedores';
    const MODULO_SALIDAS = 'salidas';
    const MODULO_PLANTILLAS = 'plantillas';
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Registrar una acción en la bitácora
     */
    public function registrar($usuario_id, $usuario_nombre, $accion, $modulo, $descripcion = null, $datos_anteriores = null, $datos_nuevos = null) {
        $ip = $this->obtenerIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $datos_ant_json = $datos_anteriores ? json_encode($datos_anteriores, JSON_UNESCAPED_UNICODE) : null;
        $datos_new_json = $datos_nuevos ? json_encode($datos_nuevos, JSON_UNESCAPED_UNICODE) : null;
        
        $sql = "INSERT INTO bitacora (usuario_id, usuario_nombre, accion, modulo, descripcion, datos_anteriores, datos_nuevos, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssssss", $usuario_id, $usuario_nombre, $accion, $modulo, $descripcion, $datos_ant_json, $datos_new_json, $ip, $user_agent);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener registros de la bitácora con filtros
     */
    public function obtenerRegistros($filtros = []) {
        $sql = "SELECT b.*, u.nombre_completo as usuario_actual
                FROM bitacora b
                LEFT JOIN usuarios u ON b.usuario_id = u.id
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Filtro por acción
        if (!empty($filtros['accion'])) {
            $sql .= " AND b.accion = ?";
            $params[] = $filtros['accion'];
            $types .= "s";
        }
        
        // Filtro por módulo
        if (!empty($filtros['modulo'])) {
            $sql .= " AND b.modulo = ?";
            $params[] = $filtros['modulo'];
            $types .= "s";
        }
        
        // Filtro por usuario
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND b.usuario_id = ?";
            $params[] = $filtros['usuario_id'];
            $types .= "i";
        }
        
        // Filtro por fecha desde
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(b.created_at) >= ?";
            $params[] = $filtros['fecha_desde'];
            $types .= "s";
        }
        
        // Filtro por fecha hasta
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(b.created_at) <= ?";
            $params[] = $filtros['fecha_hasta'];
            $types .= "s";
        }
        
        // Búsqueda en descripción
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (b.descripcion LIKE ? OR b.usuario_nombre LIKE ?)";
            $busqueda = "%" . $filtros['busqueda'] . "%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $types .= "ss";
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        // Límite de registros
        $limite = isset($filtros['limite']) ? intval($filtros['limite']) : 100;
        $sql .= " LIMIT " . $limite;
        
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $registros = [];
        while ($row = $result->fetch_assoc()) {
            $registros[] = $row;
        }
        
        return $registros;
    }
    
    /**
     * Obtener estadísticas de la bitácora
     */
    public function obtenerEstadisticas() {
        $stats = [];
        
        // Total de registros hoy
        $sql = "SELECT COUNT(*) as total FROM bitacora WHERE DATE(created_at) = CURDATE()";
        $result = $this->conn->query($sql);
        $stats['hoy'] = $result->fetch_assoc()['total'];
        
        // Total de logins hoy
        $sql = "SELECT COUNT(*) as total FROM bitacora WHERE accion = 'login' AND DATE(created_at) = CURDATE()";
        $result = $this->conn->query($sql);
        $stats['logins_hoy'] = $result->fetch_assoc()['total'];
        
        // Total de requisiciones creadas hoy
        $sql = "SELECT COUNT(*) as total FROM bitacora WHERE accion = 'crear' AND modulo = 'requisiciones' AND DATE(created_at) = CURDATE()";
        $result = $this->conn->query($sql);
        $stats['requisiciones_hoy'] = $result->fetch_assoc()['total'];
        
        // Total de salidas de almacén hoy
        $sql = "SELECT COUNT(*) as total FROM bitacora WHERE accion = 'salida_almacen' AND DATE(created_at) = CURDATE()";
        $result = $this->conn->query($sql);
        $stats['salidas_hoy'] = $result->fetch_assoc()['total'];
        
        return $stats;
    }
    
    /**
     * Obtener lista de acciones únicas
     */
    public function obtenerAcciones() {
        return [
            self::ACCION_LOGIN => 'Inicio de Sesion',
            self::ACCION_LOGOUT => 'Cierre de Sesion',
            self::ACCION_CREAR => 'Creacion',
            self::ACCION_EDITAR => 'Edicion',
            self::ACCION_ELIMINAR => 'Eliminacion',
            self::ACCION_APROBAR => 'Aprobacion',
            self::ACCION_RECHAZAR => 'Rechazo',
            self::ACCION_COTIZAR => 'Cotizacion',
            self::ACCION_SALIDA => 'Salida de Almacen',
            self::ACCION_ENTRADA => 'Entrada a Almacen',
            self::ACCION_IMPRIMIR => 'Impresion'
        ];
    }
    
    /**
     * Obtener lista de módulos
     */
    public function obtenerModulos() {
        return [
            self::MODULO_AUTH => 'Autenticacion',
            self::MODULO_REQUISICIONES => 'Requisiciones',
            self::MODULO_INVENTARIO => 'Inventario',
            self::MODULO_USUARIOS => 'Usuarios',
            self::MODULO_PROVEEDORES => 'Proveedores',
            self::MODULO_SALIDAS => 'Salidas',
            self::MODULO_PLANTILLAS => 'Plantillas'
        ];
    }
    
    /**
     * Obtener usuarios para filtro
     */
    public function obtenerUsuariosBitacora() {
        $sql = "SELECT DISTINCT b.usuario_id, b.usuario_nombre 
                FROM bitacora b 
                WHERE b.usuario_id IS NOT NULL 
                ORDER BY b.usuario_nombre ASC";
        $result = $this->conn->query($sql);
        
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        
        return $usuarios;
    }
    
    /**
     * Obtener IP del cliente
     */
    private function obtenerIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
    }
}
