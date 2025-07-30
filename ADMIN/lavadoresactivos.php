<?php
require_once '../conexion.php'; // Conexión a la base de datos
include 'header.php'; // Inclusión del encabezado de la página (navbar, estilos, etc.)

// Obtener el día actual en inglés y traducirlo al español para usarlo más adelante
$diaHoy = [
    'Monday'    => 'Lunes',
    'Tuesday'   => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday'  => 'Jueves',
    'Friday'    => 'Viernes',
    'Saturday'  => 'Sábado',
    'Sunday'    => 'Domingo',
][date('l')];

// Lista completa de días en español para usar como referencia
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

// Consulta SQL para obtener todos los lavadores activos, con total de servicios,
// última fecha de servicio y cuántos trabajos ha hecho hoy
$query = "
    SELECT u.id, u.nombre, u.imagen,
           COUNT(s.id) AS total_servicios,
           MAX(s.fecha) AS ultimo_servicio,
           SUM(DATE(s.fecha) = CURDATE()) AS trabajos_hoy
    FROM usuario u
    LEFT JOIN servicios s ON u.id = s.id_empleado
    WHERE u.rol = 'TecnicoLavado' AND u.estado = 'activo'
    GROUP BY u.id, u.nombre, u.imagen
    ORDER BY total_servicios DESC
";

$resultado = $con->query($query);

// Preparar arreglo con los IDs de los lavadores encontrados
$idsLavadores = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $idsLavadores[] = $row['id'];
    }
    $resultado->data_seek(0); // Reiniciar el puntero para poder iterar nuevamente
}

// Obtener la disponibilidad por día de los lavadores
$disponibilidadLavadores = [];
if (count($idsLavadores) > 0) {
    $idsStr = implode(',', $idsLavadores);
    $queryDisp = "
        SELECT id_lavador, dia_semana 
        FROM lavadores_disponibilidad 
        WHERE id_lavador IN ($idsStr) AND estado = 'activo'
    ";
    $resDisp = $con->query($queryDisp);
    while ($filaDisp = $resDisp->fetch_assoc()) {
        $disponibilidadLavadores[$filaDisp['id_lavador']][] = $filaDisp['dia_semana'];
    }
}

// Contar cuántos lavadores hay por día para el gráfico de barras
$lavadoresPorDia = array_fill_keys($diasSemana, 0);
foreach ($disponibilidadLavadores as $dias) {
    foreach ($dias as $dia) {
        if (isset($lavadoresPorDia[$dia])) {
            $lavadoresPorDia[$dia]++;
        }
    }
}
?>

<!-- Carga de estilos para animaciones y estilos personalizados -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<link rel="stylesheet" href="../CSS/EmpleadosCSS/activos.css" />

<div class="content-wrapper">
  <!-- Título de la sección -->
  <section class="content-header">
    <h1>Lavadores Disponibles | <small>Estado actual y programación semanal</small></h1>
  </section>

  <section class="content">
    <div class="row">
      <!-- Mostrar cada lavador en una tarjeta -->
      <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while ($lavador = $resultado->fetch_assoc()): ?>
          <?php 
            $diasDisponibles = $disponibilidadLavadores[$lavador['id']] ?? []; // Dias de disponibilidad por lavador
            $disponibleHoy = in_array($diaHoy, $diasDisponibles); // Si está disponible hoy
          ?>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= ($lavador['id'] % 3) * 100 ?>">
            <div class="card tarjeta-lavador shadow-sm">
              <div class="card-body text-center">

                <!-- Link solo si es administrador -->
                <a <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?> href="editarDisponibilidad.php?id=<?= $lavador['id'] ?>" <?php endif; ?> style="text-decoration:none; color:inherit;">
                  <!-- Imagen del lavador o default si no existe -->
                  <img 
                    src="../IMG/usuarios/<?= htmlspecialchars($lavador['imagen']) ?>" 
                    alt="Foto de <?= htmlspecialchars($lavador['nombre']) ?>"
                    onerror="this.onerror=null;this.src='../IMG/usuarios/default.png';"
                    style="max-width: 150px; border-radius: 50%;"
                  >
                  <h4 class="mb-1"><?= htmlspecialchars($lavador['nombre']) ?></h4>
                </a>

                <p class="text-muted mb-2">Horario: 8:00 a.m. - 6:00 p.m.</p>

                <p><strong>Servicios Totales:</strong> <?= $lavador['total_servicios'] ?></p>
                <p><strong>Último servicio:</strong> 
                  <?= $lavador['ultimo_servicio'] ? date('d/m/Y H:i', strtotime($lavador['ultimo_servicio'])) : 'Ninguno' ?>
                </p>

                <span class="badge <?= $disponibleHoy ? 'badge-success' : 'badge-danger' ?>">
                  <?= $disponibleHoy ? 'Disponible hoy' : 'No disponible hoy' ?>
                </span>

                <hr>
                <p><strong>Días disponibles:</strong></p>
                <?php 
                  if (count($diasDisponibles) > 0):
                    foreach ($diasSemana as $dia):
                      if (in_array($dia, $diasDisponibles)):
                        echo "<span class='badge bg-primary me-1'>$dia</span>";
                      endif;
                    endforeach;
                  else:
                    echo "<span class='text-muted'>No configurado</span>";
                  endif;
                ?>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <p class="text-center text-muted">No se encontraron lavadores disponibles.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Gráfico resumen de disponibilidad semanal -->
    <div class="card mt-4">
      <div class="card-header">
        <h3 class="card-title">Gráfico: Lavadores activos por día</h3>
      </div>
      <div class="card-body">
        <canvas id="graficoLavadores"></canvas>
      </div>
    </div>
  </section>
</div>

<!-- Librerías para animaciones y gráficos -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 800, easing: 'ease-in-out', once: true });</script>

<!-- Chart.js para el gráfico de barras -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoLavadores').getContext('2d');
const grafico = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($diasSemana) ?>, // Etiquetas del eje X
        datasets: [{
            label: 'Lavadores activos',
            data: <?= json_encode(array_values($lavadoresPorDia)) ?>, // Datos del eje Y
            backgroundColor: 'rgba(40, 167, 69, 0.5)',
            borderColor: 'rgba(40, 167, 69, 1)',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, stepSize: 1 }
        },
        plugins: {
            legend: {
                labels: { font: { size: 14, weight: 'bold' } }
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>
