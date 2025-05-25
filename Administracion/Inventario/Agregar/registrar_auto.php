<?php
require_once __DIR__ . '../../../conexion.php';

// Configurar el manejo de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

header('Content-Type: application/json; charset=utf-8');

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Campos obligatorios actualizados
    $required = ['marca', 'modelo', 'familia', 'color', 'precio_contado', 'puertas'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es obligatorio", 400);
        }
    }

    // Procesar campos de texto
    $marca = substr(trim($_POST['marca']), 0, 80);
    $modelo = substr(trim($_POST['modelo']), 0, 80);
    $familia = substr(trim($_POST['familia']), 0, 80);
    $color = substr(trim($_POST['color']), 0, 50);
    $descripcion = substr(trim($_POST['descripcion'] ?? ''), 0, 255);
    $motor = substr(trim($_POST['motor'] ?? ''), 0, 50);

    // Procesar valores numéricos
    $contado = round(floatval($_POST['precio_contado']), 2);
    $credito = isset($_POST['precio_credito']) ? round(floatval($_POST['precio_credito']), 2) : null;
    $costo = isset($_POST['costo_compra']) ? round(floatval($_POST['costo_compra']), 2) : null;
    $puertas = intval($_POST['puertas']);
    $kilometraje = isset($_POST['kilometraje']) ? intval($_POST['kilometraje']) : null;

    // Procesar transmisión (solo 3 opciones)
    $transmision = null;
    $transmisionesPermitidas = ['Automática', 'Manual', 'CVT'];
    if (isset($_POST['transmision']) && in_array($_POST['transmision'], $transmisionesPermitidas)) {
        $transmision = $_POST['transmision'];
    }

    // Validaciones adicionales
    if ($puertas < 2 || $puertas > 5) {
        throw new Exception("El número de puertas debe estar entre 2 y 5", 400);
    }

    if ($kilometraje !== null && $kilometraje < 0) {
        throw new Exception("El kilometraje no puede ser negativo", 400);
    }

    // Preparar consulta SQL actualizada
    $sql = "INSERT INTO vehiculos 
            (marca, modelo, familia, color, precio_contado, precio_credito, costo_compra, 
             descripcion, puertas, kilometraje, motor, transmision) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error, 500);
    }

    // Vincular parámetros
    $stmt->bind_param(
        'ssssdddsiiss',
        $marca,
        $modelo,
        $familia,
        $color,
        $contado,
        $credito,
        $costo,
        $descripcion,
        $puertas,
        $kilometraje,
        $motor,
        $transmision
    );

    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error, 500);
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Auto registrado correctamente',
        'id' => $stmt->insert_id,
        'data' => [
            'marca' => $marca,
            'modelo' => $modelo,
            'precio' => $contado,
            'puertas' => $puertas,
            'transmision' => $transmision
        ]
    ]);

} catch (Exception $e) {
    // Manejo de errores
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}
?>