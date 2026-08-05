<?php
class RequisicionController {
    private $requisicionModel;
    private $productoModel;
    private $subAlmacenModel;
    private $notificacionModel;
    
    public function __construct() {
        $this->requisicionModel = new Requisicion();
        $this->productoModel = new Producto();
        $this->subAlmacenModel = new SubAlmacen();
        $this->notificacionModel = new Notificacion();
    }
    
    public function listar($user, $estado_filter = null, $mes_filter = null, $anio_filter = null, $mostrar_ocultas = false, $usuario_filter = null) {
        return $this->requisicionModel->obtenerTodas($estado_filter, $user['rol'], $user['id'], $mes_filter, $anio_filter, $mostrar_ocultas, $usuario_filter);
    }
    
    public function obtenerUsuariosConRequisiciones() {
        return $this->requisicionModel->obtenerUsuariosConRequisiciones();
    }
    
    public function crear($datos, $user) {
        $resultado = $this->requisicionModel->crear($datos);
        
        if ($resultado['success']) {
            $tipoRequisicion = $datos['tipo_requisicion'] ?? 'producto';
            
            if ($tipoRequisicion === 'servicio') {
                // Procesar servicios
                $productos_nombre = $datos['productos_nombre'] ?? [];
                
                foreach ($productos_nombre as $index => $nombre) {
                    if (!empty($nombre)) {
                        $detalle = [
                            'producto_id' => null,
                            'producto_nombre' => $nombre,
                            'cantidad' => 1,
                            'unidad' => 'servicio'
                        ];
                        
                        $this->requisicionModel->agregarDetalle($resultado['id'], $detalle);
                    }
                }
                
                // Notificar a compras
                $this->notificacionModel->notificarRol(
                    'compras',
                    'nueva_requisicion',
                    'Nueva Requisición de Servicio',
                    "Se ha recibido una nueva requisición de servicio {$resultado['folio']} de {$user['nombre_completo']}. Por favor revísala.",
                    $resultado['id']
                );
            } else {
                // Procesar productos (lógica existente)
                $productos_req = $datos['productos'] ?? [];
                $cantidades = $datos['cantidades'] ?? [];
                $unidades = $datos['unidades'] ?? [];
                
                $productos_nombre_otros = $datos['productos_nombre'] ?? [];
                
                for ($i = 0; $i < count($productos_req); $i++) {
                    if (!empty($productos_req[$i]) && !empty($cantidades[$i])) {
                        $producto_id = ($productos_req[$i] != 'otro' && $productos_req[$i] != 'servicio') ? intval($productos_req[$i]) : null;
                        $producto_nombre = '';
                        
                        if ($producto_id) {
                            $conn = getConnection();
                            $sql = "SELECT nombre FROM inventario WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $producto_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($row = $result->fetch_assoc()) {
                                $producto_nombre = $row['nombre'];
                            }
                            $stmt->close();
                        } else {
                            // Usar el array paralelo productos_nombre_custom[] que tiene el mismo indice
                            if (isset($productos_nombre_otros[$i]) && trim($productos_nombre_otros[$i]) !== '') {
                                $producto_nombre = trim($productos_nombre_otros[$i]);
                            }
                        }
                        
                        $unidad_val = isset($unidades[$i]) && trim($unidades[$i]) !== '' ? trim($unidades[$i]) : 'pieza';
                        
                        $detalle = [
                            'producto_id' => $producto_id,
                            'producto_nombre' => $producto_nombre,
                            'cantidad' => intval($cantidades[$i]),
                            'unidad' => $unidad_val
                        ];
                        
                        $this->requisicionModel->agregarDetalle($resultado['id'], $detalle);
                    }
                }
                
                // Notificar a compras
                $this->notificacionModel->notificarRol(
                    'compras',
                    'nueva_requisicion',
                    'Nueva Requisición Recibida',
                    "Se ha recibido una nueva requisición {$resultado['folio']} de {$user['nombre_completo']}. Por favor revísala.",
                    $resultado['id']
                );
            }
            
            return ['success' => true, 'folio' => $resultado['folio']];
        }
        
        return $resultado;
    }
    
    public function cambiarEstado($id, $nuevo_estado) {
        return $this->requisicionModel->cambiarEstado($id, $nuevo_estado);
    }
    
    public function obtenerDatosFormulario($user) {
        $sub_almacenes = $this->subAlmacenModel->obtenerTodos();
        $productos = $this->productoModel->obtenerInventario(null, 'admin', null);
        
        return [
            'sub_almacenes' => $sub_almacenes,
            'productos' => $productos
        ];
    }
}
?>
