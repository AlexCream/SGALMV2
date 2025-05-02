<?php
$servidor = "localhost";
$usuario = "Consultador";
$pass = "admin";
$bd = "automotriz_LA";

$conexionConsulta = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$conexionConsulta ->set_charset("utf8mb4"); // Establecer el conjunto de caracteres a utf8mb4
?>