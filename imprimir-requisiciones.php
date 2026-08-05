<?php
// Activar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Error: Acceso no autorizado');
}

// Obtener IDs de requisiciones desde POST o GET
$requisiciones_ids = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requisiciones'])) {
    // Desde formulario POST (selección múltiple)
    $requisiciones_ids = array_map('intval', $_POST['requisiciones']);
} elseif (isset($_GET['ids'])) {
    // Desde GET con múltiples IDs separados por coma
    $requisiciones_ids = array_map('intval', explode(',', $_GET['ids']));
} elseif (isset($_GET['id'])) {
    // Desde GET con un solo ID
    $requisiciones_ids = [intval($_GET['id'])];
}

if (empty($requisiciones_ids)) {
    die('Error: No se especificaron requisiciones. GET: ' . print_r($_GET, true) . ' POST: ' . print_r($_POST, true));
}

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
$conn->set_charset("utf8");

$placeholders = implode(',', array_fill(0, count($requisiciones_ids), '?'));
$tipos = str_repeat('i', count($requisiciones_ids));

// Solo incluir productos que NO fueron surtidos desde almacén (surtido_almacen = 0 o NULL)
$sql = "SELECT 
    COALESCE(i.codigo, CONCAT('PROD-', LPAD(rd.id, 4, '0'))) as codigo,
    COALESCE(i.nombre, rd.producto_nombre, 'Producto') as producto,
    COALESCE(rd.unidad, i.unidad, 'pieza') as unidad,
    COALESCE(p.nombre, 'Sin Proveedor') as proveedor,
    COALESCE(p.contacto, '') as proveedor_contacto,
    COALESCE(p.telefono, '') as proveedor_telefono,
    COALESCE(p.email, '') as proveedor_email,
    COALESCE(p.direccion, '') as proveedor_direccion,
    COALESCE(p.rfc, '') as proveedor_rfc,
    rd.cantidad,
    COALESCE(rd.precio_cotizado, 0) as precio,
    r.folio,
    r.fecha_solicitud,
    r.porcentaje_iva,
    p.id as proveedor_id
FROM requisicion_detalles rd
INNER JOIN requisiciones r ON rd.requisicion_id = r.id
LEFT JOIN inventario i ON rd.producto_id = i.id
LEFT JOIN proveedores p ON rd.proveedor_id = p.id
WHERE r.id IN ($placeholders)
AND r.estado IN ('aprobada', 'completada')
AND rd.aprobado = 1
AND (rd.surtido_almacen = 0 OR rd.surtido_almacen IS NULL)
AND NOT (COALESCE(rd.precio_cotizado, 0) = 0 AND rd.proveedor_id IS NULL)
ORDER BY COALESCE(p.nombre, 'Sin Proveedor'), COALESCE(i.nombre, rd.producto_nombre)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    $conn->close();
    die('Error: No se pudo preparar la consulta');
}

$stmt->bind_param($tipos, ...$requisiciones_ids);
$stmt->execute();
$resultado = $stmt->get_result();

// Agrupar productos por proveedor y obtener porcentaje IVA
$proveedores = [];
$porcentajesIVA = [];
$datosProveedores = [];
$foliosProveedores = [];
while ($fila = $resultado->fetch_assoc()) {
    $prov = $fila['proveedor'];
    
    if (!isset($proveedores[$prov])) {
        $proveedores[$prov] = [];
        $porcentajesIVA[$prov] = isset($fila['porcentaje_iva']) && $fila['porcentaje_iva'] > 0 ? floatval($fila['porcentaje_iva']) : 16;
        $datosProveedores[$prov] = [
            'contacto' => $fila['proveedor_contacto'],
            'telefono' => $fila['proveedor_telefono'],
            'email' => $fila['proveedor_email'],
            'direccion' => $fila['proveedor_direccion'],
            'rfc' => $fila['proveedor_rfc']
        ];
        $foliosProveedores[$prov] = [];
    }
    
    // Recopilar folios unicos por proveedor
    if (!empty($fila['folio']) && !in_array($fila['folio'], $foliosProveedores[$prov])) {
        $foliosProveedores[$prov][] = $fila['folio'];
    }
    
    $proveedores[$prov][] = $fila;
}

$stmt->close();

