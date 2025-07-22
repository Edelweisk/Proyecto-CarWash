<?php
require_once('../conexion.php');
include '../control.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    $stmt = $con->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo 'ok';
    } else {
        echo 'error';
    }

    $stmt->close();
}
?>
