<?php
class DashboardController {
    private $productoModel;
    private $requisicionModel;
    private $subAlmacenModel;
    
    public function __construct() {
        $this->productoModel = new Producto();
        $this->requisicionModel = new Requisicion();
        $this->subAlmacenModel = new SubAlmacen();
    }
    
    public function index($user, $sub_almacen_filter = null) {
        $stats_productos = $this->productoModel->obtenerEstadisticas($user['rol'], $user['sub_almacen_id']);
        $stats_requisiciones = $this->requisicionModel->obtenerEstadisticas($user['rol'], $user['id']);
        
        $stats = array_merge($stats_productos, $stats_requisiciones);
        
        $inventario = $this->productoModel->obtenerInventario($sub_almacen_filter, $user['rol'], $user['sub_almacen_id']);
        $sub_almacenes = $this->subAlmacenModel->obtenerTodos();
        
        return [
            'stats' => $stats,
            'inventario' => $inventario,
            'sub_almacenes' => $sub_almacenes
        ];
    }
}
?>
