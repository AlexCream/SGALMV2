<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Verificar si el email ya existe
    $sql = "SELECT email FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "El correo ya está registrado";
    } else {
        // Registrar nuevo usuario
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $password);
        
        if ($stmt->execute()) {
            $success = "¡Registro exitoso! Serás redirigido...";
            header("refresh:2; url=login.php");
        } else {
            $error = "Error al registrar usuario";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Automotriz Los Amigos</title>
    <style>
        /* ESTILOS IDÉNTICOS AL LOGIN */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            min-height: 100vh;
        }

        /* HEADER IDÉNTICO */
        .header {
            background-color: #333;
            color: white;
            padding: 50px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .logo {
            position: absolute;
            left: 20px;
            height: 100px;
            width: auto;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        /* CONTENIDO PRINCIPAL - MISMO ESTILO */
        .main-content {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-section {
            padding: 25px 40px;
        }

        .section-title {
            color: #444;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .form-group input {
        width: calc(100% - 30px); 
        margin-left: 15px; /* Margen izquierdo */
        margin-right: 15px; /* Margen derecho */
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 15px;
    }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #444;
        }

        /* MENSAJES - */
        .alert-message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid;
            font-weight: bold;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .alert-error {
            background-color: #fdecea;
            color: #d32f2f;
            border-left-color: #d32f2f;
        }

        /* FOOTER */
        .footer {
            background-color: #333;
            color: #aaa;
            padding: 35px 0;
            text-align: center;
            font-size: 14px;
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #444;
        }

        .login-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .login-links a {
            color: #333;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
        <div class="header-content">
            <img src="logo/LOGO.jpg" alt="Logo Automotriz" class="logo">
            <h1>AUTOMOTRIZ LOS AMIGOS</h1>
        </div>
    </header>
    
    <!-- CONTENIDO PRINCIPAL -->
    <div class="main-content">
        <div class="form-section">
            <div class="section-title">REGISTRO DE USUARIO</div>
            
            <?php if (isset($success)): ?>
                <div class="alert-message alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert-message alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="registro.php">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="submit-btn">REGISTRARSE</button>
            </form>
            
            <div class="login-links">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </div>
    </div>
    
    <!-- FOOTER IDÉNTICO AL LOGIN -->
    <footer class="footer">
        Sistema de Gestión Automotriz © <?php echo date('Y'); ?>
    </footer>
</body>
</html>