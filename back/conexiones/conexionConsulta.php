<?php
$servidor = "localhost";
$usuario = "admin";
$pass = "1A2B3C";
$bd = "automotriz_LA";

$conexionConsulta = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexionConsulta->connect_error) {
    die("Error de conexion: " . $conexionConsulta->connect_error);
}

$conexionConsulta ->set_charset("utf8mb4"); // Establecer el conjunto de caracteres a utf8mb4
?>