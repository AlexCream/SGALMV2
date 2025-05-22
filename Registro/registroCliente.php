<?php
//Settings de la conexion pibes
    include("../sql/conexionInsercion.php");
    require __DIR__ . '../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    //datos sobre el trabajador
    $nombre = $conexionInsercion->real_escape_string(trim($_POST['nombre'] ?? ''));
    $apellido = $conexionInsercion->real_escape_string(trim($_POST['apellido'] ?? ''));

    //credenciales cuenta
    $contrasena = $conexionInsercion->real_escape_string(trim($_POST['contrasena'] ?? ''));
    $contrasenaConf = $conexionInsercion->real_escape_string(trim($_POST['confirmar_contrasena'] ?? ''));

    $correo = $_POST['correo'] ?? '';

    //datos de contacto
    $telefono1 = $conexionInsercion->real_escape_string(trim($_POST['telefono1'] ?? ''));
    $telefono2 = $conexionInsercion->real_escape_string(trim($_POST['telefono2'] ?? null));
    $direccion = $conexionInsercion->real_escape_string(trim($_POST['direccion'] ?? ''));

    $img_perf = null;

    if (empty($nombre) || empty($telefono1) || empty($apellido) || empty($contrasena) || empty($contrasenaConf) || empty($correo)) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan campos obligatorios"]);
        exit;
    }
    
    if (strlen($nombre) > 255 || strlen($apellido) > 255 || strlen($direccion) > 255) {
        http_response_code(400);
        echo json_encode(["error" => "Uno o más campos exceden la longitud permitida"]);
        exit;
    }

    if($contrasena != $contrasenaConf){
        http_response_code(400);
        echo json_encode(["error" => "Las contraseñas no coinciden"]);
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "Correo electrónico no válido"]);
        exit;
    }

    $pass_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    //Verificar que el correo ya existe en los registros.
    $verificarEmail = $conexionInsercion->query("SELECT usr_id FROM usuarios where email = '$email'");

    if ($verificarEmail->num_rows > 0) {
        echo json_encode(["error" => "El correo electronico ya esta registrado"]);
        exit;
    }

    //Insertar con la setencias de preapred statement.
    $sql = "INSERT INTO usuarios (usr_id, nombre, apellido, direccion, telefono1, telefono2, email, pass_hash, img_perf, validado, token, eliminado)
        VALUES (3, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $token = bin2hex(random_bytes(16));

    $stmt = $conexionInsercion->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Error en la preparación: " . $conexionInsert->error]);
        exit;
    }
    
    $stmt->bind_param(
        "issssssss",
        3,
        $nombre,
        $apellidos,
        $direccion,
        $telefono1,
        $telefono2,
        $email,
        $pass_hash,
        $img_perf,
        false,
        $token,
        false
    );

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.google.com'; // Usa el servidor SMTP correcto
        $mail->SMTPAuth = true;
        $mail->Username = 'tu_correo@gmail.com';
        $mail->Password = 'tu_contraseña';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tu_correo@tudominio.com', 'Automotriz Los Amigos');
        $mail->addAddress($correo, $nombre . ' ' . $apellido);

        $mail->isHTML(true);
        $mail->Subject = 'Verifica tu cuenta';
        $mail->Body = "
            <h3>Hola $nombre,</h3>
            <p>Gracias por registrarte. Por favor haz clic en el siguiente enlace para verificar tu cuenta:</p>
            <a href='https://tu-dominio.com/verificar.php?token=$token'>Verificar cuenta</a>
            <p>Si no solicitaste este registro, puedes ignorar este mensaje.</p>
        ";
        $mail->send();
        echo json_encode(["success" => "Usuario registrado. Revisa tu correo para verificar tu cuenta."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "El correo de verificación no pudo enviarse. Mailer Error: {$mail->ErrorInfo}"]);
    }

    $stmt->close();
    $conexionInsercion->close();
?>