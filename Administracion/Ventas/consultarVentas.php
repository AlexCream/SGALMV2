<?php
    header("Content-Type: application/json");
    include("../../conexionConsulta.php");

    //LLEGADA D ELOS FILTROS DE PALABRA Y MES
    $filtro = isset($_POST["filtro"]) ? trim($_POST["filtro"]) : "";
    $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : "";

    //SANCEACION DEL FILTRO DE PALABRA
    $filtro = $conexionConsulta -> real_escape_string($filtro);

    //FORMATE DEL RANGO DE FECHAS DENTRO DEL MES
    $fechaInicio = $fecha . "-01";
    $fechaFin = date("Y-m-t", strtotime($fechaInicio));

    //OBTENCION DE EL RANGO DE PAGINA A CONSULTAR
    $pagina = isset($_POST["pagina"]) ? (int)($_POST["pagina"]) : "1";
    $limit = 10;
    $inicia = ($pagina-1)*$limit;

    //CREACION DE EL QUERRY DE CONSULTA CON LOS FILTROS ASIGNADOS
    $sqlQry = "SELECT V.VENTA_ID, V.VENDEDOR_ID, 
    V.CLIENTE_ID, V.AUTO_ID, 
    V.FECHA_VENTA, V.FECHA_ULTIMO_ABONO, 
    V.ADEUDO, V.DOCUMENTACION_SUBIDA,
    VE.NOMBRE AS VENDEDOR_NOMBRE, VE.APELLIDO AS VENDEDOR_APELLIDO,
    CL.NOMBRE AS CLIENTE_NOMBRE, CL.APELLIDO AS CLIENTE_APELLIDO, 
    CL.TELEFONO1, CL.TELEFONO2, CL.EMAIL
    FROM ventas V JOIN usuarios VE on V.VENDEDOR_ID = VE.USER_ID
    JOIN  usuarios CL on V.CLIENTE_ID = CL.USER_ID WHERE
    (
    VENDEDOR_ID = '$filtro' OR 
    CLIENTE_ID = '$filtro' OR 
    VENTA_ID LIKE '%$filtro%' 
    ) AND 
    FECHA_VENTA BETWEEN '$fechaInicio' AND '$fechaFin' AND 
    ELIMINADO = 'false' AND
    VE.ROL = '2' AND
    CL.ROL = '1'
    LIMIT $limit
    OFFSET $inicia";

    //Ejecutar la consulta
    $sqlRes = $conexionConsulta->query($sqlQry);

    //Creamos un array
    $datos = [];

    //comprobamos que resultado si nos haya arrojado un registro al menos
    if($sqlRes->num_rows > 0){
        //Asociacion de los campos con sus valores
        while($fila = $sqlRes->fetch_assoc()){
            //va almacenando los datos conforme se crean los registros nuevos
            $datos[]=[
                "id" => $fila["VENTA_ID"],
                "detalles" => [
                    "auto" => htmlspecialchars($fila["AUTO_ID"],ENT_QUOTES,"UTF-8"),
                    "fechaVenta" => htmlspecialchars($fila["FECHA_VENTA"],ENT_QUOTES,"UTF-8"),
                    "ultimoPago" => htmlspecialchars($fila["FECHA_ULTIMO_ABONO"],ENT_QUOTES,"UTF-8"),  
                    "deuda" => htmlspecialchars($fila["ADEUDO"],ENT_QUOTES,"UTF-8"),
                    "documentacion" => htmlspecialchars($fila["DOCUMENTACION_SUBIDA"],ENT_QUOTES,"UTF-8")
                ], 
                "cliente" => [
                    "id" => $fila["CLIENTE_ID"],
                    "nombre" => htmlspecialchars($fila["CLIENTE_NOMBRE"],ENT_QUOTES,"UTF-8"),
                    "apellido" => htmlspecialchars($fila["CLIENTE_APELLIDO"],ENT_QUOTES,"UTF-8"),
                    "telefono1" => htmlspecialchars($fila["TELEFONO1"],ENT_QUOTES,"UTF-8"),  
                    "telefono2" => htmlspecialchars($fila["TELEFONO2"],ENT_QUOTES,"UTF-8"),
                    "correo" => htmlspecialchars($fila["EMAIL"],ENT_QUOTES,"UTF-8")
                ],
                "vendedor" => [
                    "id" => $fila["VENDEDOR_ID"],
                    "nombre" => htmlspecialchars($fila["VENDEDOR_NOMBRE"],ENT_QUOTES,"UTF-8"),
                    "apellido" => htmlspecialchars($fila["VENDEDOR_APELLIDO"],ENT_QUOTES,"UTF-8")
                ]
            ];    
        }
    }else{
        $datos = [];
    }

    //Conteo de las paginas totales necesarias
    $contarQry = "SELECT COUNT(*) as total FROM 
    ventas V JOIN usuarios VE on V.VENDEDOR_ID = VE.USER_ID
    JOIN  usuarios CL on V.CLIENTE_ID = CL.USER_ID WHERE
    (
    VENDEDOR_ID LIKE '$filtro' OR 
    CLIENTE_ID LIKE '$filtro' OR 
    VENTA_ID LIKE '%$filtro%' 
    ) AND 
    FECHA_VENTA BETWEEN '$fechaInicio' AND '$fechaFin' AND 
    ELIMINADO = 'false' AND
    VE.ROL = '2' AND
    CL.ROL = '1'";
    $contarRes = $conexionConsulta->query($contarQry);
    $resultado = $contarRes->fetch_assoc();
    $cantidadregistros = $resultado["total"];
    $paginastotales=ceil($cantidadregistros/$limit);

    //Envio del resultado en formato JSON, datos (arreglo de registros) y paginas (numero de paginas totales)
    echo json_encode(['datos' => $datos,'paginas' => $paginastotales],JSON_UNESCAPED_UNICODE);
        
    //Cerramos la conexion a la base de datos
    $conexionConsulta->close();
?>