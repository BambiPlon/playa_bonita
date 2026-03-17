<?php
require_once 'config/database.php';
session_start();

header('Content-Type: application/json');

$conn = getConnection();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$abreviatura = trim($_POST['abreviatura'] ?? '');

if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'El nombre de la unidad es requerido']);
    exit;
}

// Normalizar el nombre a minúsculas
$nombre = strtolower($nombre);

// Verificar si ya existe
$stmt = $conn->prepare("SELECT id FROM unidades WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'success' => true, 
        'unidad_id' => $row['id'],
        'unidad_nombre' => $nombre,
        'message' => 'La unidad ya existe'
    ]);
    $stmt->close();
    exit;
}
$stmt->close();

// Generar abreviatura si no se proporcionó
if (empty($abreviatura)) {
    $abreviatura = substr($nombre, 0, 3);
}

// Insertar nueva unidad
$stmt = $conn->prepare("INSERT INTO unidades (nombre, abreviatura) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $abreviatura);

if ($stmt->execute()) {
    $unidad_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'unidad_id' => $unidad_id,
        'unidad_nombre' => $nombre,
        'message' => 'Unidad agregada exitosamente'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la unidad: ' . $conn->error]);
}

$stmt->close();
// Conexión manejada por singleton - no cerrar aquí
