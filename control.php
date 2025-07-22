<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../conexion.php');

function actualizarRolSesion(mysqli $con, int $idUsuario): void {
    $stmt = $con->prepare("SELECT rol FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $stmt->bind_result($rol);
    if ($stmt->fetch()) {
        $_SESSION['rol'] = $rol;  
    } else {
       
        session_destroy();
        header("Location: ../login.php");
        exit();
    }
    $stmt->close();
}


if (isset($_SESSION['id_usuario'])) {
    actualizarRolSesion($con, $_SESSION['id_usuario']);
}
?>
