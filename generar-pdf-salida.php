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

$salida_id = $_GET['id'] ?? 0;

$db = getConnection();
$salidaController = new SalidaController($db);
$salida = $salidaController->obtenerSalidaPorId($salida_id);

if (!$salida) {
    ob_end_clean();
    die('Salida no encontrada');
}
ob_end_clean();

function pdfText($str) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', (string)$str);
}

class PDF extends FPDF
{
    public $orgNombre = 'Hotel Playa Bonita';
    public $orgLinea1 = 'Av. Paseo Balboa No. 100, Puerto Penasco, Sonora';
    public $orgLinea2 = 'CP: 83550 | Contacto: Departamento de Compras';
    public $mutedColor = [80,80,80];

    function Header()
    {
        $this->SetMargins(12, 12, 12);

        // Encabezado centrado
        $this->SetXY(12, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, pdfText($this->orgNombre), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor($this->mutedColor[0], $this->mutedColor[1], $this->mutedColor[2]);
        $this->Cell(0, 5, pdfText($this->orgLinea1), 0, 1, 'C');
        $this->Cell(0, 5, pdfText($this->orgLinea2), 0, 1, 'C');
        $this->SetTextColor(0,0,0);

        // Título en caja (si la quieres sin caja, quita Rect)
        $badgeW = 64; $badgeH = 8;
        $badgeX = 210 - 12 - $badgeW; // Letter
        $badgeY = 13;
        $this->Rect($badgeX, $badgeY, $badgeW, $badgeH);
        $this->SetXY($badgeX, $badgeY + 1.3);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($badgeW, 5, pdfText('SALIDA DE ALMACÉN'), 0, 0, 'C');

        // Separador
        $this->Ln(10);
        $this->Line(12, 36, 204, 36);
        $this->Ln(6);
    }

    function muted($on = true)
    {
        if ($on) $this->SetTextColor($this->mutedColor[0], $this->mutedColor[1], $this->mutedColor[2]);
        else $this->SetTextColor(0,0,0);
    }

    function labelValue($x, $y, $label, $value, $labelW = 22)
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell($labelW, 5, pdfText($label), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, pdfText($value), 0, 1, 'L');
    }

