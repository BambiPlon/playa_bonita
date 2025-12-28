<?php
require_once 'init.php';

$authController = new AuthController();

// Si ya está logueado, redirigir al dashboard
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
            $error = 'Usuario o contraseña incorrectos';
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
<title>Login - Sistema de Inventario</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
  --navy-1:#0a192f;
  --navy-2:#1d3557;
  --navy-3:#2c4a6d;
  --cyan:#00BCD4;

  --panel:#f8fafc;
  --text:#0f172a;
  --muted:#64748b;
  --danger:#F44336;
}

*{margin:0;padding:0;box-sizing:border-box}

body{
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:24px;

  background:
    radial-gradient(1200px 600px at 20% 20%, rgba(0,188,212,.22), transparent 55%),
    radial-gradient(900px 500px at 80% 80%, rgba(44,74,109,.35), transparent 55%),
    linear-gradient(135deg,var(--navy-1),var(--navy-2),var(--navy-3));
}

.shell{
  width:100%;
  max-width:1100px;
  filter:drop-shadow(0 28px 80px rgba(0,0,0,.45));
  animation:fadeIn .55s ease-out;
}

@keyframes fadeIn{
  from{opacity:0;transform:translateY(12px)}
  to{opacity:1;transform:translateY(0)}
}

.login-card{
  display:grid;
  grid-template-columns:1fr 1fr;
  border-radius:22px;
  overflow:hidden;
  background:#fff;
  box-shadow:0 40px 90px rgba(0,0,0,.45);
}

/* PANEL IZQUIERDO */
.brand-panel{
  position:relative;
  background:linear-gradient(135deg,#1b3556 0%,#2c4a6d 55%,#16324f 100%);
  display:flex;
  align-items:center;
  justify-content:center;
  padding:64px;
  color:#fff;
}

.brand-panel::before{
  content:"";
  position:absolute;
  inset:-45%;
  background:radial-gradient(circle, rgba(0,188,212,.18) 0%, transparent 60%);
  animation:pulse 8s ease-in-out infinite;
}

@keyframes pulse{
  0%,100%{transform:scale(1);opacity:.55}
  50%{transform:scale(1.12);opacity:.85}
}

.brand-content{
  position:relative;
  z-index:1;
  text-align:center;
}

/* LOGO GRANDE SIN CUADRO */
.brand-logo{
  width:300px;
  margin:0 auto 26px;
  animation:floaty 3.2s ease-in-out infinite;
}

@keyframes floaty{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-12px)}
}

.brand-logo img{
  width:100%;
  object-fit:contain;
  filter:drop-shadow(0 18px 32px rgba(0,0,0,.35));
  user-select:none;
}

.brand-title{
  font-size:30px;
  font-weight:900;
  margin-bottom:8px;
}

.brand-subtitle{
  font-size:13px;
  opacity:.9;
}

/* PANEL DERECHO */
.form-panel{
  background:var(--panel);
  padding:64px;
  display:flex;
  align-items:center;
  justify-content:center;
}

.form-wrap{
  width:100%;
  max-width:430px;
}

.form-header h2{
  font-size:26px;
  font-weight:900;
  color:var(--text);
}

.form-header p{
  font-size:13px;
  color:var(--muted);
  margin:6px 0 22px;
}

.error-message{
  background:rgba(244,67,54,.1);
  color:var(--danger);
  padding:12px 14px;
  border-radius:12px;
  margin-bottom:16px;
  display:flex;
  gap:10px;
  font-size:13px;
}

.field{margin-bottom:16px}

label{
  font-size:12px;
  font-weight:800;
  color:#334155;
  margin-bottom:8px;
  display:block;
}

.input-wrap{position:relative}

.input-icon{
  position:absolute;
  left:14px;
  top:50%;
  transform:translateY(-50%);
  color:#94a3b8;
}

input{
  width:100%;
  padding:14px 46px 14px 42px;
  border-radius:12px;
  border:2px solid #e2e8f0;
  font-size:14px;
}

input:focus{
  outline:none;
  border-color:var(--navy-2);
  box-shadow:0 0 0 4px rgba(29,53,87,.15);
}

.toggle-password{
  position:absolute;
  right:10px;
  top:50%;
  transform:translateY(-50%);
  width:36px;
  height:36px;
  border:none;
  background:#fff;
  border-radius:10px;
  cursor:pointer;
}

.btn-login{
  width:100%;
  border-radius:999px;
  border:none;
  padding:14px;
  font-weight:900;
  background:linear-gradient(135deg,var(--navy-2),var(--navy-3));
  color:#fff;
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
  box-shadow:0 18px 35px rgba(29,53,87,.35);
  cursor:pointer;
  margin-top:10px;
}

.test-box{
  margin-top:18px;
  background:#eef4ff;
  padding:16px;
  border-radius:16px;
  font-size:12px;
}

.test-box strong{color:#0f172a}

@media(max-width:900px){
  .login-card{grid-template-columns:1fr}
  .brand-logo{width:180px}
}
</style>
</head>

<body>
<div class="shell">
  <div class="login-card">

    <!-- IZQUIERDA -->
    <section class="brand-panel">
      <div class="brand-content">
        <div class="brand-logo">
          <img src="assets/img/Logo playabonita.png" alt="Playa Bonita Resorts">
        </div>
      </div>
    </section>

    <!-- DERECHA -->
    <section class="form-panel">
      <div class="form-wrap">
        <div class="form-header">
          <h2>Bienvenido</h2>
          <p>Ingresa tus credenciales para continuar</p>
        </div>

        <?php if($error): ?>
          <div class="error-message">
            <i class="fas fa-circle-exclamation"></i>
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="field">
            <label>Usuario</label>
            <div class="input-wrap">
              <i class="fas fa-user input-icon"></i>
              <input type="text" name="username" placeholder="Ingrese su usuario" required>
            </div>
          </div>

          <div class="field">
            <label>Contraseña</label>
            <div class="input-wrap">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
              <button type="button" class="toggle-password" id="togglePassword">
                <i class="fas fa-eye" id="toggleIcon"></i>
              </button>
            </div>
          </div>

          <button class="btn-login">
            <i class="fas fa-right-to-bracket"></i>
            Iniciar Sesión
          </button>

          <div class="test-box">
            <strong>Usuarios de prueba</strong><br>
            admin, compras, gerencia, tecnologia, recepcion<br>
            <strong>Contraseña:</strong> 123456
          </div>
        </form>
      </div>
    </section>

  </div>
</div>

<script>
const btn=document.getElementById('togglePassword');
const input=document.getElementById('password');
const icon=document.getElementById('toggleIcon');

btn.onclick=()=>{
  const show=input.type==='password';
  input.type=show?'text':'password';
  icon.classList.toggle('fa-eye');
  icon.classList.toggle('fa-eye-slash');
};
</script>
</body>
</html>
