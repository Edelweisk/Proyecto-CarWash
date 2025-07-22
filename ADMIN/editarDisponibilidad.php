<?php
require_once '../conexion.php';
include 'header.php';

// Validar id_lavador por GET
$id_lavador = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_lavador <= 0) {
    echo "<script>alert('ID de lavador no válido'); window.location='lavadoresDisponibles.php';</script>";
    exit;
}

// Verificar que el usuario exista y sea lavador
$stmt = $con->prepare("SELECT id, nombre FROM usuario WHERE id = ? AND rol = 'TecnicoLavado' AND estado = 'activo'");
$stmt->bind_param("i", $id_lavador);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Lavador no encontrado o inactivo'); window.location='lavadoresDisponibles.php';</script>";
    exit;
}

$lavador = $result->fetch_assoc();

// Días de la semana
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

// Procesar formulario al hacer POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminar disponibilidad previa para este lavador
    $deleteStmt = $con->prepare("DELETE FROM lavadores_disponibilidad WHERE id_lavador = ?");
    $deleteStmt->bind_param("i", $id_lavador);
    $deleteStmt->execute();

    // Insertar nuevos días activos
    if (!empty($_POST['dias'])) {
        $insertStmt = $con->prepare("INSERT INTO lavadores_disponibilidad (id_lavador, dia_semana, estado) VALUES (?, ?, 'activo')");
        foreach ($_POST['dias'] as $dia) {
            if (in_array($dia, $diasSemana)) {
                $insertStmt->bind_param("is", $id_lavador, $dia);
                $insertStmt->execute();
            }
        }
        $insertStmt->close();
    }

    echo "<script>alert('Disponibilidad actualizada correctamente'); window.location='lavadoresactivos.php';</script>";
    exit;
}

// Obtener días activos actuales
$dispResult = $con->prepare("SELECT dia_semana FROM lavadores_disponibilidad WHERE id_lavador = ? AND estado = 'activo'");
$dispResult->bind_param("i", $id_lavador);
$dispResult->execute();
$resDisp = $dispResult->get_result();

$diasActivos = [];
while ($row = $resDisp->fetch_assoc()) {
    $diasActivos[] = $row['dia_semana'];
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Editar Disponibilidad - <?= htmlspecialchars($lavador['nombre']) ?></h1>
    <p>Selecciona los días que estará disponible para trabajar.</p>
    <a href="lavadoresactivos.php" class="btn btn-secondary mb-3">&laquo; Volver</a>
    <link rel="stylesheet" href="../CSS/serviciosCSS/disponibilidad.css">
    <link rel="stylesheet" href="../CSS/serviciosCSS/">
  </section>

  <section class="content">
    <div class="card card-outline card-primary">
      <div class="card-body">
        <form method="POST" action="">
          <div class="row">
            <?php foreach ($diasSemana as $dia): ?>
              <div class="col-md-3 mb-2">
                <div class="form-check">
                  <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="dia_<?= $dia ?>" 
                    name="dias[]" 
                    value="<?= $dia ?>"
                    <?= in_array($dia, $diasActivos) ? 'checked' : '' ?>
                  >
                  <label class="form-check-label" for="dia_<?= $dia ?>"><?= $dia ?></label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <button type="submit" class="btn btn-success mt-3">Guardar Cambios</button>
        </form>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
