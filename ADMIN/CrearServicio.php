<?php
// --- Conexión a la base de datos ---
require_once('../conexion.php');

// --- Inicializar variables y estructura base del servicio ---
$editar = false; // Bandera para saber si es edición o creación
$error = null;   // Para mostrar mensajes de error

// Valores por defecto para el formulario (modo creación)
$servicio = [
    'id_empleado' => '',
    'id_tipo_lavado' => '',
    'tipo_carro' => '',
    'placa' => '',
    'fecha' => date('Y-m-d\TH:i'), // formato para input datetime-local
    'observaciones' => '',
    'estado' => 'pendiente',
    'metodo_pago' => 'efectivo',
];

// --- Procesamiento del formulario al hacer POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $idServicio = $_POST['id'] ?? null;
    $id_empleado = $_POST['id_empleado'] ?? null;
    $id_tipo_lavado = $_POST['id_tipo_lavado'] ?? null;
    $tipo_carro = $_POST['tipo_carro'] ?? '';
    $placa = $_POST['placa'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    $estado = $_POST['estado'] ?? 'pendiente';
    $metodo_pago = $_POST['metodo_pago'] ?? 'efectivo';

    // Validación básica
    if (!$id_empleado || !$id_tipo_lavado || !$tipo_carro || !$placa || !$fecha) {
        $error = "Por favor, complete todos los campos requeridos.";
    } else {
        // --- Actualizar servicio existente ---
        if ($idServicio && is_numeric($idServicio)) {
            $stmt = $con->prepare("CALL editar_servicio(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iissssssi', $id_empleado, $id_tipo_lavado, $tipo_carro, $placa, $fecha, $observaciones, $estado, $metodo_pago, $idServicio);
            $exito = $stmt->execute();
        } else {
            // --- Crear nuevo servicio ---
            $stmt = $con->prepare("CALL crear_servicio(?, ?, ?, ?, ?, ?, ?, ?)");            
            $stmt->bind_param('iissssss', $id_empleado, $id_tipo_lavado, $tipo_carro, $placa, $fecha, $observaciones, $estado, $metodo_pago);
            $exito = $stmt->execute();
        }

        // --- Verificación del resultado ---
        if ($exito) {
            header("Location: servicios.php?mensaje=Servicio guardado correctamente");
            exit;
        } else {
            $error = "Error al guardar el servicio: " . $con->error;
        }
    }
}

// --- Si es modo edición, se cargan los datos del servicio a editar ---
$idServicio = $_GET['id'] ?? null;
if ($idServicio && is_numeric($idServicio)) {
    $editar = true;
    $stmt = $con->prepare("SELECT * FROM servicios WHERE id = ?");
    $stmt->bind_param('i', $idServicio);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 1) {
        $servicio = $resultado->fetch_assoc();
        // Convertir formato de fecha a 'datetime-local'
        $servicio['fecha'] = date('Y-m-d\TH:i', strtotime($servicio['fecha']));
    } else {
        $error = "Servicio no encontrado.";
        $editar = false;
    }
}

// Si no es modo edición, precargar tipo de lavado si viene por GET
if (!$editar && isset($_GET['id_tipo_lavado']) && is_numeric($_GET['id_tipo_lavado'])) {
    $servicio['id_tipo_lavado'] = $_GET['id_tipo_lavado'];
}


// --- Cargar listas desplegables: empleados y tipos de lavado ---
$empleados = $con->query("SELECT id, nombre FROM usuario WHERE rol IN ('TecnicoLavado') ORDER BY nombre");
$tiposLavado = $con->query("SELECT id, nombre, precio FROM tipo_lavado WHERE estado = 'activo' ORDER BY nombre");

include 'header.php';
?>

<!-- Estilo específico -->
<link rel="stylesheet" href="../CSS/serviciosCSS/crear.css"/>

