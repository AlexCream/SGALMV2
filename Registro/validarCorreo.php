<?php
    include("../sql/conexionConsulta.php");

    $token = $_GET['token'] ?? '';

    if (empty($token)) {
        die("Token no válido");
    }

    $stmt = $conexionConsulta->prepare("SELECT usr_id FROM usuarios WHERE token = ? AND validado = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        $stmtUpdate = $conexionConsulta->prepare("UPDATE usuarios SET validado = 1, token = '' WHERE usr_id = ?");
        $stmtUpdate->bind_param("i", $usuario['usr_id']);
        $stmtUpdate->execute();

        echo "Cuenta verificada correctamente. Ya puedes iniciar sesión.";
    } else {
        echo "Token inválido o cuenta ya verificada.";
    }
?>