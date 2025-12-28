<?php
require_once 'models/Salida.php';
require_once 'models/Producto.php';
require_once 'models/SubAlmacen.php';

class SalidaController {
    private $salidaModel;
    private $productoModel;
    private $subAlmacenModel;
    
    public function __construct($db) {
        $this->salidaModel = new Salida($db);
        $this->productoModel = new Producto($db);
        $this->subAlmacenModel = new SubAlmacen($db);
    }
    
    public function crear($datos) {
        return $this->salidaModel->crear($datos);
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
