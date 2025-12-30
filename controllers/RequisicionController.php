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
    
    public function listar($user, $estado_filter = null, $mes_filter = null, $anio_filter = null) {
        return $this->requisicionModel->obtenerTodas($estado_filter, $user['rol'], $user['id'], $mes_filter, $anio_filter);
    }
    
    public function crear($datos, $user) {
        $resultado = $this->requisicionModel->crear($datos);
        
        if ($resultado['success']) {
            $productos_req = $datos['productos'];
            $cantidades = $datos['cantidades'];
            $unidades = $datos['unidades'];
            
            $productos_nombre_otros = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'producto_nombre_') === 0) {
                    $index = str_replace('producto_nombre_', '', $key);
                    $productos_nombre_otros[$index] = trim($value);
                }
            }
            
            $contador_otros = 0;
            $indices_otros = array_keys($productos_nombre_otros);
            sort($indices_otros); // Ordenar para procesar en orden
            
            for ($i = 0; $i < count($productos_req); $i++) {
                if (!empty($productos_req[$i]) && !empty($cantidades[$i])) {
                    $producto_id = ($productos_req[$i] != 'otro') ? intval($productos_req[$i]) : null;
                    $producto_nombre = '';
                    
                    if ($producto_id) {
                        // Producto del inventario
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
                        $conn->close();
                    } else {
                        if (isset($indices_otros[$contador_otros])) {
                            $indice_real = $indices_otros[$contador_otros];
                            $producto_nombre = $productos_nombre_otros[$indice_real];
                            $contador_otros++;
                        }
                    }
                    
                    $detalle = [
                        'producto_id' => $producto_id,
                        'producto_nombre' => $producto_nombre,
                        'cantidad' => intval($cantidades[$i]),
                        'unidad' => $unidades[$i]
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
