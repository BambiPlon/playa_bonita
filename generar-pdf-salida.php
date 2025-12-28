<?php
error_reporting(0);
ini_set('display_errors', '0');
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/SalidaController.php';
require_once __DIR__ . '/lib/fpdf/fpdf.php';

// ================= Helpers =================
function pdf_text($s): string {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$s);
}

// ================= Auth =================
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = [
    'id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['user_username'] ?? '',
    'nombre' => $_SESSION['user_nombre'] ?? '',
    'rol' => $_SESSION['user_rol'] ?? '',
    'sub_almacen_id' => $_SESSION['user_sub_almacen_id'] ?? null
];

$salida_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$db = getConnection();
$salidaController = new SalidaController($db);

$salida = $salidaController->obtenerSalidaPorId($salida_id);

if (!$salida) {
    while (ob_get_level()) ob_end_clean();
    die('Salida no encontrada');
}

// Limpia cualquier output antes del PDF
while (ob_get_level()) ob_end_clean();

// ================= PDF =================
class PDF_Salida extends FPDF
{
    public function Header()
    {
        // Colores del login (azul)
        $primary = [41, 98, 255];

        // Logo en esquina
        $logo = __DIR__ . '/assets/img/logo.png';
        if (file_exists($logo)) {
            $this->Image($logo, 10, 10, 38); // x, y, ancho
        }

        // Título a la derecha
        $this->SetXY(55, 12);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor($primary[0], $primary[1], $primary[2]);
        $this->Cell(0, 10, pdf_text('SALIDA DE ALMACÉN'), 0, 1, 'R');

        // Línea separadora
        $this->Ln(6);
        $this->SetDrawColor(220, 225, 235);
        $this->Line(10, 32, 205, 32);

        $this->Ln(8);
        $this->SetTextColor(0, 0, 0);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 130, 140);
        $this->Cell(0, 10, pdf_text('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    // Chip de etiqueta
    public function LabelValue($label, $value, $wLabel = 35)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(60, 70, 80);
        $this->Cell($wLabel, 7, pdf_text($label), 0, 0, 'L');

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 7, pdf_text($value), 0, 1, 'L');
    }

    public function SectionTitle($title)
    {
        $primary = [41, 98, 255];
        $graybg = [240, 243, 248];

        $this->Ln(4);
        $this->SetFillColor($graybg[0], $graybg[1], $graybg[2]);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor($primary[0], $primary[1], $primary[2]);
        $this->Cell(0, 9, pdf_text($title), 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }
}

$pdf = new PDF_Salida('P', 'mm', 'Letter');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

// ======= Información General =======
$pdf->SectionTitle('Información General');

$pdf->LabelValue('Folio:', $salida['folio'] ?? '');
$pdf->LabelValue('Fecha:', !empty($salida['fecha_salida']) ? date('d/m/Y', strtotime($salida['fecha_salida'])) : '');
$pdf->LabelValue('Sub-Almacén:', $salida['sub_almacen_nombre'] ?? '');
$pdf->LabelValue('Usuario:', $salida['usuario_nombre'] ?? '');

// ======= Detalles del Producto =======
$pdf->SectionTitle('Detalles del Producto');

$pdf->LabelValue('Código:', $salida['producto_codigo'] ?? '');
$pdf->LabelValue('Producto:', $salida['producto_nombre'] ?? '');
$pdf->LabelValue('Cantidad:', trim(($salida['cantidad'] ?? '') . ' ' . ($salida['unidad'] ?? '')));
$pdf->LabelValue('Destino:', $salida['destino'] ?? '');

// ======= Motivo =======
$pdf->SectionTitle('Motivo');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);

// Cuadro tipo “card”
$pdf->SetDrawColor(220, 225, 235);
$pdf->SetFillColor(250, 251, 253);
$x = $pdf->GetX();
$y = $pdf->GetY();

$motivo = pdf_text($salida['motivo'] ?? '');
$pdf->MultiCell(0, 6, $motivo, 1, 'L', true);

$pdf->Ln(6);

// ======= Firmas =======
$pdf->SectionTitle('Firmas');

$pdf->Ln(12);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(95, 5, '_______________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '_______________________________', 0, 1, 'C');
$pdf->SetTextColor(100, 110, 120);
$pdf->Cell(95, 5, pdf_text('Entrega'), 0, 0, 'C');
$pdf->Cell(95, 5, pdf_text('Recibe'), 0, 1, 'C');

// Output
$folioSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string)($salida['folio'] ?? ''));
$pdf->Output('I', 'Salida_' . $folioSafe . '.pdf');
exit;
