<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Verificar que se proporcione un ID
if (!isset($_GET['id'])) {
    header('Location: usuarios.php');
    exit;
}

$usuario_id = intval($_GET['id']);
$usuarioModel = new Usuario();
$errors = [];
$success = false;

// Obtener datos del usuario
$usuario = $usuarioModel->obtenerPorId($usuario_id);

if (!$usuario) {
    $_SESSION['error'] = "Usuario no encontrado";
    header('Location: usuarios.php');
    exit;
}

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
    $nombre_completo = trim($_POST['nombre_completo']);
    $email = trim($_POST['email']);
    $rol = $_POST['rol'];
    $sub_almacen_id = !empty($_POST['sub_almacen_id']) ? intval($_POST['sub_almacen_id']) : null;
    $activo = isset($_POST['activo']) ? 1 : 0;
    $cambiar_password = !empty($_POST['password']);
    
    // Validaciones
    if (empty($username)) {
        $errors[] = "El nombre de usuario es obligatorio";
    } elseif ($username !== $usuario['username'] && $usuarioModel->usernameExiste($username)) {
        $errors[] = "El nombre de usuario ya existe";
    }
    
    if (empty($nombre_completo)) {
        $errors[] = "El nombre completo es obligatorio";
    }
    
    if (empty($rol)) {
        $errors[] = "El rol es obligatorio";
    }
    
    // Validar contraseña si se está cambiando
    if ($cambiar_password) {
        $password = trim($_POST['password']);
        $password_confirm = trim($_POST['password_confirm']);
        
        if (strlen($password) < 6) {
            $errors[] = "La contraseña debe tener al menos 6 caracteres";
        }
        
        if ($password !== $password_confirm) {
            $errors[] = "Las contraseñas no coinciden";
        }
    }
    
    // Si no hay errores, actualizar usuario
    if (empty($errors)) {
        $datos = [
            'username' => $username,
            'nombre_completo' => $nombre_completo,
            'email' => $email,
            'rol' => $rol,
            'sub_almacen_id' => $sub_almacen_id,
            'activo' => $activo
        ];
        
        if ($cambiar_password) {
            $datos['password'] = $password;
        }
        
        $resultado = $usuarioModel->actualizar($usuario_id, $datos);
        
        if ($resultado) {
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $usuario_id) {
                $_SESSION['user_nombre'] = $nombre_completo;
                $_SESSION['user_rol'] = $rol;
                
                // Obtener el nombre del sub-almacén si existe
                if ($sub_almacen_id) {
                    $sub_almacen_sql = "SELECT nombre FROM sub_almacenes WHERE id = ?";
                    $stmt = $conn->prepare($sub_almacen_sql);
                    $stmt->bind_param("i", $sub_almacen_id);
                    $stmt->execute();
                    $sub_result = $stmt->get_result();
                    if ($sub_row = $sub_result->fetch_assoc()) {
                        $_SESSION['user_sub_almacen_id'] = $sub_almacen_id;
                        $_SESSION['user_sub_almacen_nombre'] = $sub_row['nombre'];
                    }
                } else {
                    $_SESSION['user_sub_almacen_id'] = null;
                    $_SESSION['user_sub_almacen_nombre'] = null;
                }
            }
            
            $_SESSION['success'] = "Usuario actualizado exitosamente";
            header('Location: usuarios.php');
            exit;
        } else {
            $errors[] = "Error al actualizar el usuario";
        }
    }
}

require 'includes/header.php';
require 'views/editar-usuario.view.php';
require 'includes/footer.php';
?>