<!-- Contenedor principal -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-8">
          <h1><?= $editar ? "Editar Servicio #".htmlspecialchars($idServicio) : "Crear Nuevo Servicio" ?></h1>
        </div>
        <div class="col-sm-4 text-end">
          <a href="servicios.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver a Servicios</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Formulario para crear o editar servicio -->
      <div class="card card-outline card-primary">
        <div class="card-body">
          <form action="CrearServicio.php<?= $editar ? '?id='.$idServicio : '' ?>" method="POST" novalidate>
            <?php if ($editar): ?>
              <input type="hidden" name="id" value="<?= htmlspecialchars($idServicio) ?>">
            <?php endif; ?>

            <!-- Selección de empleado -->
            <div class="mb-3">
              <label for="id_empleado" class="form-label">Empleado (Lavador)</label>
              <select name="id_empleado" id="id_empleado" class="form-select" required>
                <option value="" disabled <?= empty($servicio['id_empleado']) ? 'selected' : '' ?>>Seleccione un empleado</option>
                <?php while ($emp = $empleados->fetch_assoc()): ?>
                  <option value="<?= $emp['id'] ?>" <?= ($emp['id'] == $servicio['id_empleado']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($emp['nombre']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un empleado.</div>
            </div>

            <!-- Tipo de lavado -->
            <div class="mb-3">
              <label for="id_tipo_lavado" class="form-label">Tipo de lavado</label>
              <select name="id_tipo_lavado" id="id_tipo_lavado" class="form-select" required>
                <option value="" disabled <?= empty($servicio['id_tipo_lavado']) ? 'selected' : '' ?>>Seleccione un tipo de lavado</option>
                <?php while ($tipo = $tiposLavado->fetch_assoc()): ?>
                  <option value="<?= $tipo['id'] ?>" data-precio="<?= $tipo['precio'] ?>" <?= ($tipo['id'] == $servicio['id_tipo_lavado']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipo['nombre']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un tipo de lavado.</div>
            </div>

            <!-- Precio (se muestra automáticamente) -->
            <div class="mb-3">
              <label for="precio_tipo_lavado" class="form-label">Precio</label>
              <input type="text" id="precio_tipo_lavado" class="form-control" readonly placeholder="Seleccione un tipo de lavado">
            </div>

            <!-- Tipo de carro -->
            <div class="mb-3">
              <label for="tipo_carro" class="form-label">Tipo de carro</label>
              <input type="text" name="tipo_carro" id="tipo_carro" class="form-control" required
                value="<?= htmlspecialchars($servicio['tipo_carro']) ?>" placeholder="Ejemplo: Sedán, SUV">
              <div class="invalid-feedback">Ingrese el tipo de carro.</div>
            </div>

            <!-- Placa -->
            <div class="mb-3">
              <label for="placa" class="form-label">Placa</label>
              <input type="text" name="placa" id="placa" class="form-control" required
                value="<?= htmlspecialchars($servicio['placa']) ?>" placeholder="Ejemplo: ABC123">
              <div class="invalid-feedback">Ingrese la placa del carro.</div>
            </div>

            <!-- Fecha y hora del servicio -->
            <div class="mb-3">
              <label for="fecha" class="form-label">Fecha y hora</label>
              <input type="datetime-local" name="fecha" id="fecha" class="form-control" required
                value="<?= htmlspecialchars($servicio['fecha']) ?>">
              <div class="invalid-feedback">Seleccione la fecha y hora del servicio.</div>
            </div>

            <!-- Observaciones -->
            <div class="mb-3">
              <label for="observaciones" class="form-label">Observaciones</label>
              <textarea name="observaciones" id="observaciones" class="form-control" rows="4" placeholder="Detalles adicionales..."><?= htmlspecialchars($servicio['observaciones']) ?></textarea>
            </div>

            <!-- Estado del servicio -->
            <div class="mb-3">
              <label for="estado" class="form-label">Estado del Servicio</label>
              <select name="estado" id="estado" class="form-select" required>
                <?php
                $estados = ['pendiente' => 'Pendiente', 'finalizado' => 'Finalizado'];
                foreach ($estados as $key => $val) {
                  $sel = ($servicio['estado'] ?? '') === $key ? 'selected' : '';
                  echo "<option value=\"$key\" $sel>$val</option>";
                }
                ?>
              </select>
              <div class="invalid-feedback">Seleccione un estado.</div>
            </div>

            <!-- Método de pago -->
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

            <!-- Botón de acción -->
            <button type="submit" class="btn btn-primary"><?= $editar ? 'Guardar Cambios' : 'Crear Servicio' ?></button>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Script: Mostrar precio según tipo de lavado -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tipoLavadoSelect = document.getElementById('id_tipo_lavado');
  const precioInput = document.getElementById('precio_tipo_lavado');

  function actualizarPrecio() {
    const selected = tipoLavadoSelect.options[tipoLavadoSelect.selectedIndex];
    const precio = selected.getAttribute('data-precio');
    precioInput.value = precio ? `$${parseFloat(precio).toFixed(2)}` : '';
  }

  tipoLavadoSelect.addEventListener('change', actualizarPrecio);
  actualizarPrecio(); // Mostrar precio al cargar
});

// Validación Bootstrap 5
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
