<?php
require_once('../conexion.php');
include 'header.php';

// Obtener ID de servicio desde GET, con valor por defecto 1 para pruebas
$idServicio = $_GET['id'] ?? 1; 
if (!is_numeric($idServicio)) {
    echo '<div class="alert alert-danger m-4">ID de servicio no válido.</div>';
    include 'footer.php';
    exit;
}

$idServicio = (int) $idServicio;

// Consulta segura con prepared statement
$stmt = $con->prepare("
    SELECT s.*, u.nombre AS empleado_nombre, u.email AS empleado_email, u.usuario AS empleado_usuario, u.imagen AS empleado_imagen
    FROM servicios s
    LEFT JOIN usuario u ON s.id_empleado = u.id
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

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-8">
          <h1>Detalle del Servicio #<?php echo htmlspecialchars($servicio['id']); ?></h1>
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
        <div class="card-header">
          <h3 class="card-title">Información del Servicio</h3>
        </div>
        <div class="card-body">

          <div class="row mb-3">
            <div class="col-md-3 text-center">
              <?php
              $imgPath = "../uploads/" . $servicio['empleado_imagen'];
              if (!empty($servicio['empleado_imagen']) && file_exists($imgPath)):
              ?>
                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Imagen empleado" class="img-fluid rounded-circle border" style="max-width: 150px;">
              <?php else: ?>
                <i class="fas fa-user-circle fa-9x text-muted"></i>
              <?php endif; ?>
              <h5 class="mt-2"><?php echo htmlspecialchars($servicio['empleado_nombre'] ?? 'Empleado desconocido'); ?></h5>
              <small class="text-muted"><?php echo htmlspecialchars($servicio['empleado_usuario'] ?? ''); ?></small><br>
              <small class="text-muted"><?php echo htmlspecialchars($servicio['empleado_email'] ?? ''); ?></small>
            </div>

            <div class="col-md-9">
              <table class="table table-striped table-bordered">
                <tbody>
                  <tr>
                    <th>Tipo de carro</th>
                    <td><?php echo htmlspecialchars($servicio['tipo_carro']); ?></td>
                  </tr>
                  <tr>
                    <th>Placa</th>
                    <td><?php echo htmlspecialchars($servicio['placa']); ?></td>
                  </tr>
                  <tr>
                    <th>Fecha y hora</th>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($servicio['fecha'])); ?></td>
                  </tr>
                  <tr>
                    <th>Observaciones</th>
                    <td><?php echo nl2br(htmlspecialchars($servicio['observaciones'])); ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
