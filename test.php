<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli(
  'localhost',
  'b0nwpa6mgd5t_eduar',   // <-- usuario nuevo
  'Req2026pruebas',        // <-- password nueva
  'b0nwpa6mgd5t_inventario_requisiciones'
);

echo "DB OK ✅";