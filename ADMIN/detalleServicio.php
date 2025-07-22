<?php
require_once('../conexion.php');
include 'header.php';

$idServicio = $_GET['id'] ?? 1;

if (!is_numeric($idServicio)) {
    echo '<div class="alert alert-danger m-4">ID de servicio no válido.</div>';
    include 'footer.php';
    exit;
}

$idServicio = (int) $idServicio;

// Consulta con JOIN para obtener tipo de lavado actualizado (nombre + precio) y empleado
$stmt = $con->prepare("
    SELECT 
        s.*, 
        u.nombre AS empleado_nombre, 
        u.email AS empleado_email, 
        u.usuario AS empleado_usuario, 
        u.imagen AS empleado_imagen,
        tl.nombre AS tipo_lavado_nombre,
        tl.precio AS tipo_lavado_precio
    FROM servicios s
    LEFT JOIN usuario u ON s.id_empleado = u.id
    LEFT JOIN tipo_lavado tl ON s.id_tipo_lavado = tl.id
    WHERE s.id = ?
");
$stmt->bind_param('i', $idServicio);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="alert alert-warning m-4">No se encontró el servicio solicitado.</div>';
    include 'footer.php';
    exit;
}

$servicio = $result->fetch_assoc();
?>

<!-- CSS -->
<link rel="stylesheet" href="../CSS/serviciosCSS/verservicio.css" />

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-3 align-items-center">
        <div class="col-sm-8">
          <h1 class="text-primary">
            Detalle del Servicio <small class="text-muted">#<?= htmlspecialchars($servicio['id']); ?></small>
          </h1>
        </div>
        <div class="col-sm-4 text-end">
          <a href="servicios.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Servicios
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title">Información Detallada del Servicio</h3>
        </div>
        <div class="card-body">

          <div class="row">
            <!-- Imagen del Empleado -->
            <div class="col-md-3 text-center mb-4">
              <?php
                $imgPath = "../uploads/" . $servicio['empleado_imagen'];
                if (!empty($servicio['empleado_imagen']) && file_exists($imgPath)):
              ?>
                  <img src="<?= htmlspecialchars($imgPath); ?>" class="img-fluid rounded-circle shadow mb-2" style="max-width: 150px;" alt="Empleado">
              <?php else: ?>
                  <i class="fas fa-user-circle fa-9x text-muted mb-2"></i>
              <?php endif; ?>
              <h5 class="fw-bold"><?= htmlspecialchars($servicio['empleado_nombre'] ?? 'Empleado desconocido'); ?></h5>
              <p class="mb-0 text-muted"><?= htmlspecialchars($servicio['empleado_usuario'] ?? ''); ?></p>
              <p class="text-muted"><?= htmlspecialchars($servicio['empleado_email'] ?? ''); ?></p>
            </div>

            <!-- Datos del Servicio -->
            <div class="col-md-9">
              <table class="table table-hover table-bordered table-striped">
                <tbody>
                  <tr>
                    <th style="width: 30%;">Tipo de Lavado</th>
                    <td><?= htmlspecialchars($servicio['tipo_lavado_nombre'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>Precio</th>
                    <td>$<?= number_format((float)($servicio['tipo_lavado_precio'] ?? 0), 2); ?></td>
                  </tr>
                  <tr>
                    <th>Tipo de Vehículo</th>
                    <td><?= htmlspecialchars($servicio['tipo_carro']); ?></td>
                  </tr>
                  <tr>
                    <th>Placa del Vehículo</th>
                    <td><?= htmlspecialchars($servicio['placa']); ?></td>
                  </tr>
                  <tr>
                    <th>Fecha y Hora del Servicio</th>
                    <td><?= date('d/m/Y H:i:s', strtotime($servicio['fecha'])); ?></td>
                  </tr>
                  <tr>
                    <th>Estado</th>
                    <td><?= ucfirst(htmlspecialchars($servicio['estado'])); ?></td>
                  </tr>
                  <tr>
                    <th>Método de Pago</th>
                    <td><?= ucfirst(htmlspecialchars($servicio['metodo_pago'] ?? 'No especificado')); ?></td>
                  </tr>
                  <tr>
                    <th>Observaciones</th>
                    <td><?= nl2br(htmlspecialchars($servicio['observaciones'] ?? '-')); ?></td>
                  </tr>
                </tbody>
              </table>

              <div class="text-end">
                <a href="crearServicio.php?id=<?= $servicio['id']; ?>" class="btn btn-warning">
                  <i class="fas fa-edit"></i> Editar Servicio
                </a>
              </div>

            </div>
          </div>

        </div>
      </div>

    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
