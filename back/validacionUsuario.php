<?php
include("conexionConsulta.php");
include("conexionActualizacion.php");

$email = $_POST['email'];
$token = bin2hex(random_bytes(16));

// Insertar usuario
$stmt = $conexion->prepare("INSERT INTO usuarios (email, token) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $token);
if ($stmt->execute()) {
    // Enviar correo
    $link = "http://localhost/confirmar.php?token=$token"; // Cambia dominio
    $asunto = "Confirma tu cuenta";
    $mensaje = "Haz clic aquí para confirmar tu cuenta: $link";
    $cabeceras = "From: no-reply@tusitio.com";

    if (mail($email, $asunto, $mensaje, $cabeceras)) {
        echo "Correo enviado. Revisa tu bandeja.";
    } else {
        echo "Error al enviar el correo.";
    }
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conexion->close();
?>
