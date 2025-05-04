<?php
    header("Content-Type: application/json");
    include("conexionConsulta.php");

    $filtro = isset($_POST["filtro"]) ? trim($_POST["filtro"]) : "";
    $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : "";

    $filtro = $conexionConsulta -> real_escape_string($filtro);

    $fechaInicio = $fecha . "-01";
    $fechaFin = date("Y-m-t", strtotime($fechaInicio));

    $pagina = isset($_POST["pagina"]) ? (int)($_POST["pagina"]) : "1";
    $limit = 10;
    $inicia = ($pagina-1)*$limit;

    $sqlQry = "SELECT V.VENTA_ID, V.VENDEDOR_ID, 
    V.CLIENTE_ID, V.AUTO_ID, 
    V.FECHA_VENTA, V.FECHA_ULTIMO_ABONO, 
    V.ADEUDO, V.DOCUMENTACION_SUBIDA,
    VE.NOMBRE, VE.APELLIDO,
    CL.NOMBRE, CL.APELLIDO, 
    CL.TELEFONO1, CL.TELEFONO2, CL.EMAIL
    FROM ventas V JOIN usuarios VE on V.VENDEDOR_ID = VE.USER_ID
    JOIN  usuarios CL on V.CLIENTE_ID = CL.USER_ID WHERE
    VENDEDOR_ID LIKE '%$$filtro%' OR 
    CLIENTE_ID LIKE '%$filtro%' OR 
    VENTA_ID LIKE '%$filtro%' AND 
    FECHA_VENTA LIKE '%$fecha%' AND 
    ELIMINADO = 'false' AND
    VE.ROL = '2' AND
    CL.ROL = '1'
    LIMIT $limit
    OFFSET $inicia";

?>