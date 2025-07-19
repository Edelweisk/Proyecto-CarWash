<?php
require_once '../conexion.php';
include 'header.php';

// Fecha de hoy para filtrar
$hoy = date('Y-m-d');

// Total de servicios hoy
$sqlTotalServicios = "SELECT COUNT(*) as total FROM servicios WHERE DATE(fecha) = ?";
$stmtTotal = $con->prepare($sqlTotalServicios);
$stmtTotal->bind_param("s", $hoy);
$stmtTotal->execute();
$resTotal = $stmtTotal->get_result()->fetch_assoc();
$totalServiciosHoy = $resTotal['total'] ?? 0;

// Servicios por tipo de carro hoy
$sqlServiciosTipo = "SELECT tipo_carro, COUNT(*) as cantidad FROM servicios WHERE DATE(fecha) = ? GROUP BY tipo_carro";
$stmtTipo = $con->prepare($sqlServiciosTipo);
$stmtTipo->bind_param("s", $hoy);
$stmtTipo->execute();
$resultTipo = $stmtTipo->get_result();

// Lavadores activos hoy (que tuvieron al menos un servicio)
$sqlLavadoresActivos = "
    SELECT COUNT(DISTINCT id_empleado) AS activos
    FROM servicios
    WHERE DATE(fecha) = ?
";
$stmtLavadores = $con->prepare($sqlLavadoresActivos);
$stmtLavadores->bind_param("s", $hoy);
$stmtLavadores->execute();
$resLavadores = $stmtLavadores->get_result()->fetch_assoc();
$lavadoresActivosHoy = $resLavadores['activos'] ?? 0;

// Listado de servicios hoy con datos del lavador
$sqlServiciosHoy = "
    SELECT s.id, s.tipo_carro, s.placa, s.fecha, s.observaciones, u.nombre as lavador
    FROM servicios s
    JOIN usuario u ON s.id_empleado = u.id
    WHERE DATE(s.fecha) = ?
    ORDER BY s.fecha DESC
";
$stmtServiciosHoy = $con->prepare($sqlServiciosHoy);
$stmtServiciosHoy->bind_param("s", $hoy);
$stmtServiciosHoy->execute();
$resultServiciosHoy = $stmtServiciosHoy->get_result();
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Resumen Diario <small>Servicios y desempeño del día <?= date('d/m/Y') ?></small></h1>
  </section>

  <section class="content">
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card p-3 text-center shadow-sm">
          <h3><?= $totalServiciosHoy ?></h3>
          <p>Total servicios hoy</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-3 text-center shadow-sm">
          <h3><?= $lavadoresActivosHoy ?></h3>
          <p>Lavadores activos hoy</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-3 shadow-sm">
          <h5>Servicios por tipo de carro</h5>
          <ul class="list-group">
            <?php while($tipo = $resultTipo->fetch_assoc()): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($tipo['tipo_carro']) ?>
                <span class="badge badge-primary badge-pill"><?= $tipo['cantidad'] ?></span>
              </li>
            <?php endwhile; ?>
            <?php if ($resultTipo->num_rows === 0): ?>
              <li class="list-group-item text-muted">No hay servicios registrados hoy</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header">
        <h4>Detalle de servicios realizados hoy</h4>
      </div>
      <div class="card-body p-0">
        <?php if ($resultServiciosHoy->num_rows > 0): ?>
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Hora</th>
                <th>Lavador</th>
                <th>Tipo de carro</th>
                <th>Placa</th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php while($serv = $resultServiciosHoy->fetch_assoc()): ?>
                <tr>
                  <td><?= date('H:i', strtotime($serv['fecha'])) ?></td>
                  <td><?= htmlspecialchars($serv['lavador']) ?></td>
                  <td><?= htmlspecialchars($serv['tipo_carro']) ?></td>
                  <td><?= htmlspecialchars($serv['placa']) ?></td>
                  <td><?= htmlspecialchars($serv['observaciones']) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="text-center p-4">No se registraron servicios hoy.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
