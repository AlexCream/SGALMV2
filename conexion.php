<?php
    $servidor = "direccionalSV";
    $usuario = "usuario";
    $pass= "contraseña";
    $bd= "basededatos";
    $conexion = new mysqli($servidor, $usuario, $pass, $bd);
    if($conexion -> connect_error){
        die("Error de conexion: " .$conexion->connect_error);
    }
?>