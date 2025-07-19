<?php
include 'header.php'; // Ya se inicia sesión y se cargan variables como $nombre

// Consultas para el dashboard
$totalUsuarios = $con->query("SELECT COUNT(*) AS total FROM usuario")->fetch_assoc()['total'] ?? 0;
$totalServicios = $con->query("SELECT COUNT(*) AS total FROM servicios")->fetch_assoc()['total'] ?? 0;
$totalLavadores = $con->query("SELECT COUNT(DISTINCT id_empleado) AS total FROM servicios")->fetch_assoc()['total'] ?? 0;

// Resumen diario
$hoy = date('Y-m-d');
$serviciosHoy = $con->query("SELECT COUNT(*) AS total FROM servicios WHERE DATE(fecha) = '$hoy'")->fetch_assoc()['total'] ?? 0;

$fechaActual = date('d/m/Y');

$ultimos_servicios_sql = "
    SELECT s.*, u.nombre 
    FROM servicios s 
    LEFT JOIN usuario u ON s.id_empleado = u.id 
    ORDER BY s.fecha DESC 
    LIMIT 5
";
$ultimos_servicios = $con->query($ultimos_servicios_sql);
?>

<!-- AOS Animaciones -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init();</script>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-8">
          <h1 data-aos="fade-right">Bienvenido, <?php echo htmlspecialchars($nombre); ?> 👋</h1>
          <p class="text-muted" data-aos="fade-left">Hoy es <?php echo $fechaActual; ?> | Panel de administración</p>
        </div>
      </div>
    </div>
  </section>

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
            <div class="icon">
              <i class="fas fa-car"></i>
            </div>
            <a href="servicios.php" class="small-box-footer">Ver detalles <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Usuarios -->
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?php echo $totalUsuarios; ?></h3>
              <p>Usuarios registrados</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="usuarios.php" class="small-box-footer">Gestionar usuarios <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Lavadores -->
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $totalLavadores; ?></h3>
              <p>Lavadores activos</p>
            </div>
            <div class="icon">
              <i class="fas fa-user-cog"></i>
            </div>
            <a href="usuarios.php?rol=Lavador" class="small-box-footer">Ver lavadores <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Resumen del Día -->
        <div class="col-lg-3 col-6" data-aos="zoom-in">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?php echo $serviciosHoy; ?></h3>
              <p>Servicios hoy</p>
            </div>
            <div class="icon">
              <i class="fas fa-calendar-day"></i>
            </div>
            <span class="small-box-footer text-muted">Resumen diario</span>
          </div>
        </div>

      </div>

      <!-- Últimos Servicios Registrados -->
      <div class="row mt-4">
        <div class="col-12" data-aos="fade-up">
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
                    <tr>
                      <td colspan="6" class="text-center">No hay servicios registrados</td>
                    </tr>
                  <?php endif; ?>
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
