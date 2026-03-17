<?php
require_once 'init.php';

$authController = new AuthController();

if ($authController->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        if ($authController->login($username, $password)) {
            header('Location: index.php');
            exit();
        } else {
            $error = 'Usuario o contrasena incorrectos';
        }
    } else {
        $error = 'Por favor complete todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Playa Bonita</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
    --blue:#2563eb;
    --blue-dark:#1d4ed8;
    --text:#0f172a;
    --muted:#64748b;
    --muted2:#94a3b8;
}

*{ box-sizing:border-box; }

body{
    margin:0;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
    font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial;

    background:
        radial-gradient(900px 500px at 50% 20%, rgba(37,99,235,0.14), transparent 60%),
        radial-gradient(900px 500px at 50% 85%, rgba(245,130,32,0.08), transparent 65%),
        linear-gradient(135deg, #f8fafc, #e5e7eb);
}

/* 🔹 MÁS DELGADO */
.login-wrapper{
    width:100%;
    max-width:460px; /* antes 600px */
    margin:0 auto;
}

.login-card{
    width:100%;
    padding:42px; /* antes 60px */
    border-radius:24px;
    background: rgba(255,255,255,0.95);
    border:1px solid rgba(15,23,42,0.06);

    box-shadow:
        0 -8px 20px rgba(15,23,42,0.05),
        0 18px 50px rgba(15,23,42,0.12);

    text-align:center;
}

/* LOGO */
.login-logo{
    margin-bottom:18px;
}
.login-logo img{
    max-width:220px; /* ligeramente más pequeño */
    height:auto;
}

/* TITULO */
.login-title{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 12px;
    border-radius:999px;
    background: linear-gradient(180deg, rgba(37,99,235,0.08), rgba(255,255,255,0));
    border:1px solid rgba(15,23,42,0.08);
    color: rgba(15,23,42,0.75);
    font-size:11px;
    letter-spacing:0.2em;
    text-transform:uppercase;
    font-weight:700;
    margin-bottom:26px;
}

/* FORM */
form{
    width:100%;
    max-width:380px; /* más estrecho */
    margin:0 auto;
    text-align:left;
}

/* Floating Field */
.field{
    margin-bottom:16px;
}
.field .control{
    position:relative;
}
.field .icon{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    font-size:14px;
    color: rgba(15,23,42,0.40);
}

.field input{
    width:100%;
    height:52px; /* más estilizado */
    padding:0 14px 0 42px;
    border-radius:16px;
    border:1px solid rgba(15,23,42,0.12);
    background: rgba(255,255,255,0.80);
    font-size:13px;
    transition: 200ms ease;
    outline:none;
    box-shadow: 0 4px 14px rgba(15,23,42,0.06);
}

.field input::placeholder{ color: transparent; }

.field label{
    position:absolute;
    left:42px;
    top:50%;
    transform: translateY(-50%);
    font-size:12px;
    color: rgba(15,23,42,0.50);
    font-weight:700;
    transition: 200ms ease;
    pointer-events:none;
}

.field input:focus{
    border-color: rgba(37,99,235,0.55);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
}

.field input:focus ~ label,
.field input:not(:placeholder-shown) ~ label{
    top:0;
    transform: translateY(-50%);
    background:#fff;
    padding:0 6px;
    font-size:11px;
    color: rgba(37,99,235,0.85);
}

/* BOTÓN */
.btn-login{
    width:100%;
    height:54px;
    border:none;
    border-radius:18px;
    background: linear-gradient(135deg,var(--blue),var(--blue-dark));
    color:white;
    font-weight:700;
    font-size:14px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    transition: 220ms ease;
    box-shadow: 0 16px 40px rgba(37,99,235,0.25);
    margin-top:6px;
}

.btn-login:hover{
    transform: translateY(-2px);
    box-shadow: 0 22px 55px rgba(37,99,235,0.32);
}

/* FOOTER */
.login-footer{
    margin-top:18px;
    font-size:12px;
    color: var(--muted2);
}
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <img src="assets/img/logo.png" alt="Playa Bonita Resort">
        </div>

        <div class="login-title">
            <i class="fa-solid fa-shield-halved"></i>
            Acceso al Sistema
        </div>

        <?php if ($error): ?>
            <div class="login-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="field">
                <div class="control">
                    <i class="fa-solid fa-user icon"></i>
                    <input type="text" name="username" placeholder=" " required autofocus>
                    <label>Usuario</label>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" name="password" placeholder=" " required>
                    <label>Contrasena</label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-right-to-bracket"></i>
                Iniciar Sesio
            </button>
        </form>

        <div class="login-footer">
            Playa Bonita Resort
        </div>

    </div>
</div>

</body>
</html>