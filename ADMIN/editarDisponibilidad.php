<?php
// Incluimos la conexión a la base de datos y el header común (barra superior, estilos globales, etc.)
require_once '../conexion.php';
include 'header.php';

// Validación del parámetro GET 'id' (id del lavador). Debe ser un número entero válido.
$id_lavador = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_lavador <= 0) {
    // Si no es válido, redireccionamos con alerta
    echo "<script>alert('ID de lavador no válido'); window.location='lavadoresDisponibles.php';</script>";
    exit;
}

// Validamos que el usuario exista, tenga rol 'TecnicoLavado' y esté activo
$stmt = $con->prepare("SELECT id, nombre FROM usuario WHERE id = ? AND rol = 'TecnicoLavado' AND estado = 'activo'");
$stmt->bind_param("i", $id_lavador);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Si el lavador no existe o está inactivo, redireccionamos
    echo "<script>alert('Lavador no encontrado o inactivo'); window.location='lavadoresDisponibles.php';</script>";
    exit;
}

// Guardamos el resultado para mostrar el nombre del lavador
$lavador = $result->fetch_assoc();

// Arreglo con los días válidos de la semana
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

// --- PROCESAMIENTO DEL FORMULARIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Eliminamos todas las disponibilidades actuales de este lavador
    $deleteStmt = $con->prepare("DELETE FROM lavadores_disponibilidad WHERE id_lavador = ?");
    $deleteStmt->bind_param("i", $id_lavador);
    $deleteStmt->execute();

    // Si se seleccionaron días, los insertamos como activos
    if (!empty($_POST['dias'])) {
        $insertStmt = $con->prepare("INSERT INTO lavadores_disponibilidad (id_lavador, dia_semana, estado) VALUES (?, ?, 'activo')");
        foreach ($_POST['dias'] as $dia) {
            if (in_array($dia, $diasSemana)) {
                // Solo insertamos si el día es válido
                $insertStmt->bind_param("is", $id_lavador, $dia);
                $insertStmt->execute();
            }
        }
        $insertStmt->close();
    }

    // Redireccionamos con mensaje de éxito
    echo "<script>alert('Disponibilidad actualizada correctamente'); window.location='lavadoresactivos.php';</script>";
    exit;
}

// --- OBTENER DÍAS ACTIVOS ACTUALES ---
$dispResult = $con->prepare("SELECT dia_semana FROM lavadores_disponibilidad WHERE id_lavador = ? AND estado = 'activo'");
$dispResult->bind_param("i", $id_lavador);
$dispResult->execute();
$resDisp = $dispResult->get_result();

$diasActivos = [];
while ($row = $resDisp->fetch_assoc()) {
    $diasActivos[] = $row['dia_semana']; // Guardamos los días activos en un array para mostrar marcados los checkboxes
}
?>


<!-- Contenedor principal -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Editar Disponibilidad - <?= htmlspecialchars($lavador['nombre']) ?></h1>
    <p>Selecciona los días que estará disponible para trabajar.</p>

    <!-- Botón de volver -->
    <a href="lavadoresactivos.php" class="btn btn-secondary mb-3">&laquo; Volver</a>

    <!-- Hojas de estilo específicas -->
    <link rel="stylesheet" href="../CSS/serviciosCSS/disponibilidad.css">
    <link rel="stylesheet" href="../CSS/serviciosCSS/">
  </section>

  <!-- Sección de contenido principal -->
  <section class="content">
    <div class="card card-outline card-primary">
      <div class="card-body">
        <!-- Formulario para guardar disponibilidad -->
        <form method="POST" action="">
          <div class="row">
            <?php foreach ($diasSemana as $dia): ?>
              <div class="col-md-3 mb-2">
                <div class="form-check">
                  <!-- Checkbox para cada día -->
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

          <!-- Botón de guardar -->
          <button type="submit" class="btn btn-success mt-3">Guardar Cambios</button>
        </form>
      </div>
    </div>
  </section>
</div>

<!-- Footer común -->
<?php include 'footer.php'; ?>


<?php include 'footer.php'; ?>
