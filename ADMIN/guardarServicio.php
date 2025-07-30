<?php
// Conexión a la base de datos
require_once('../conexion.php');

// Verifica que la petición sea POST (evita accesos directos por URL)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ============================
    // OBTENER DATOS DEL FORMULARIO
    // ============================

    $id_empleado   = $_POST['id_empleado'] ?? null;       // ID del lavador asignado
    $tipo_carro    = $_POST['tipo_carro'] ?? '';          // Tipo de vehículo (ej. Sedán, SUV)
    $placa         = $_POST['placa'] ?? '';               // Placa del vehículo
    $fecha         = $_POST['fecha'] ?? '';               // Fecha y hora del servicio
    $observaciones = $_POST['observaciones'] ?? '';       // Comentarios adicionales (opcional)

    // ============================
    // VALIDACIÓN BÁSICA DE CAMPOS
    // ============================

    if (!$id_empleado || !$tipo_carro || !$placa || !$fecha) {
        // Si falta algún campo obligatorio, redirigir con mensaje de error
        header('Location: formServicio.php?error=Por favor complete todos los campos requeridos.');
        exit;
    }

    // ============================
    // DIFERENCIAR ENTRE CREAR O ACTUALIZAR
    // ============================

    if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
        // --------------------------
        // ACTUALIZAR SERVICIO EXISTENTE
        // --------------------------
        $id = (int) $_POST['id']; // ID del servicio que se va a editar

        // Preparar sentencia SQL con parámetros
        $stmt = $con->prepare("
            UPDATE servicios 
            SET id_empleado = ?, tipo_carro = ?, placa = ?, fecha = ?, observaciones = ? 
            WHERE id = ?
        ");

        // Enlazar parámetros: i (int), s (string) según orden
        $stmt->bind_param('issssi', $id_empleado, $tipo_carro, $placa, $fecha, $observaciones, $id);
        $stmt->execute();
        $stmt->close();

        // Redirigir con mensaje de éxito
        header('Location: servicios.php?msg=Servicio actualizado correctamente');
    } else {
        // --------------------------
        // CREAR NUEVO SERVICIO
        // --------------------------

        // Preparar sentencia SQL para inserción
        $stmt = $con->prepare("
            INSERT INTO servicios (id_empleado, tipo_carro, placa, fecha, observaciones)
            VALUES (?, ?, ?, ?, ?)
        ");

        // Enlazar parámetros de la nueva inserción
        $stmt->bind_param('issss', $id_empleado, $tipo_carro, $placa, $fecha, $observaciones);
        $stmt->execute();
        $stmt->close();

        // Redirigir con mensaje de éxito
        header('Location: servicios.php?msg=Servicio creado correctamente');
    }

    // Salir del script tras completar operación
    exit;
} else {
    // Si alguien intenta acceder sin usar POST, redirigir a la lista de servicios
    header('Location: servicios.php');
    exit;
}
