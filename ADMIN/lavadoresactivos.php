<?php
require_once '../conexion.php';
include 'header.php'; // Cabecera del dashboard con Bootstrap incluido

// Consulta de lavadores activos con conteo de servicios
$query = "
    SELECT u.id, u.nombre, u.imagen,
           COUNT(s.id) AS total_servicios,
           MAX(s.fecha) AS ultimo_servicio,
           SUM(DATE(s.fecha) = CURDATE()) AS trabajos_hoy
    FROM usuario u
    LEFT JOIN servicios s ON u.id = s.id_empleado
    GROUP BY u.id, u.nombre, u.imagen
    ORDER BY total_servicios DESC
";

$resultado = $con->query($query);
?>

<!-- AOS CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<link rel="stylesheet" href="../CSS/EmpleadosCSS/activos.css" />
<div class="content-wrapper">
  <section class="content-header">
    <h1>Lavadores Activos <small>Estado actual y desempeño</small></h1>
  </section>

  <section class="content">
    <div class="row">
      <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while ($lavador = $resultado->fetch_assoc()): ?>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= ($lavador['id'] % 3) * 100 ?>">
            <div class="card tarjeta-lavador shadow-sm">
              <div class="card-body text-center">
                <img 
                  src="../IMG/usuarios/<?= htmlspecialchars($lavador['imagen']) ?>" 
                  alt="Foto de <?= htmlspecialchars($lavador['nombre']) ?>"
                  onerror="this.onerror=null;this.src='../IMG/usuarios/default.png';"
                >
                <h4 class="mb-1"><?= htmlspecialchars($lavador['nombre']) ?></h4>
                <p class="text-muted mb-2">Horario: 8:00 a.m. - 6:00 p.m.</p>

                <p><strong>Servicios Totales:</strong> <?= $lavador['total_servicios'] ?></p>
                <p><strong>Último servicio:</strong> 
                  <?= $lavador['ultimo_servicio'] ? date('d/m/Y H:i', strtotime($lavador['ultimo_servicio'])) : 'Ninguno' ?>
                </p>

                <span class="badge <?= $lavador['trabajos_hoy'] > 0 ? 'badge-success' : 'badge-danger' ?>">
                  <?= $lavador['trabajos_hoy'] > 0 ? 'Disponible' : 'No disponible' ?>
                </span>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <p class="text-center text-muted">No se encontraron lavadores.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Gráfico de trabajadores por día (simulado) -->
    <div class="card mt-4">
      <div class="card-header">
        <h3 class="card-title">Gráfico: Lavadores por Día</h3>
      </div>
      <div class="card-body">
        <canvas id="graficoLavadores"></canvas>
        <!-- Aquí debes implementar el JS con datos reales -->
      </div>
    </div>
  </section>
</div>

<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
  });
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoLavadores').getContext('2d');
const grafico = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'], // Simulado
        datasets: [{
            label: 'Lavadores activos',
            data: [3, 4, 2, 5, 3], // ← REEMPLAZAR con datos reales desde PHP si lo deseas
            backgroundColor: 'rgba(0, 123, 255, 0.5)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1
            }
        },
        plugins: {
          legend: {
            labels: {
              font: {
                size: 14,
                weight: 'bold'
              }
            }
          }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
