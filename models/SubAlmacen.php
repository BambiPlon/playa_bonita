<?php
class SubAlmacen {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function obtenerTodos() {
        $sql = "SELECT * FROM sub_almacenes ORDER BY nombre";
        $result = $this->conn->query($sql);
        $almacenes = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $almacenes[] = $row;
            }
        }
        
        return $almacenes;
    }
    
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM sub_almacenes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
