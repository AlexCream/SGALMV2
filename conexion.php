<?php
$servidor = "srv526.hstgr.io";
$usuario = "u788577049_root";
$pass = "AutoLos4";
$bd = "u788577049_sgalm";

$conexion = new mysqli($servidor, $usuario, $pass, $bd);

if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

?>