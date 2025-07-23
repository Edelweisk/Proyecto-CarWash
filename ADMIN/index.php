<?php
include 'header.php';

// --- Consultas principales ---
$totalUsuarios = $con->query("SELECT COUNT(*) AS total FROM usuario")->fetch_assoc()['total'] ?? 0;
$totalServicios = $con->query("SELECT COUNT(*) AS total FROM servicios")->fetch_assoc()['total'] ?? 0;
$totalLavadores = $con->query("SELECT COUNT(DISTINCT id_empleado) AS total FROM servicios")->fetch_assoc()['total'] ?? 0;

$hoy = date('Y-m-d');
$fechaActual = date('d/m/Y');
$serviciosHoy = $con->query("SELECT COUNT(*) AS total FROM servicios WHERE DATE(fecha) = '$hoy'")->fetch_assoc()['total'] ?? 0;

// Servicios ayer para comparación simple
$ayer = date('Y-m-d', strtotime('-1 day'));
$serviciosAyer = $con->query("SELECT COUNT(*) AS total FROM servicios WHERE DATE(fecha) = '$ayer'")->fetch_assoc()['total'] ?? 0;
$diferenciaServicios = $serviciosHoy - $serviciosAyer;

// Últimos servicios
$ultimos_servicios_sql = "
    SELECT s.*, u.nombre 
    FROM servicios s 
    LEFT JOIN usuario u ON s.id_empleado = u.id 
    ORDER BY s.fecha DESC 
    LIMIT 5
";
$ultimos_servicios = $con->query($ultimos_servicios_sql);

// Últimos usuarios registrados (sin rol)
$ultimos_usuarios_sql = "SELECT nombre, usuario, fechaRegistro FROM usuario ORDER BY fechaRegistro DESC LIMIT 5";
$ultimos_usuarios = $con->query($ultimos_usuarios_sql);

// Servicios últimos 7 días para gráfica
$servicios_7dias = [];
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $count = $con->query("SELECT COUNT(*) AS total FROM servicios WHERE DATE(fecha) = '$fecha'")->fetch_assoc()['total'] ?? 0;
    $servicios_7dias[] = ['fecha' => date('d/m', strtotime($fecha)), 'total' => (int)$count];
}
?>

<!-- AOS y Chart.js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>AOS.init();</script>
<link rel="stylesheet" href="../CSS/IndexCss/Index.css" />
<div class="content-wrapper">
  <!-- Encabezado -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1 data-aos="fade-right">Bienvenido, <?php echo htmlspecialchars($nombre ?? 'Administrador'); ?> 👋</h1>
          <p class="text-muted" data-aos="fade-left">Hoy es <?php echo $fechaActual; ?> | Panel de administración</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Botones rápidos -->
  <section class="mb-4" data-aos="fade-up">
    <div class="container-fluid d-flex gap-3 flex-wrap">
       <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?>
      <a href="CrearServicio.php?action=new" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Servicio</a>
      <a href="Registrar_Empleado.php" class="btn btn-success"><i class="fas fa-user-plus"></i> Nuevo Usuario</a>
      <?php endif; ?>
      <a href="lavadoresactivos.php" class="btn btn-info"><i class="fas fa-user-cog"></i> Ver Lavadores</a>
            <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?>

      <a href="reportes.php" class="btn btn-warning"><i class="fas fa-chart-line"></i> Reportes</a>
      <?php endif; ?>
      <div class="col-sm-4 text-end">
          <a href="crearServicio.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Servicio
          </a>
        </div>
    </div>
  
  </section>

  <!-- Estadísticas rápidas -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">

        <!-- Total Servicios -->
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-primary">
            <div class="inner">
              <h3><?php echo $totalServicios; ?></h3>
              <p>Servicios realizados</p>
            </div>
            <div class="icon"><i class="fas fa-car"></i></div>
            <a href="servicios.php" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?>
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?php echo $totalUsuarios; ?></h3>
              <p>Usuarios registrados</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="usuarios.php" class="small-box-footer">Gestionar usuarios <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
