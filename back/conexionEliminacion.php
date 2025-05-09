<?php
$servidor = "localhost";
$usuario = "admin";
$pass = "1A2B3C";
$bd = "automotriz_LA";

$conexionEliminacion = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexionEliminacion->connect_error) {
    die("Error de conexion: " . $conexionEliminacion->connect_error);
}

$conexionEliminacion ->set_charset("utf8mb4"); // Establecer el conjunto de caracteres a utf8mb4
?>