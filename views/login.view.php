<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Nuevo diseño de login con tema azul marino minimalista */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a192f 0%, #1d3557 50%, #2c4a6d 100%);
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(0, 188, 212, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            padding: 48px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(10, 25, 47, 0.4);
            width: 100%;
            max-width: 440px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-header .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #1d3557 0%, #2c4a6d 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(29, 53, 87, 0.3);
        }
        
        .login-header .logo-icon i {
            font-size: 32px;
            color: #00BCD4;
        }
        
        .login-header h1 {
            color: #0a192f;
            margin-bottom: 8px;
            font-size: 24px;
            font-weight: 700;
        }
        
        .login-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0a192f;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.3s;
            background: #fff;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #2c4a6d;
            box-shadow: 0 0 0 3px rgba(29, 53, 87, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1d3557 0%, #2c4a6d 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(29, 53, 87, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(29, 53, 87, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: rgba(244, 67, 54, 0.08);
            color: #F44336;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #F44336;
            font-size: 14px;
        }
        
        .credentials-info {
            margin-top: 32px;
            padding: 20px;
            background: rgba(29, 53, 87, 0.03);
            border-radius: 8px;
            font-size: 12px;
            border: 1px solid rgba(29, 53, 87, 0.1);
        }
        
        .credentials-info h4 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #0a192f;
            font-size: 13px;
            font-weight: 600;
        }
        
        .credentials-info ul {
            margin: 12px 0;
            padding-left: 20px;
        }
        
        .credentials-info li {
            margin: 6px 0;
            color: #495057;
        }
        
        .credentials-info strong {
            color: #1d3557;
        }
        
        .credentials-info p {
            margin: 12px 0 0 0;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h1>Sistema de Inventario</h1>
                <p>Ingrese sus credenciales para continuar</p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
            
            <div class="credentials-info">
                <h4>Usuarios de prueba:</h4>
                <ul>
                    <li><strong>admin</strong> - Acceso total</li>
                    <li><strong>compras</strong> - Departamento Compras</li>
                    <li><strong>gerencia</strong> - Gerencia</li>
                    <li><strong>tecnologia</strong> - Departamento Tecnología</li>
                </ul>
                <p>Contraseña para todos: <strong>123456</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
