<?php
    header('Content-Type: application/json; charset=utf-8');
    include("conexiones/conexionInsercion.php");
    include("conexiones/conexionConsulta.php");
    
    //informacion de rol
    $rol = isset($_POST['rol']) ? intval($_POST['rol']) : null;

    $rolesPermitidos = [1, 2];
    if (!in_array($rol, $rolesPermitidos, true)) {
        http_response_code(400);
        echo json_encode(["error" => "Rol no permitido"]);
        exit;
    }

    //datos sobre el trabajador
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';

    //credenciales cuenta
    $contrasena = $_POST['contrasena'] ?? '';
    $contrasenaConf = $_POST['confirmar_contrasena'] ?? '';

    $correo = $_POST['correo'] ?? '';

    //datos de contacto
    $telefono1 = $_POST['telefono1'] ?? '';
    $telefono2 = $_POST['telefono2'] ?? null;
    $direccion = $_POST['direccion'] ?? '';

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

    $stmtCheck = $conexionConsulta->prepare("SELECT user_id FROM usuarios WHERE email = ?");
    $stmtCheck->bind_param("s", $correo);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    if ($stmtCheck->num_rows > 0) {
        http_response_code(400);
        echo json_encode(["error" => "El correo ya está registrado"]);
        exit;
    }
    $stmtCheck->close();
    
    $validado = 1;
    $token = '';
    $eliminado = 0;

    $sql = "INSERT INTO usuarios (rol, nombre, apellido, direccion, telefono1, telefono2, email, pass_hash, img_perf, validado, token, eliminado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexionInsert->prepare($sql);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "Error en la preparación: " . $conexionInsert->error]);
        exit;
    }

    $stmt->bind_param(
        "issssssssiii",
        $rol,
        $nombre,
        $apellido,
        $direccion,
        $telefono1,
        $telefono2,
        $correo,
        $pass_hash,
        $img_perf,
        $validado,
        $token,
        $eliminado
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => "Usuario registrado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al registrar usuario: " . $stmt->error]);
    }
    $stmt->close();
?>