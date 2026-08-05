<?php
require_once 'models/Salida.php';
require_once 'models/Producto.php';
require_once 'models/SubAlmacen.php';
require_once 'models/Bitacora.php';

class SalidaController {
    private $salidaModel;
    private $productoModel;
    private $subAlmacenModel;
    private $bitacora;
    
    public function __construct($db) {
        $this->salidaModel = new Salida($db);
        $this->productoModel = new Producto($db);
        $this->subAlmacenModel = new SubAlmacen($db);
        $this->bitacora = new Bitacora();
    }
    
    public function crear($datos) {
        $resultado = $this->salidaModel->crear($datos);
        if ($resultado) {
            $this->bitacora->registrar(
                $_SESSION['user_id'] ?? null,
                $_SESSION['user_nombre'] ?? 'Sistema',
                'crear_salida',
                'salidas',
                'Salida de producto: ' . ($datos['producto_nombre'] ?? 'ID: ' . ($datos['producto_id'] ?? 'N/A')) . ' - Cantidad: ' . ($datos['cantidad'] ?? 0),
                null,
                $datos
            );
        }
        return $resultado;
    }
    
    public function crearMultiple($datos_base, $productos) {
        $resultado = $this->salidaModel->crearMultiple($datos_base, $productos);
        if ($resultado) {
            $this->bitacora->registrar(
                $_SESSION['user_id'] ?? null,
                $_SESSION['user_nombre'] ?? 'Sistema',
                'crear_salida_multiple',
                'salidas',
                'Salida multiple de ' . count($productos) . ' productos - Destino: ' . ($datos_base['destino'] ?? 'N/A'),
                null,
                ['datos_base' => $datos_base, 'productos' => $productos]
            );
        }
        return $resultado;
    }
    
    public function obtenerRequisicionesCompletadas($sub_almacen_id = null) {
        return $this->salidaModel->obtenerRequisicionesCompletadas($sub_almacen_id);
    }
    
    public function obtenerProductosRequisicion($requisicion_id) {
        return $this->salidaModel->obtenerProductosRequisicion($requisicion_id);
    }
    
    public function obtenerSalidas($usuario_id, $sub_almacen_id = null) {
        return $this->salidaModel->obtenerPorUsuario($usuario_id, $sub_almacen_id);
    }
    
    public function obtenerProductos($sub_almacen_id = null) {
        return $this->productoModel->obtenerTodos($sub_almacen_id);
    }
    
    public function obtenerSubAlmacenes() {
        return $this->subAlmacenModel->obtenerTodos();
    }
    
    public function obtenerSalidaPorId($id) {
        return $this->salidaModel->obtenerPorId($id);
    }
}
