<?php
require_once 'models/Producto.php';
require_once 'models/SubAlmacen.php';

class ProductoController {
    private $productoModel;
    private $subAlmacenModel;
    
    public function __construct($db) {
        $this->productoModel = new Producto($db);
        $this->subAlmacenModel = new SubAlmacen($db);
    }
    
    public function crear($datos) {
        return $this->productoModel->crear($datos);
    }
    
    public function obtenerProductos($sub_almacen_id = null) {
        return $this->productoModel->obtenerTodos($sub_almacen_id);
    }
    
    public function obtenerSubAlmacenes() {
        return $this->subAlmacenModel->obtenerTodos();
    }
}
