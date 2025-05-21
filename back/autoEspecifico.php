<?php
header("Content-Type: application/json");
include("conexiones/Consulta.php");

// llegada de filtros
$clave_auto = $conexionConsulta -> real_escape_string($_POST['clave_auto'] ?? '');

// construir la consulta
$sql = "SELECT 
    a.clave_auto, a.marca, a.familia, a.modelo, a.color
    a.precio_contado, a.precio_credito, a.descripcion,
    i.clave_imagen, i.tipo
FROM autos AS a 
LEFT JOIN imagenes AS i 
ON a.clave_auto = i.auto_id
WHERE  clave_auto = ?";

$stmt = $conexionConsulta->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => $conexionConsulta->error]);
    exit;
}

//ejecucion y devolucion del resultado
$stmt->bind_param('s', $clave_auto);
$stmt->execute();
$resultado = $stmt->get_result();

$datosAuto = null;
$imagenes = [];

while ($fila = $resultado->fetch_assoc()) {
    if (!$datosAuto) {
        $datosAuto = [
            "clave_auto" => $fila['clave_auto'],
            "datos" => [
                "marca" => $fila['marca'],
                "modelo" => $fila['modelo'],
                "familia" => $fila['familia'],
                "color" => $fila['color'],
                "precio_contado" => $fila['precio_contado'],
                "precio_credito" => $fila['precio_credito'],
                "descripcion" => $fila['descripcion']
            ],
            "imagenes" => []
        ];
    }

    if ($fila['clave_imagen']) {
        $imagenes[] = [
            "tipo" => $fila['tipo'],
            "url" => $fila['clave_imagen'],
        ];
    }
}

$stmt->close();

if ($datosAuto) {
    echo json_encode($datosAuto);
} else {
    echo json_encode(["error" => "Auto no encontrado"]);
}
?>