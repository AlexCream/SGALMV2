<?php
//Settings de la conexion pibes
include("conexionInsercion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Validacion de campos not nulls.

    $camposRequeridos = [
        'nombre',
        'apellidos',
        'direccion',
        'telefono_principal',
        'email',
        'contrasena',
        'confirmar_contrasena'
    ];

    foreach ($camposRequeridos as $campo) {
        if (empty($_POST[$campo])) {
            die("El campo $campo es obligatorio.");
        }
    }
    //Verificar que las contraseñas coincidan
    if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
        die("Las contraseñas no coinciden.");
    }

    //limpiar los datos de entrada
    $nombre = $conexionInsercion->real_escape_string(trim($_POST['nombre']));
    $apellidos = $conexionInsercion->real_escape_string((trim($_POST['apellidos'])));
    $direccion = $conexionInsercion->real_escape_string(trim($_POST['direccion']));
    $telefono_principal = $conexionInsercion->real_escape_string(trim($_POST['telefono_principal']));
    $telefono_secundario = $conexionInsercion->real_escape_string(trim($_POST['telefono_secundario']));
    $email = $conexionInsercion->real_escape_string(trim($_POST['email']));
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(16));
    $validado = 0;

    //Verificar que el correo ya existe en los registros.
    $verificarEmail = $conexionInsercion->query("SELECT id FROM clientes where email = '$email'");

    if ($verificarEmail->num_rows > 0) {
        die("El correo electronico ya esta registrado!!");
    }

    //Insertar con la setencias de preapred statement.

    $stmt = $conexionInsercion->prepare("INSERT INTO clientes (nombre,apellidos, direccion,
    telefono_principal,telefono_secundario,email,contrasena,token,validado) VALUES (?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param(
        "sssssss",
        $nombre,
        $apellidos,
        $direccion,
        $telefono_principal,
        $telefono_secundario,
        $email,
        $contrasena,
        $token,
        false
    );

    $stmt->execute();

/*    if ($stmt->execute()) {
            $link = "http://tusitio.com/confirmar.php?token=$token";
            $asunto = "Confirma tu cuenta";
            $mensaje = "Hola $nombre,\n\nGracias por registrarte.\n\nHaz clic en este enlace para confirmar tu cuenta:\n$link\n\nSi no fuiste tú, ignora este mensaje.";
            $cabeceras = "From: no-reply@tusitio.com";

            if (mail($email, $asunto, $mensaje, $cabeceras)) {
                header('Location: registro_exitoso.html');
                exit;
            } else {
                die("Error al enviar el correo de confirmación.");
            }

        } else {
            die("Error al registrar el usuario: " . $stmt->error);
        }

        $stmt->close();
        $conexion->close();
    */
    }

?>