<?php
//Settings de la conexion pibes
require_once __DIR__ . '/includes/conexion.php';

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
    $nombre = $conexion->real_escape_string(trim($_POST['nombre']));
    $apellidos = $conexion->real_escape_string((trim($_POST['apellidos'])));
    $direccion = $conexion->real_escape_string(trim($_POST['direccion']));
    $telefono_principal = $conexion->real_escape_string(trim($_POST['telefono_principal']));
    $telefono_secundario = $conexion->real_escape_string(trim($_POST['telefono_secundario']));
    $email = $conexion->real_escape_string(trim($_POST['email']));
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);


    //Verificar que el correo ya existe en los registros.
    $verificarEmail = $conexion->query("SELECT id FROM clientes where email = '$email'");

    if ($verificarEmail->num_rows > 0) {
        die("El correo electronico ya esta registrado!!");
    }

    //Insertar con la setencias de preapred statement.

    $stmt = $conexion->prepare("INSERT INTO clientes (nombre,apellidos, direccion,
    telefono_principal,telefono_secundario,email,contrasena) VALUES (?,?,?,?,?,?,?)");

    $stmt->bind_param(
        "sssssss",
        $nombre,
        $apellidos,
        $direccion,
        $telefono_principal,
        $telefono_secundario,
        $email,
        $contrasena
    );

    if ($stmt->execute()) {
        //Pagina para confirmar el registro exitoso.
        header('Location: registro_exitoso.html');
        exit;

    } else {
        die("Error al registrar el usuario: " . $stmt->error);
    }


}

?>