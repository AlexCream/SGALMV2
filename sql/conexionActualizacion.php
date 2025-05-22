<?php
$servidor = "localhost";
$usuario = "admin";
$pass = "1A2B3C";
$bd = "automotriz_LA";

$conexionActualizacion = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexionActualizacion->connect_error) {
    die("Error de conexion: " . $conexionActualizacion->connect_error);
}

$conexionActualizacion ->set_charset("utf8mb4"); // Establecer el conjunto de caracteres a utf8mb4
?>