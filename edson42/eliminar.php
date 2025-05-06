<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include 'conexion.php';

$id = $_GET['id'];
$sql = "DELETE FROM inventario WHERE id = $id";

if ($conexion->query($sql) === TRUE) {
    header("Location: inventario.php"); // Recarga la página
} else {
    echo "Error al eliminar: " . $conexion->error;
}
?>