<?php endif; ?>
        <!-- Lavadores -->
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $totalLavadores; ?></h3>
              <p>Lavadores activos</p>
            </div>
            <div class="icon"><i class="fas fa-user-cog"></i></div>
            <a href="lavadoresactivos.php?rol=Lavador" class="small-box-footer">Ver lavadores <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Resumen del Día con comparación -->
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>
                <?php echo $serviciosHoy; ?>
                <?php if ($diferenciaServicios > 0): ?>
                  <small class="text-success" title="Incremento desde ayer">+<?php echo $diferenciaServicios; ?> ↑</small>
                <?php elseif ($diferenciaServicios < 0): ?>
                  <small class="text-danger" title="Disminución desde ayer"><?php echo $diferenciaServicios; ?> ↓</small>
                <?php else: ?>
                  <small class="text-muted" title="Sin cambios">=</small>
                <?php endif; ?>
              </h3>
              <p>Servicios hoy</p>
            </div>
          <div class="icon"><i class="fas fa-user-cog"></i></div>
            <a href="resumendiario.php?rol=Lavador" class="small-box-footer">Ver resumen diario <i class="fas fa-arrow-circle-right"></i></a>
          </div>
          
        </div>

      </div>

      <!-- Gráfica servicios últimos 7 días -->
      <div class="row mt-4" data-aos="fade-up">
        <div class="col-lg-8 mx-auto">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">Servicios últimos 7 días</h3>
            </div>
            <div class="card-body">
              <canvas id="chartServicios" height="100"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimos Servicios Registrados -->
      <div class="row mt-4">
        <div class="col-lg-6" data-aos="fade-up">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">Últimos servicios registrados</h3>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table table-bordered table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Empleado</th>
                    <th>Tipo de carro</th>
                    <th>Placa</th>
                    <th>Fecha</th>
                    <th>Observaciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($ultimos_servicios && $ultimos_servicios->num_rows > 0): ?>
                    <?php while ($fila = $ultimos_servicios->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo $fila['id']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre'] ?? 'Desconocido'); ?></td>
                        <td><?php echo htmlspecialchars($fila['tipo_carro']); ?></td>
                        <td><?php echo htmlspecialchars($fila['placa']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($fila['fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($fila['observaciones']); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay servicios registrados</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Últimos Usuarios Registrados -->
        <div class="col-lg-6" data-aos="fade-up">
          <div class="card card-outline card-success">
            <div class="card-header">
              <h3 class="card-title">Últimos usuarios registrados</h3>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 300px;">
              <table class="table table-bordered table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Fecha registro</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($ultimos_usuarios && $ultimos_usuarios->num_rows > 0): ?>
                    <?php while ($fila = $ultimos_usuarios->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['usuario']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($fila['fechaRegistro'])); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="3" class="text-center">No hay usuarios registrados</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Estado simple del sistema -->
      <div class="row mt-4" data-aos="fade-up">
        <div class="col-lg-12">
          <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas float-start me-2"></i>
            <div>
              Estado del sistema: <strong>Conectado y funcionando correctamente</strong>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
  // Gráfica con Chart.js
  const ctx = document.getElementById('chartServicios').getContext('2d');
  const data = {
    labels: <?php echo json_encode(array_column($servicios_7dias, 'fecha')); ?>,
    datasets: [{
      label: 'Servicios',
      data: <?php echo json_encode(array_column($servicios_7dias, 'total')); ?>,
      fill: false,
      borderColor: 'rgba(0, 123, 255, 0.7)',
      backgroundColor: 'rgba(0, 123, 255, 0.3)',
      tension: 0.2,
      pointRadius: 5,
      pointHoverRadius: 7,
      borderWidth: 3,
    }]
  };
  const config = {
    type: 'line',
    data: data,
    options: {
      scales: {
        y: { beginAtZero: true, stepSize: 1 },
      },
      plugins: {
        legend: { display: false },
        tooltip: { enabled: true }
      },
      responsive: true,
      maintainAspectRatio: false,
    }
  };
  const chartServicios = new Chart(ctx, config);
</script>

<?php include 'footer.php'; ?>
