<?php
require_once('../conexion.php');
include 'header.php';

$idServicio = $_GET['id'] ?? null;
$editar = false;
$servicio = [
    'id_empleado' => '',
    'id_tipo_lavado' => '',
    'tipo_carro' => '',
    'placa' => '',
    'fecha' => date('Y-m-d\TH:i'), // formato HTML5 datetime-local
    'observaciones' => '',
    'estado' => 'pendiente',       // Opcional, estado por defecto
    'metodo_pago' => 'efectivo',   // Opcional, método por defecto
];

// Cargar empleados para el select
$empleados = $con->query("SELECT id, nombre FROM usuario WHERE rol IN ('TecnicoLavado', 'administrador') ORDER BY nombre");

// Cargar tipos de lavado para el select
$tiposLavado = $con->query("SELECT id, nombre FROM tipo_lavado WHERE estado = 'activo' ORDER BY nombre");

if ($idServicio && is_numeric($idServicio)) {
    $editar = true;
    $stmt = $con->prepare("SELECT * FROM servicios WHERE id = ?");
    $stmt->bind_param('i', $idServicio);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 1) {
        $servicio = $resultado->fetch_assoc();
        $servicio['fecha'] = date('Y-m-d\TH:i', strtotime($servicio['fecha']));
    } else {
        echo '<div class="alert alert-warning m-4">Servicio no encontrado.</div>';
        include 'footer.php';
        exit;
    }
}
?>

<link rel="stylesheet" href="../CSS/serviciosCSS/crear.css"/>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-8">
          <h1><?php echo $editar ? "Editar Servicio #" . htmlspecialchars($idServicio) : "Crear Nuevo Servicio"; ?></h1>
        </div>
        <div class="col-sm-4 text-end">
          <a href="servicios.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a Servicios</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-outline card-primary">
        <div class="card-body">
          <form action="guardarServicio.php" method="POST" novalidate>
            <?php if ($editar): ?>
              <input type="hidden" name="id" value="<?php echo htmlspecialchars($idServicio); ?>">
            <?php endif; ?>

            <div class="mb-3">
              <label for="id_empleado" class="form-label">Empleado (Lavador)</label>
              <select name="id_empleado" id="id_empleado" class="form-select" required>
                <option value="" disabled <?php echo empty($servicio['id_empleado']) ? 'selected' : ''; ?>>Seleccione un empleado</option>
                <?php while ($emp = $empleados->fetch_assoc()): ?>
                  <option value="<?php echo $emp['id']; ?>" <?php echo ($emp['id'] == $servicio['id_empleado']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($emp['nombre']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un empleado.</div>
            </div>

            <div class="mb-3">
              <label for="id_tipo_lavado" class="form-label">Tipo de lavado</label>
              <select name="id_tipo_lavado" id="id_tipo_lavado" class="form-select" required>
                <option value="" disabled <?php echo empty($servicio['id_tipo_lavado']) ? 'selected' : ''; ?>>Seleccione un tipo de lavado</option>
                <?php while ($tipo = $tiposLavado->fetch_assoc()): ?>
                  <option value="<?php echo $tipo['id']; ?>" <?php echo ($tipo['id'] == $servicio['id_tipo_lavado']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un tipo de lavado.</div>
            </div>

            <div class="mb-3">
              <label for="tipo_carro" class="form-label">Tipo de carro</label>
              <input type="text" name="tipo_carro" id="tipo_carro" class="form-control" required
                value="<?php echo htmlspecialchars($servicio['tipo_carro']); ?>" placeholder="Ejemplo: Sedán, SUV">
              <div class="invalid-feedback">Ingrese el tipo de carro.</div>
            </div>

            <div class="mb-3">
              <label for="placa" class="form-label">Placa</label>
              <input type="text" name="placa" id="placa" class="form-control" required
                value="<?php echo htmlspecialchars($servicio['placa']); ?>" placeholder="Ejemplo: ABC123">
              <div class="invalid-feedback">Ingrese la placa del carro.</div>
            </div>

            <div class="mb-3">
              <label for="fecha" class="form-label">Fecha y hora</label>
              <input type="datetime-local" name="fecha" id="fecha" class="form-control" required
                value="<?php echo htmlspecialchars($servicio['fecha']); ?>">
              <div class="invalid-feedback">Seleccione la fecha y hora del servicio.</div>
            </div>

            <div class="mb-3">
              <label for="observaciones" class="form-label">Observaciones</label>
              <textarea name="observaciones" id="observaciones" class="form-control" rows="4" placeholder="Detalles adicionales..."><?php echo htmlspecialchars($servicio['observaciones']); ?></textarea>
            </div>

            <!-- Opcional: Estado y método de pago -->
            <div class="mb-3">
              <label for="estado" class="form-label">Estado del Servicio</label>
              <select name="estado" id="estado" class="form-select" required>
                <?php
                $estados = ['pendiente' => 'Pendiente', 'en_proceso' => 'En proceso', 'finalizado' => 'Finalizado'];
                foreach ($estados as $key => $val) {
                  $sel = ($servicio['estado'] ?? '') === $key ? 'selected' : '';
                  echo "<option value=\"$key\" $sel>$val</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">Seleccione un estado.</div>
            </div>

            <div class="mb-3">
              <label for="metodo_pago" class="form-label">Método de pago</label>
              <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                <?php
                $metodos = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia'];
                foreach ($metodos as $key => $val) {
                  $sel = ($servicio['metodo_pago'] ?? '') === $key ? 'selected' : '';
                  echo "<option value=\"$key\" $sel>$val</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">Seleccione un método de pago.</div>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $editar ? 'Guardar Cambios' : 'Crear Servicio'; ?></button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
// Validación simple de formulario (Bootstrap 5)
(() => {
  'use strict'
  const forms = document.querySelectorAll('form[novalidate]')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<?php include 'footer.php'; ?>