// Verificar que haya datos ANTES de cerrar la conexión
if (empty($proveedores)) {
    // Depuración: verificar qué está pasando
    $debug_sql = "SELECT r.id, r.estado, rd.id as detalle_id, rd.aprobado, rd.precio_cotizado 
                  FROM requisiciones r 
                  LEFT JOIN requisicion_detalles rd ON r.id = rd.requisicion_id 
                  WHERE r.id IN ($placeholders)";
    $debug_stmt = $conn->prepare($debug_sql);
    $debug_stmt->bind_param($tipos, ...$requisiciones_ids);
    $debug_stmt->execute();
    $debug_result = $debug_stmt->get_result();
    $debug_data = [];
    while ($row = $debug_result->fetch_assoc()) {
        $debug_data[] = $row;
    }
    $debug_stmt->close();
    $conn->close();
    
    die('Error: No hay datos para imprimir.<br><br>
        <strong>IDs solicitados:</strong> ' . implode(', ', $requisiciones_ids) . '<br><br>
        <strong>Datos de depuración:</strong><pre>' . print_r($debug_data, true) . '</pre><br>
        <strong>Nota:</strong> Verifique que la requisición esté en estado "aprobada" o "completada" y que los productos tengan aprobado = 1');
}

$conn->close();

while (ob_get_level()) {
    ob_end_clean();
}

require_once 'lib/fpdf/fpdf.php';

// Función para convertir UTF-8 a ISO-8859-1 (compatible con FPDF)
function convertirTexto($texto) {
    if (empty($texto)) return '';
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $texto);
}

