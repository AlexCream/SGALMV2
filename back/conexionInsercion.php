<?php
$servidor = "localhost";
$usuario = "root";
$pass = "1A2B3C";
$bd = "automotriz_LA";

$conexionInsercion = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexionInsercion->connect_error) {
    die("Error de conexion: " . $conexionInsercion->connect_error);
}

$conexionInsercion ->set_charset("utf8mb4"); // Establecer el conjunto de caracteres a utf8mb4
?>