<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventario_requisiciones');

// Conexión singleton para evitar múltiples conexiones y cierres prematuros
$GLOBALS['db_connection'] = null;

function getConnection() {
    // Si ya existe una conexión válida, reutilizarla
    if ($GLOBALS['db_connection'] !== null && $GLOBALS['db_connection']->ping()) {
        return $GLOBALS['db_connection'];
    }
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    $GLOBALS['db_connection'] = $conn;
    return $conn;
}
