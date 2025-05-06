<?php
$host = "localhost";
$user = "root";
$password = "1234";  
$database = "automotriz"; // Nombre base de datos

$conexion = new mysqli($host, $user, $password, $database);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>