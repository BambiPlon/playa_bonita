<?php
// PDF para imprimir salida de almacén de productos surtidos desde inventario
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Error: Acceso no autorizado');
}

$user = [
    'id' => $_SESSION['user_id'],
    'nombre' => $_SESSION['user_nombre'],
    'rol' => $_SESSION['user_rol']
];

// Obtener ID de requisición
$requisicion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (empty($requisicion_id)) {
    die('Error: No se especificó la requisición');
}

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
$conn->set_charset("utf8");

// Obtener datos de la requisición
$sqlReq = "SELECT r.*, u.nombre_completo as solicitante_nombre, u.rol as departamento 
           FROM requisiciones r 
           LEFT JOIN usuarios u ON r.usuario_id = u.id 
           WHERE r.id = ?";
$stmtReq = $conn->prepare($sqlReq);
$stmtReq->bind_param("i", $requisicion_id);
$stmtReq->execute();
$requisicion = $stmtReq->get_result()->fetch_assoc();
$stmtReq->close();

if (!$requisicion) {
    die('Error: Requisición no encontrada');
}

// Obtener solo productos surtidos desde almacén
$sql = "SELECT 
    COALESCE(i.codigo, CONCAT('PROD-', LPAD(rd.id, 4, '0'))) as codigo,
    COALESCE(i.nombre, rd.producto_nombre, 'Producto') as producto,
    COALESCE(rd.unidad, i.unidad, 'pieza') as unidad,
    rd.cantidad,
    COALESCE(s.nombre, 'Almacen General') as almacen_origen,
    i.sub_almacen_id
FROM requisicion_detalles rd
INNER JOIN requisiciones r ON rd.requisicion_id = r.id
LEFT JOIN inventario i ON rd.producto_id = i.id
LEFT JOIN sub_almacenes s ON i.sub_almacen_id = s.id
WHERE r.id = ?
AND (rd.surtido_almacen = 1 OR (COALESCE(rd.precio_cotizado, 0) = 0 AND rd.proveedor_id IS NULL AND rd.aprobado = 1))
ORDER BY COALESCE(i.nombre, rd.producto_nombre)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requisicion_id);
$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
while ($fila = $resultado->fetch_assoc()) {
    $productos[] = $fila;
}
$stmt->close();
$conn->close();

if (empty($productos)) {
    die('Error: No hay productos surtidos desde almacén en esta requisición.<br><br>
        <a href="ver-requisicion.php?id=' . $requisicion_id . '">Volver a la requisición</a>');
}

while (ob_get_level()) {
    ob_end_clean();
}

require_once 'lib/fpdf/fpdf.php';

// Función para convertir UTF-8 a ISO-8859-1 (compatible con FPDF)
function convertirTexto($texto) {
    if (empty($texto)) return '';
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
}

class PDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetMargins(10, 15, 10);

// Logo
$logoPath = __DIR__ . '/img/logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 10, 10, 18, 18);
}

// Encabezado
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, 'Hotel Playa Bonita', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, 'Av. Paseo Balboa No. 100, Puerto Penasco, Sonora', 0, 1, 'C');
$pdf->Cell(0, 5, 'CP: 83550 | Contacto: Departamento de Almacen', 0, 1, 'C');

// Folio y Fecha en esquina superior derecha
$pdf->SetY(10);
$pdf->SetX(140);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 5, 'Requisicion:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 5, $requisicion['folio'], 0, 1, 'L');

$pdf->SetX(140);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 5, 'Fecha:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 5, date('d/m/Y'), 0, 1, 'L');

$pdf->Ln(8);

// Título del documento
$pdf->SetFillColor(16, 185, 129); // Verde esmeralda
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'SALIDA DE ALMACEN - PRODUCTOS SURTIDOS', 1, 1, 'C', true);
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Información de la requisición
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, 'Datos de la Requisicion:', 0, 0, 'L');
$pdf->Cell(95, 6, 'Entregar a:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(25, 5, 'Folio:', 0, 0, 'L');
$pdf->Cell(70, 5, $requisicion['folio'], 0, 0, 'L');
$pdf->Cell(25, 5, 'Solicitante:', 0, 0, 'L');
$pdf->Cell(70, 5, convertirTexto($requisicion['solicitante'] ?? $requisicion['solicitante_nombre'] ?? 'N/A'), 0, 1, 'L');

$pdf->Cell(25, 5, 'Fecha Req.:', 0, 0, 'L');
$pdf->Cell(70, 5, date('d/m/Y', strtotime($requisicion['fecha_solicitud'])), 0, 0, 'L');
$pdf->Cell(25, 5, 'Departamento:', 0, 0, 'L');
$pdf->Cell(70, 5, convertirTexto($requisicion['departamento'] ?? 'No asignado'), 0, 1, 'L');

$pdf->Ln(5);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Tabla de productos
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(15, 7, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'CODIGO', 1, 0, 'C', true);
$pdf->Cell(75, 7, 'DESCRIPCION', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'UNIDAD', 1, 0, 'C', true);
$pdf->Cell(45, 7, 'ALMACEN ORIGEN', 1, 1, 'C', true);

// Filas de productos
$pdf->SetFont('Arial', '', 9);
foreach ($productos as $prod) {
    $pdf->Cell(15, 6, $prod['cantidad'], 1, 0, 'C');
    $pdf->Cell(30, 6, convertirTexto(substr($prod['codigo'], 0, 18)), 1, 0, 'C');
    $pdf->Cell(75, 6, convertirTexto(substr($prod['producto'], 0, 42)), 1, 0, 'L');
    $pdf->Cell(25, 6, convertirTexto($prod['unidad']), 1, 0, 'C');
    $pdf->Cell(45, 6, convertirTexto(substr($prod['almacen_origen'], 0, 25)), 1, 1, 'C');
}

// Filas vacías para mantener formato
$filasVacias = max(0, 10 - count($productos));
for ($i = 0; $i < $filasVacias; $i++) {
    $pdf->Cell(15, 6, '', 1, 0, 'C');
    $pdf->Cell(30, 6, '', 1, 0, 'C');
    $pdf->Cell(75, 6, '', 1, 0, 'L');
    $pdf->Cell(25, 6, '', 1, 0, 'C');
    $pdf->Cell(45, 6, '', 1, 1, 'C');
}

$pdf->Ln(5);

// Resumen
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(145, 6, '', 0, 0);
$pdf->Cell(25, 6, 'Total Items:', 0, 0, 'R');
$pdf->Cell(20, 6, count($productos), 0, 1, 'R');

// Observaciones
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Observaciones:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 5, 'Productos surtidos desde inventario interno para la requisicion ' . $requisicion['folio'] . '. La salida fue procesada automaticamente al cotizar la requisicion.');

// Firmas
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(63, 5, '_______________________________', 0, 0, 'C');
$pdf->Cell(64, 5, '_______________________________', 0, 0, 'C');
$pdf->Cell(63, 5, '', 0, 1, 'C');
$pdf->Cell(63, 5, 'Almacen (Entrega)', 0, 0, 'C');
$pdf->Cell(64, 5, 'Solicitante (Recibe)', 0, 0, 'C');
$pdf->Cell(63, 5, '', 0, 1, 'C');

// Salida del PDF
$pdf->Output('I', 'Salida_Almacen_' . $requisicion['folio'] . '.pdf');