    function sectionTitle($x, $y, $title)
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 5, pdfText($title), 0, 1, 'L');
    }

    function tableHeader($x, $y, $cols)
    {
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 9);
        foreach ($cols as $c) {
            $this->Cell($c['w'], 7, pdfText($c['t']), 1, 0, 'C');
        }
        $this->Ln();
    }

    // Cuenta líneas como MultiCell
    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w==0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if ($nb>0 && $s[$nb-1]=="\n") $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i<$nb) {
            $c = $s[$i];
            if ($c=="\n") {
                $i++; $sep=-1; $j=$i; $l=0; $nl++;
                continue;
            }
            if ($c==' ') $sep = $i;
            $l += $cw[$c] ?? 0;
            if ($l>$wmax) {
                if ($sep==-1) {
                    if ($i==$j) $i++;
                } else {
                    $i = $sep+1;
                }
                $sep=-1; $j=$i; $l=0; $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    /**
     * FILA DE TABLA CORRECTA (SIN LÍNEAS RARAS)
     * Clave: NO usar GetX() después de MultiCell.
     * Guardamos $xCell antes de imprimir cada celda.
     */
    function Row($cols, $data, $x, $minH = 10)
    {
        // altura máxima por wrap
        $maxLines = 1;
        for ($i=0; $i<count($cols); $i++) {
            $txt = pdfText($data[$i] ?? '');
            $lines = $this->NbLines($cols[$i]['w'], $txt);
            if ($lines > $maxLines) $maxLines = $lines;
        }
        $h = max($minH, 5 * $maxLines);

        $y = $this->GetY();
        $this->SetXY($x, $y);

        for ($i=0; $i<count($cols); $i++) {
            $w = $cols[$i]['w'];
            $align = $cols[$i]['a'] ?? 'L';
            $txt = pdfText($data[$i] ?? '');

            // Guardar X real de esta celda
            $xCell = $this->GetX();

            // Borde de celda
            $this->Rect($xCell, $y, $w, $h);

            // Texto dentro (con padding vertical)
            $this->SetXY($xCell, $y + 2);
            $this->MultiCell($w, 5, $txt, 0, $align);

            // Volver al tope de la fila y avanzar a la siguiente celda
            $this->SetXY($xCell + $w, $y);
        }

        // Siguiente fila
        $this->SetXY($x, $y + $h);
    }
}

$pdf = new PDF('P', 'mm', 'Letter');
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

// ===== Datos =====
$folio     = $salida['folio'] ?? ('SAL-' . (int)$salida_id);
$fecha     = isset($salida['fecha_salida']) ? date('d/m/Y', strtotime($salida['fecha_salida'])) : date('d/m/Y');

$subalmacen = $salida['sub_almacen_nombre'] ?? '';
$usuario    = $salida['usuario_nombre'] ?? '';
$destino    = $salida['destino'] ?? '';
$motivo     = trim((string)($salida['motivo'] ?? ''));

$codigo   = $salida['producto_codigo'] ?? '';
$producto = $salida['producto_nombre'] ?? '';
$cantidad = (string)($salida['cantidad'] ?? '');
$unidad   = (string)($salida['unidad'] ?? '');

// ===== Info derecha =====
$pdf->labelValue(130, 40, 'No. Salida:', $folio, 22);
$pdf->labelValue(130, 45, 'Fecha:', $fecha, 22);

// ===== Bloques sin contornos =====
$leftX  = 14;
$rightX = 112;
$topY   = 58;

$pdf->sectionTitle($leftX, $topY, 'Datos del Producto:');
$pdf->muted(true);
$pdf->SetXY($leftX, $topY + 6);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(90, 5, pdfText(
    "Código: {$codigo}\n" .
    "Descripción: {$producto}\n" .
    "Unidad: {$unidad}"
), 0, 'L');
$pdf->muted(false);

$pdf->sectionTitle($rightX, $topY, 'Entregar en:');
$pdf->muted(true);
$pdf->SetXY($rightX, $topY + 6);
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(90, 5, pdfText(
    "Sub-Almacén: {$subalmacen}\n" .
    "Área/Destino: {$destino}\n" .
    "Usuario: {$usuario}"
), 0, 'L');
$pdf->muted(false);

// Separador
$pdf->Line(12, 88, 204, 88);

// ===== Tabla (ya sin error) =====
$cols = [
    ['t'=>'CANT.',       'w'=>14, 'a'=>'C'],
    ['t'=>'CÓDIGO',      'w'=>38, 'a'=>'L'],
    ['t'=>'DESCRIPCIÓN', 'w'=>84, 'a'=>'L'],
    ['t'=>'UNIDAD',      'w'=>22, 'a'=>'C'],
    ['t'=>'DESTINO',     'w'=>34, 'a'=>'L'],
];

$pdf->tableHeader(12, 94, $cols);
$pdf->SetFont('Arial', '', 9);

// fila con datos
$pdf->Row($cols, [$cantidad, $codigo, $producto, $unidad, $destino], 12, 10);

// filas vacías
$emptyRows = 8;
for ($i=0; $i<$emptyRows; $i++) {
    $pdf->Row($cols, ['', '', '', '', ''], 12, 10);
}

// ===== Motivo =====
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, pdfText('Motivo / Observaciones:'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->muted(true);
$pdf->MultiCell(0, 5, pdfText($motivo), 0, 'L');
$pdf->muted(false);

// ===== Firmas (solo entrega/recibe) =====
$pdf->Ln(18);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, '_______________________________', 0, 0, 'C');
$pdf->Cell(95, 5, '_______________________________', 0, 1, 'C');
$pdf->Cell(95, 5, pdfText('Entrega'), 0, 0, 'C');
$pdf->Cell(95, 5, pdfText('Recibe'), 0, 1, 'C');

$pdf->Output('I', 'Salida_' . preg_replace('/[^A-Za-z0-9_\-]/', '', (string)$folio) . '.pdf');