// Clase PDF personalizada sin Header automático
class PDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
    
    function DibujarEncabezado($nombreProveedor, $numeroOrden, $datosProveedor, $porcentajeIVA) {
        // Logo
        $logoPath = __DIR__ . '/assets/img/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, $this->GetY(), 18, 18);
        }
        
        // Encabezado principal (desplazado a la derecha del logo)
        $xTexto = file_exists($logoPath) ? 30 : 10;
        $this->SetX($xTexto);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(100 - ($xTexto - 10), 8, 'Hotel Playa Bonita', 0, 0, 'L');
        
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(90, 8, 'ORDEN DE COMPRA', 0, 1, 'R');
        
        $this->SetFont('Arial', '', 9);
        $this->SetX($xTexto);
        $this->Cell(100 - ($xTexto - 10), 5, 'Av. Paseo Balboa No. 100, Puerto Penasco, Sonora', 0, 0, 'L');
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(45, 5, 'No. Orden: ', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, $numeroOrden, 0, 1, 'L');
        
        $this->SetFont('Arial', '', 9);
        $this->SetX($xTexto);
        $this->Cell(100 - ($xTexto - 10), 5, 'CP: 83550 | Contacto: Departamento de Compras', 0, 0, 'L');
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(45, 5, 'Fecha: ', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(45, 5, date('d/m/Y'), 0, 1, 'L');
        
        $this->Ln(5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(3);
        
        // Sección Proveedor y Entregar en
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(95, 6, 'Proveedor:', 0, 0, 'L');
        $this->Cell(95, 6, 'Entregar en:', 0, 1, 'L');
        
        // Nombre del proveedor
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(95, 5, convertirTexto($nombreProveedor), 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->Cell(95, 5, 'Av. Paseo Balboa No. 100', 0, 1, 'L');
        
        // Contacto
        $this->SetFont('Arial', '', 9);
        if (!empty($datosProveedor['contacto'])) {
            $this->Cell(95, 5, 'Contacto: ' . convertirTexto($datosProveedor['contacto']), 0, 0, 'L');
        } else {
            $this->Cell(95, 5, '', 0, 0, 'L');
        }
        $this->Cell(95, 5, 'Hotel Playa Bonita', 0, 1, 'L');
        
        // Teléfono
        if (!empty($datosProveedor['telefono'])) {
            $this->Cell(95, 5, 'Tel: ' . $datosProveedor['telefono'], 0, 0, 'L');
        } else {
            $this->Cell(95, 5, '', 0, 0, 'L');
        }
        $this->Cell(95, 5, 'Almacen General', 0, 1, 'L');
        
        // Email
        if (!empty($datosProveedor['email'])) {
            $this->Cell(95, 5, 'Email: ' . $datosProveedor['email'], 0, 0, 'L');
        } else {
            $this->Cell(95, 5, '', 0, 0, 'L');
        }
        $this->Cell(95, 5, 'Area de Mantenimiento', 0, 1, 'L');
        
        // Dirección
        if (!empty($datosProveedor['direccion'])) {
            $this->Cell(95, 5, 'Dir: ' . convertirTexto(substr($datosProveedor['direccion'], 0, 50)), 0, 0, 'L');
        } else {
            $this->Cell(95, 5, '', 0, 0, 'L');
        }
        $this->Cell(95, 5, '', 0, 1, 'L');
        
        // RFC
        if (!empty($datosProveedor['rfc'])) {
            $this->Cell(95, 5, 'RFC: ' . $datosProveedor['rfc'], 0, 1, 'L');
        }
        
        $this->Ln(3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(3);
    }
}

// Crear instancia de PDF
$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetMargins(10, 15, 10);

// Generar una página por cada proveedor
foreach ($proveedores as $nombreProveedor => $productos) {
    $porcentajeIVA = $porcentajesIVA[$nombreProveedor];
    $datosProveedor = $datosProveedores[$nombreProveedor];
    
    // Usar folios de las requisiciones como numero de orden
    $folios = isset($foliosProveedores[$nombreProveedor]) ? $foliosProveedores[$nombreProveedor] : [];
    $numeroOrden = !empty($folios) ? implode(', ', $folios) : '0001';
    
    $pdf->AddPage();
    
    // Dibujar encabezado manualmente con los datos del proveedor
    $pdf->DibujarEncabezado($nombreProveedor, $numeroOrden, $datosProveedor, $porcentajeIVA);
    
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(12, 7, 'CANT.', 1, 0, 'C', true);
    $pdf->Cell(25, 7, 'CODIGO', 1, 0, 'C', true);
    $pdf->Cell(58, 7, 'DESCRIPCION', 1, 0, 'C', true);
    $pdf->Cell(18, 7, 'UNIDAD', 1, 0, 'C', true);
    $pdf->Cell(27, 7, 'PRECIO S/IVA', 1, 0, 'C', true);
    $pdf->Cell(27, 7, 'PRECIO C/IVA', 1, 0, 'C', true);
    $pdf->Cell(23, 7, 'IMPORTE', 1, 1, 'C', true);
    
    // Filas de productos
    $pdf->SetFont('Arial', '', 8);
    $totalSinIVA = 0;
    
    foreach ($productos as $prod) {
        $precioSinIVA = floatval($prod['precio']);
        $precioConIVA = $precioSinIVA * (1 + ($porcentajeIVA / 100));
        $subtotal = $precioSinIVA * $prod['cantidad'];
        $totalSinIVA += $subtotal;
        
        $pdf->Cell(12, 6, $prod['cantidad'], 1, 0, 'C');
        $pdf->Cell(25, 6, convertirTexto(substr($prod['codigo'], 0, 15)), 1, 0, 'C');
        $pdf->Cell(58, 6, convertirTexto(substr($prod['producto'], 0, 35)), 1, 0, 'L');
        $pdf->Cell(18, 6, convertirTexto($prod['unidad']), 1, 0, 'C');
        $pdf->Cell(27, 6, '$' . number_format($precioSinIVA, 2), 1, 0, 'R');
        $pdf->Cell(27, 6, '$' . number_format($precioConIVA, 2), 1, 0, 'R');
        $pdf->Cell(23, 6, '$' . number_format($subtotal, 2), 1, 1, 'R');
    }
    
    // Filas vacías para mantener formato
    $filasVacias = max(0, 10 - count($productos));
    for ($i = 0; $i < $filasVacias; $i++) {
        $pdf->Cell(12, 6, '', 1, 0, 'C');
        $pdf->Cell(25, 6, '', 1, 0, 'C');
        $pdf->Cell(58, 6, '', 1, 0, 'L');
        $pdf->Cell(18, 6, '', 1, 0, 'C');
        $pdf->Cell(27, 6, '', 1, 0, 'R');
        $pdf->Cell(27, 6, '', 1, 0, 'R');
        $pdf->Cell(23, 6, '', 1, 1, 'R');
    }
    
    // Calcular IVA y Total
    $montoIVA = $totalSinIVA * ($porcentajeIVA / 100);
    $totalConIVA = $totalSinIVA + $montoIVA;
    
    // Totales
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(140, 6, '', 0, 0);
    $pdf->Cell(27, 6, 'Subtotal:', 0, 0, 'R');
    $pdf->Cell(23, 6, '$' . number_format($totalSinIVA, 2), 0, 1, 'R');
    
    $pdf->Cell(140, 6, '', 0, 0);
    $pdf->Cell(27, 6, 'Delivery:', 0, 0, 'R');
    $pdf->Cell(23, 6, '$0.00', 0, 1, 'R');
    
    $pdf->Cell(140, 6, '', 0, 0);
    $pdf->Cell(27, 6, 'IVA (' . $porcentajeIVA . '%):', 0, 0, 'R');
    $pdf->Cell(23, 6, '$' . number_format($montoIVA, 2), 0, 1, 'R');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(140, 6, '', 0, 0);
    $pdf->Cell(27, 6, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(23, 6, '$' . number_format($totalConIVA, 2), 0, 1, 'R');
    

    
    $pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
// Posición actual
$yFirma = $pdf->GetY();

// Ruta de la firma
$firmaPath = __DIR__ . '/assets/img/firma.png';

// Insertar imagen de firma
if (file_exists($firmaPath)) {
    $pdf->Image($firmaPath, 50, $yFirma, 60); // X, Y, ancho
}

// Línea debajo de la firma (opcional, se ve pro)
$pdf->SetY($yFirma + 20);
$pdf->SetX(50);
$pdf->Cell(60, 5, '', 'T', 1, 'C');

// Nombre debajo (opcional)
$pdf->SetX(50);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(60, 5, 'Autorizado', 0, 1, 'C');
}

// Salida del PDF
$pdf->Output('I', 'Orden_Compra_' . date('Ymd') . '.pdf');
