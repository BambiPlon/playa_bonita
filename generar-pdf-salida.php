<?php
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

session_start();
require_once 'config/database.php';
require_once 'controllers/SalidaController.php';
require_once 'lib/fpdf/fpdf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getConnection();
$salidaController = new SalidaController($db);

// Obtener salidas - puede ser por ID individual o por folio de grupo
$salidas = [];
$folio_grupo = '';

if (isset($_GET['folio'])) {
    $folio_grupo = $_GET['folio'];
    $salidaModel = new Salida($db);
    $salidas = $salidaModel->obtenerPorFolioGrupo($folio_grupo);
    
    if (empty($salidas)) {
        $query = "SELECT s.*, p.nombre as producto_nombre, p.codigo as producto_codigo, p.unidad,
                  sa.nombre as sub_almacen_nombre, u.nombre_completo as usuario_nombre
                  FROM salidas_almacen s
                  INNER JOIN inventario p ON s.producto_id = p.id
                  INNER JOIN sub_almacenes sa ON s.sub_almacen_id = sa.id
                  INNER JOIN usuarios u ON s.usuario_id = u.id
                  WHERE s.folio LIKE ?
                  ORDER BY s.id ASC";
        $stmt = $db->prepare($query);
        $patron = $folio_grupo . '%';
        $stmt->bind_param("s", $patron);
        $stmt->execute();
        $result = $stmt->get_result();
        $salidas = $result->fetch_all(MYSQLI_ASSOC);
    }
} elseif (isset($_GET['id'])) {
    $salida_id = intval($_GET['id']);
    $salida = $salidaController->obtenerSalidaPorId($salida_id);
    if ($salida) {
        $salidas = [$salida];
        $folio_grupo = $salida['folio'];
    }
}

if (empty($salidas)) {
    ob_end_clean();
    die('Salida no encontrada');
}

ob_end_clean();

function convertirTexto($texto) {
    if (empty($texto)) return '';
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
}

class PDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

$primera_salida = $salidas[0];
$folio_display = $folio_grupo ?: $primera_salida['folio'];

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// === ENCABEZADO ===
// Titulo centrado
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, 'Hotel Playa Bonita', 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, 'Av. Paseo Balboa No. 100, Puerto Penasco, Sonora', 0, 1, 'C');
$pdf->Cell(0, 5, 'CP: 83550 | Contacto: Departamento de Almacen', 0, 1, 'C');

// Folio y Fecha en esquina superior derecha

// Info derecha - Requisicion y Fecha
$pdf->SetY(10);
$pdf->SetX(140);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 5, 'Requisicion:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 5, $folio_display, 0, 1, 'L');

$pdf->SetX(140);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 5, 'Fecha:', 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(25, 5, date('d/m/Y', strtotime($primera_salida['fecha_salida'])), 0, 1, 'C');
$pdf->Ln(8);



$pdf->Ln(5);

// === BANNER VERDE ===
$pdf->SetFillColor(34, 139, 34); // Verde
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'SALIDA DE ALMACEN - PRODUCTOS SURTIDOS', 0, 1, 'C', true);

$pdf->Ln(8);
$pdf->SetTextColor(0, 0, 0);



// === TABLA DE PRODUCTOS ===
// Encabezados de tabla
$pdf->SetFillColor(245, 245, 245);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(18, 8, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'CODIGO', 1, 0, 'C', true);
$pdf->Cell(70, 8, 'DESCRIPCION', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'UNIDAD', 1, 0, 'C', true);
$pdf->Cell(42, 8, 'ALMACEN ORIGEN', 1, 1, 'C', true);

// Filas de productos
$pdf->SetFont('Arial', '', 9);
foreach ($salidas as $salida) {
    $pdf->Cell(18, 7, $salida['cantidad'], 1, 0, 'C');
    $pdf->Cell(30, 7, $salida['producto_codigo'] ?? 'N/A', 1, 0, 'C');
    $pdf->Cell(70, 7, convertirTexto(substr($salida['producto_nombre'], 0, 40)), 1, 0, 'L');
    $pdf->Cell(30, 7, convertirTexto($salida['unidad'] ?? 'pieza'), 1, 0, 'C');
    $pdf->Cell(42, 7, convertirTexto($salida['sub_almacen_nombre']), 1, 1, 'C');
}

// Filas vacias para completar formato
$filas_vacias = max(0, 6 - count($salidas));
for ($i = 0; $i < $filas_vacias; $i++) {
    $pdf->Cell(18, 7, '', 1, 0, 'C');
    $pdf->Cell(30, 7, '', 1, 0, 'C');
    $pdf->Cell(70, 7, '', 1, 0, 'L');
    $pdf->Cell(30, 7, '', 1, 0, 'C');
    $pdf->Cell(42, 7, '', 1, 1, 'C');
}

$pdf->Ln(3);

// Total Items alineado a la derecha
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(148, 6, '', 0, 0);
$pdf->Cell(25, 6, 'Total Items:', 0, 0, 'R');
$pdf->Cell(17, 6, count($salidas), 0, 1, 'R');

$pdf->Ln(8);

// === OBSERVACIONES ===
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, 'Observaciones:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$motivo = $primera_salida['motivo'] ?? 'Productos surtidos desde inventario interno.';
$pdf->MultiCell(0, 5, convertirTexto($motivo));

// === FIRMAS ===
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 10);

// Lineas de firma
$pdf->Cell(95, 5, '_________________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '_________________________________', 0, 1, 'C');

$pdf->Ln(2);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, 'Almacen (Entrega)', 0, 0, 'C');
$pdf->Cell(95, 5, 'Solicitante (Recibe)', 0, 1, 'C');

$pdf->Output('I', 'Salida_' . $folio_display . '.pdf');
