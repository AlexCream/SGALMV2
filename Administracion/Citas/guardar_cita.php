<?php
require_once __DIR__ . '../../conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Validar campos
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $cliente = trim($_POST['cliente'] ?? '');

    if (!$fecha || !$hora || !$cliente) {
        throw new Exception('Todos los campos son obligatorios', 400);
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        throw new Exception('Fecha inválida', 400);
    }
    if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
        throw new Exception('Hora inválida', 400);
    }

    $cliente = substr($cliente, 0, 100);

    $stmt = $conexion->prepare("INSERT INTO citas (fecha, hora, cliente) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Error al preparar: ' . $conexion->error, 500);
    }

    $stmt->bind_param("sss", $fecha, $hora, $cliente);
    if (!$stmt->execute()) {
        throw new Exception('Error al guardar: ' . $stmt->error, 500);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cita guardada exitosamente',
        'id' => $stmt->insert_id
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}
