<?php
require_once '../conexion.php';
include 'header.php';

$hoy = date('Y-m-d');

// Total de servicios hoy
$sqlTotalServicios = "SELECT COUNT(*) as total FROM servicios WHERE DATE(fecha) = ?";
$stmtTotal = $con->prepare($sqlTotalServicios);
$stmtTotal->bind_param("s", $hoy);
$stmtTotal->execute();
$resTotal = $stmtTotal->get_result()->fetch_assoc();
$totalServiciosHoy = $resTotal['total'] ?? 0;

// Servicios por tipo de carro
$sqlServiciosTipo = "SELECT tipo_carro, COUNT(*) as cantidad FROM servicios WHERE DATE(fecha) = ? GROUP BY tipo_carro";
$stmtTipo = $con->prepare($sqlServiciosTipo);
$stmtTipo->bind_param("s", $hoy);
$stmtTipo->execute();
$resultTipo = $stmtTipo->get_result();

// Lavadores activos (distintos empleados que realizaron servicios hoy)
$sqlLavadoresActivos = "SELECT COUNT(DISTINCT id_empleado) AS activos FROM servicios WHERE DATE(fecha) = ?";
$stmtLavadores = $con->prepare($sqlLavadoresActivos);
$stmtLavadores->bind_param("s", $hoy);
$stmtLavadores->execute();
$resLavadores = $stmtLavadores->get_result()->fetch_assoc();
$lavadoresActivosHoy = $resLavadores['activos'] ?? 0;

// Total ingresos del día (sumando el precio del tipo de lavado)
$sqlIngresos = "
  SELECT SUM(tl.precio) AS total_ingresos
  FROM servicios s
  JOIN tipo_lavado tl ON s.id_tipo_lavado = tl.id
  WHERE DATE(s.fecha) = ?
";
$stmtIngresos = $con->prepare($sqlIngresos);
$stmtIngresos->bind_param("s", $hoy);
$stmtIngresos->execute();
$resIngresos = $stmtIngresos->get_result()->fetch_assoc();
$totalIngresosHoy = $resIngresos['total_ingresos'] ?? 0.00;

// Detalle de servicios realizados hoy
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

<link rel="stylesheet" href="../CSS/serviciosCSS/resumen.css">

<div class="content-wrapper">
  <section class="content-header">
    <h1>Resumen Diario <small><?= date('d/m/Y') ?></small></h1>
    <button class="btn btn-primary float-end mt-2" onclick="window.print()">
      <i class="fas fa-print"></i> Imprimir Resumen
    </button>
  </section>

  <section class="content" id="resumenDiario">
    <div class="row mb-4">
      <div class="col-md-3" data-aos="fade-right">
        <div class="card text-center shadow rounded">
          <div class="card-body">
            <h3 class="text-primary"><?= $totalServiciosHoy ?></h3>
            <p class="text-muted">Servicios Realizados Hoy</p>
          </div>
        </div>
      </div>

      <div class="col-md-3" data-aos="fade-up">
        <div class="card text-center shadow rounded">
          <div class="card-body">
            <h3 class="text-success"><?= $lavadoresActivosHoy ?></h3>
            <p class="text-muted">Lavadores Activos Hoy</p>
          </div>
        </div>
      </div>

      <div class="col-md-3" data-aos="fade-up">
        <div class="card text-center shadow rounded">
          <div class="card-body">
            <h3 class="text-warning">$<?= number_format($totalIngresosHoy, 2) ?></h3>
            <p class="text-muted">Ingresos del Día</p>
          </div>
        </div>
      </div>

      <div class="col-md-3" data-aos="fade-left">
        <div class="card shadow rounded">
          <div class="card-header">
            <h5>Servicios por Tipo de Carro</h5>
          </div>
          <ul class="list-group list-group-flush">
            <?php if ($resultTipo->num_rows > 0): ?>
              <?php while($tipo = $resultTipo->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?= htmlspecialchars($tipo['tipo_carro']) ?>
                  <span class="badge bg-primary rounded-pill"><?= $tipo['cantidad'] ?></span>
                </li>
              <?php endwhile; ?>
            <?php else: ?>
              <li class="list-group-item text-muted">No hay servicios hoy</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="card shadow" data-aos="fade-up">
      <div class="card-header bg-info text-white">
        <h4 class="mb-0">Detalle de Servicios Realizados</h4>
      </div>
      <div class="card-body p-0">
        <?php if ($resultServiciosHoy->num_rows > 0): ?>
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead class="thead-light">
                <tr>
                  <th>Hora</th>
                  <th>Lavador</th>
                  <th>Tipo de Carro</th>
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
          </div>
        <?php else: ?>
          <p class="text-center p-4">No se registraron servicios hoy.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  #resumenDiario, #resumenDiario * {
    visibility: visible;
  }
  #resumenDiario {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  button, .btn, .navbar, .main-footer, .sidebar {
    display: none !important;
  }
}
</style>

<?php include 'footer.php'; ?>
