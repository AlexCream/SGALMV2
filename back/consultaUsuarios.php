<?php
//ESTE SCRIPT RECIBE COMO PARAMETRO DE ENTRADA CARACTERES O CADENAS QUE FILTRAN LA CONSULTA DE CLIENTES EN LA BASE DE DATOS
header("Content-Type: application/json");
include("conexionConsulta.php");

//recepcion del filtro de busqueda
$filtro = isset($_POST["filtro"]) ? trim($_POST["filtro"]) : "";

//recepcion del tipo de usuario
$rol = isset($_POST["rol"]) ? trim($_POST["filtro"]) : "1 OR 2 OR 3";

//recepcion de la pagina de busqueda
$pagina = isset($_POST["pagina"]) ? (int)($_POST["pagina"]) : "1";
$limit = 10;
$inicia = ($pagina-1)*$limit;

//saneacion del filtro
$filtro = $conexionConsulta -> real_escape_string($filtro);

//saneacion del inicio
$filtro = $conexionConsulta -> real_escape_string($filtro);


//creacion de la consulta de busqueda
$sqlQry = "SELECT * FROM usuarios WHERE 
    USER_ID LIKE '%$filtro%' OR 
    NOMBRE LIKE '%$filtro%' OR 
    APELLIDO LIKE '%$filtro%' OR 
    EMAIL LIKE '%$filtro%' AND 
    ROL = '$rol' AND 
    VALIDADO = true
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
        $datos[] = [
            "id" => $fila["USER_ID"],
            //
            //le indicamos los caracteres especiales para evitar errores de formato, le decirmos por medio de la funcion
            //htmlspecialchars el campo a transformar, el entquotes y el tipo de caracteres que estaremos usando
            "nombre" => htmlspecialchars($fila["NOMBRE"],ENT_QUOTES,"UTF-8"),
            "apellido" => htmlspecialchars($fila["APELLIDO"],ENT_QUOTES,"UTF-8"),
            "direccion" => htmlspecialchars($fila["DIRECCION"],ENT_QUOTES,"UTF-8"),
            "telefono1" => htmlspecialchars($fila["TELEFONO1"],ENT_QUOTES,"UTF-8"),  
            "telefono2" => htmlspecialchars($fila["TELEFONO2"],ENT_QUOTES,"UTF-8"),
            "correo" => htmlspecialchars($fila["EMAIL"],ENT_QUOTES,"UTF-8"),
        ]; 
    }
}else{
    $datos = "[]";
}

//Conteo de las paginas totales necesarias
$contarQry = "SELECT COUNT(*) as total FROM usuarios";
$contarRes = $conexionConsulta->query($contarQry);
$resultado = $contarRes->fetch_assoc();
$cantidadregistros = $resultado["total"];
$paginastotales=ceil($cantidadregistros/$limit);

//Envio del resultado en formato JSON, datos (arreglo de registros) y paginas (numero de paginas totales)
echo json_encode(['datos' => $datos,'paginas' => $paginastotales],JSON_UNESCAPED_UNICODE);
    
//Cerramos la conexion a la base de datos
$conexionConsulta->close();
?>