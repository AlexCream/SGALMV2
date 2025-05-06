<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inicio - Automotriz</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php include('menu.php'); ?>
    
    <div class="contenido">
        <h1>Bienvenido al Sistema Automotriz</h1>
        <p>Seleccione una opción del menú superior</p>
    </div>
</body>
</html>