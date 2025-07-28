<?php
// ==========================
// Conexión y control de sesión
// ==========================

require_once('../conexion.php'); // Conexión a la base de datos
include '../control.php';        // Verifica que el usuario tenga sesión activa y permisos adecuados

// ======================================
// Validación del método y procesamiento
// ======================================

// Verifica que la petición sea POST y que el campo 'id' esté definido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    
    // Convertimos el ID recibido a entero para mayor seguridad (evita inyecciones)
    $id = (int) $_POST['id'];

    // ==========================
    // Preparación de la consulta
    // ==========================

    // Se prepara una consulta SQL segura para eliminar un servicio por su ID
    $stmt = $con->prepare("DELETE FROM servicios WHERE id = ?");
    
    // Se asocia el valor del ID como parámetro en la consulta (tipo entero)
    $stmt->bind_param('i', $id);

    // ============================
    // Ejecución de la eliminación
    // ============================

    // Si la consulta se ejecuta correctamente, devolvemos una respuesta 'ok'
    if ($stmt->execute()) {
        echo 'ok';
    } else {
        // Si ocurre un error, devolvemos 'error' (puede ser capturado por JS para mostrar un mensaje)
        echo 'error';
    }

    // Cerramos el statement preparado para liberar recursos
    $stmt->close();
}
?>
