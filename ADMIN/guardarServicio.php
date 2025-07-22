<?php
require_once('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_POST['id_empleado'] ?? null;
    $tipo_carro = $_POST['tipo_carro'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';

    // Validaciones básicas
    if (!$id_empleado || !$tipo_carro || !$placa || !$fecha) {
        header('Location: formServicio.php?error=Por favor complete todos los campos requeridos.');
        exit;
    }

    // Preparar consulta
    if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
        // Actualizar
        $id = (int) $_POST['id'];
        $stmt = $con->prepare("UPDATE servicios SET id_empleado=?, tipo_carro=?, placa=?, fecha=?, observaciones=? WHERE id=?");
        $stmt->bind_param('issssi', $id_empleado, $tipo_carro, $placa, $fecha, $observaciones, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: servicios.php?msg=Servicio actualizado correctamente');
    } else {
        // Insertar
        $stmt = $con->prepare("INSERT INTO servicios (id_empleado, tipo_carro, placa, fecha, observaciones) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $id_empleado, $tipo_carro, $placa, $fecha, $observaciones);
        $stmt->execute();
        $stmt->close();
        header('Location: servicios.php?msg=Servicio creado correctamente');
    }
    exit;
} else {
    header('Location: servicios.php');
    exit;
}

