<?php
class NotificacionController {
    private $notificacionModel;
    
    public function __construct() {
        $this->notificacionModel = new Notificacion();
    }
    
    public function listar($usuario_id, $solo_no_leidas = false) {
        return $this->notificacionModel->obtenerPorUsuario($usuario_id, $solo_no_leidas);
    }
    
    public function contarNoLeidas($usuario_id) {
        return $this->notificacionModel->contarNoLeidas($usuario_id);
    }
    
    public function marcarLeida($notificacion_id) {
        return $this->notificacionModel->marcarLeida($notificacion_id);
    }
}
?>
