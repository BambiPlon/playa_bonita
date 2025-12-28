<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$usuarioModel = new Usuario();
$errors = [];
$success = false;

// Obtener sub-almacenes
$conn = getConnection();
$sql = "SELECT * FROM sub_almacenes ORDER BY nombre";
$result = $conn->query($sql);
$sub_almacenes = [];
while($row = $result->fetch_assoc()) {
    $sub_almacenes[] = $row;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $sub_almacen_id = !empty($_POST['sub_almacen_id']) ? intval($_POST['sub_almacen_id']) : null;
    
    // Validaciones
    if (empty($username)) {
        $errors[] = "El nombre de usuario es obligatorio";
    } elseif ($usuarioModel->usernameExiste($username)) {
        $errors[] = "El nombre de usuario ya existe";
    }
    
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Las contraseñas no coinciden";
    }
    
    if (empty($nombre_completo)) {
        $errors[] = "El nombre completo es obligatorio";
    }
    
    if (empty($rol)) {
        $errors[] = "El rol es obligatorio";
    }
    
    // Si no hay errores, crear usuario
    if (empty($errors)) {
        $datos = [
            'username' => $username,
            'password' => $password,
            'nombre_completo' => $nombre_completo,
            'email' => $email,
            'rol' => $rol,
            'sub_almacen_id' => $sub_almacen_id
        ];
        
        $nuevo_id = $usuarioModel->crear($datos);
        
        if ($nuevo_id) {
            $_SESSION['success'] = "Usuario creado exitosamente";
            header('Location: usuarios.php');
            exit;
        } else {
            $errors[] = "Error al crear el usuario";
        }
    }
}

require 'includes/header.php';
require 'views/agregar-usuario.view.php';
require 'includes/footer.php';
?>
