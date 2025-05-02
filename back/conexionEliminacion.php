<?php
$servidor = "localhost";
$usuario = "Eliminador";
$pass = "admin";
$bd = "automotriz_LA";

$conexion = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$conexion ->set_charset("utf8mb4"); // Establecer el conjunto de caracteres a utf8mb4
?>