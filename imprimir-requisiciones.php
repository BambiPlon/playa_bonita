<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function pdf_text($s): string {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$s);
}

/* ================= VALIDACIONES ================= */

if (!isset($_SESSION['user_id'])) {
    die('No hay sesión activa');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['requisiciones'])) {
    die('No se seleccionaron requisiciones');
}

$ids = array_values(array_filter(array_map('intval', $_POST['requisiciones'])));
if (empty($ids)) {
    die('Requisiciones inválidas');
}

/* ================= DEPENDENCIAS ================= */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/fpdf/fpdf.php';

$conn = getConnection();

/* ================= CONSULTA ================= */

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$sql = "
SELECT
    COALESCE(i.codigo, '') AS codigo,
    COALESCE(i.nombre, rd.producto_nombre) AS producto,
    COALESCE(p.nombre, 'Sin Proveedor') AS proveedor,
    rd.cantidad,
    rd.precio_cotizado
FROM requisicion_detalles rd
INNER JOIN requisiciones r 
    ON rd.requisicion_id = r.id
LEFT JOIN inventario i 
    ON rd.producto_id = i.id
LEFT JOIN proveedores p 
    ON rd.proveedor_id = p.id
WHERE r.id IN ($placeholders)
  AND LOWER(TRIM(r.estado)) = 'aprobada'
  AND rd.aprobado = 1
ORDER BY proveedor, producto
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();

/* ================= AGRUPAR POR PROVEEDOR ================= */

$proveedores = [];

while ($row = $res->fetch_assoc()) {
    $proveedores[$row['proveedor']][] = $row;
}

$stmt->close();

if (empty($proveedores)) {
    die('No hay productos aprobados para imprimir');
}

/* ================= PDF ================= */

class PDF_OC extends FPDF {

    function Header() {
        $logo = __DIR__ . '/assets/img/logo.png';
        if (file_exists($logo)) {
            $this->Image($logo, 10, 10, 40);
        }
        $this->Ln(20);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF_OC('P', 'mm', 'Letter');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 20);

/* ================= COLORES ================= */

$primary = [41, 98, 255];
$gray = [240, 243, 248];

$oc = 1;

/* ================= GENERAR OC POR PROVEEDOR ================= */

foreach ($proveedores as $proveedor => $items) {

    $pdf->AddPage();

    // Título
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->SetTextColor($primary[0], $primary[1], $primary[2]);
    $pdf->Cell(0, 10, pdf_text('ORDEN DE COMPRA'), 0, 1, 'R');

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');
    $pdf->Cell(0, 6, 'OC #: ' . str_pad($oc, 4, '0', STR_PAD_LEFT), 0, 1, 'R');

    $pdf->Ln(4);

    // Proveedor
    $pdf->SetFillColor($gray[0], $gray[1], $gray[2]);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 8, pdf_text('Proveedor: ' . $proveedor), 0, 1, 'L', true);

    $pdf->Ln(4);

    // Encabezado tabla
    $pdf->SetFillColor($primary[0], $primary[1], $primary[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $pdf->Cell(30, 8, 'CODIGO', 0, 0, 'C', true);
    $pdf->Cell(80, 8, 'DESCRIPCION', 0, 0, 'C', true);
    $pdf->Cell(20, 8, 'CANT', 0, 0, 'C', true);
    $pdf->Cell(30, 8, 'P/U', 0, 0, 'C', true);
    $pdf->Cell(30, 8, 'TOTAL', 0, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);

    $total = 0;

    foreach ($items as $it) {
        $sub = $it['cantidad'] * $it['precio_cotizado'];
        $total += $sub;

        $pdf->Cell(30, 7, $it['codigo'], 1);
        $pdf->Cell(80, 7, pdf_text($it['producto']), 1);
        $pdf->Cell(20, 7, $it['cantidad'], 1, 0, 'C');
        $pdf->Cell(30, 7, '$' . number_format($it['precio_cotizado'], 2), 1, 0, 'R');
        $pdf->Cell(30, 7, '$' . number_format($sub, 2), 1, 1, 'R');
    }

    $pdf->Ln(4);

    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(160, 8, 'TOTAL', 1, 0, 'R');
    $pdf->Cell(30, 8, '$' . number_format($total, 2), 1, 1, 'R');

    $oc++;
}

/* ================= SALIDA ================= */

while (ob_get_level()) {
    ob_end_clean();
}

$pdf->Output('I', 'Ordenes_Compra_' . date('Ymd_His') . '.pdf');
exit;
