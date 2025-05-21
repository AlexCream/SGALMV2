<?php
header("Content-Type: application/json");
include("conexiones/conexionConsulta.php");

// llegada de filtros
$marca = $conexionConsulta -> real_escape_string($_POST['marca'] ?? '');
$familia = $conexionConsulta -> real_escape_string($_POST['familia'] ?? '');
$modelo = $conexionConsulta -> real_escape_string($_POST['modelo'] ?? '');
$precioMin = filter_var($_POST['precioMin'] ?? '', FILTER_VALIDATE_FLOAT);
$precioMax = filter_var($_POST['precioMax'] ?? '', FILTER_VALIDATE_FLOAT);
$paginaActual = filter_var($_POST['pagina'] ?? 1, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

// asignacion de la paginacion
$porPagina = 16;
$offset = ($paginaActual - 1) * $porPagina;

// arreglo para cláusulas y parámetros
$conditions = [];
$params = [];
$types = '';

// agregar filtros si vienen con datos
if ($marca !== '') {
    $conditions[] = "au.marca = ?";
    $params[] = $marca;
    $types .= 's';
}
if ($familia !== '') {
    $conditions[] = "au.familia = ?";
    $params[] = $familia;
    $types .= 's';
}
if ($modelo !== '') {
    $conditions[] = "au.modelo = ?";
    $params[] = $modelo;
    $types .= 's';
}
if ($precioMin !== '') {
    $conditions[] = "au.precio >= ?";
    $params[] = $precioMin;
    $types .= 'd';
}
if ($precioMax !== '') {
    $conditions[] = "au.precio <= ?";
    $params[] = $precioMax;
    $types .= 'd';
}

// construir la consulta
$sql = "SELECT 
    au.clave_auto AS clave_auto, au.marca AS marca, 
    au.familia AS familia, au.modelo AS modelo,
    au.precio_contado AS precio_contado, im.imagen AS imagen
FROM autos AS au 
LEFT JOIN imagenes AS im
    ON im.clave_auto = au.clave_auto AND im.tipo = 'principal'";

$conditions[] = "disponible = 1";
$conditions[] = "eliminado = 0";

$sql .= " WHERE " . implode(" AND ", $conditions);

//añadido de la condicion de limit y ofset y parametros
$sql .= " LIMIT ? OFFSET ?";
$params[] = $porPagina;
$params[] = $offset;
$types .= 'ii';

// preparar la consulta
$stmt = $conexionConsulta->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => $conexionConsulta->error]);
    exit;
}

// ligar parámetros
$stmt->bind_param($types, ...$params);

// ejecutar y obtener resultados
$stmt->execute();
$resultado = $stmt->get_result();

$autos = [];
while ($fila = $resultado->fetch_assoc()) {
    $autos[] = [
        'clave_auto' => $fila['clave_auto'],
        'marca' => $fila['marca'],
        'familia' => $fila['familia'],
        'modelo' => $fila['modelo'],
        'precio_contado' => $fila['precio_contado'],
        'imagen' => $fila['imagen'] ?? null
    ];
}

$stmt->close();

$sqlCount = "SELECT COUNT(*) AS total FROM autos";
if (!empty($conditions)) {
    $sqlCount .= " WHERE " . implode(" AND ", $conditions);
}
$stmtCount = $conexionConsulta->prepare($sqlCount);

// reusar los mismos parámetros menos LIMIT y OFFSET
$parametrosContar = array_slice($params, 0, count($params) - 2);
$tiposContar = substr($types, 0, strlen($types) - 2);
$stmtCount->bind_param($tiposContar, ...$parametrosContar);

$stmtCount->execute();
$resultadoCount = $stmtCount->get_result();
$totalRegistros = $resultadoCount->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $porPagina);
$stmtCount->close();

$respuesta = [
    'auto' => $autos,
    'paginas' => $totalPaginas
];

echo json_encode($respuesta);
?>