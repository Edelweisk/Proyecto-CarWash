<?php
require_once '../conexion.php';
include 'header.php';

// Obtener día actual en español (Lunes, Martes, etc.)
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
$indiceDiaHoy = date('N') - 1; // 0 para lunes ... 6 para domingo
$diaHoy = $diasSemana[$indiceDiaHoy];

// Obtener todos los lavadores activos con sus datos y servicios
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

// Armar lista de IDs para obtener disponibilidad
$idsLavadores = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $idsLavadores[] = $row['id'];
    }
    // Reiniciar puntero para reutilizar resultado
    $resultado->data_seek(0);
}

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

// Calcular lavadores disponibles por día para gráfica
$lavadoresPorDia = array_fill_keys($diasSemana, 0);
foreach ($disponibilidadLavadores as $dias) {
    foreach ($dias as $dia) {
        if (isset($lavadoresPorDia[$dia])) {
            $lavadoresPorDia[$dia]++;
        }
    }
}
?>

<!-- AOS CSS -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
<link rel="stylesheet" href="../CSS/EmpleadosCSS/activos.css" />

<div class="content-wrapper">
  <section class="content-header">
    <h1>Lavadores Disponibles | <small>Estado actual y programación semanal</small></h1>
  </section>

  <section class="content">
    <div class="row">
      <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while ($lavador = $resultado->fetch_assoc()): ?>
          <?php 
            $diasDisponibles = $disponibilidadLavadores[$lavador['id']] ?? [];
            $disponibleHoy = in_array($diaHoy, $diasDisponibles);
          ?>
          <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= ($lavador['id'] % 3) * 100 ?>">
            <div class="card tarjeta-lavador shadow-sm">
              <div class="card-body text-center">

            
                <a <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?> href="editarDisponibilidad.php?id=<?php endif; ?><?= $lavador['id'] ?>" style="text-decoration:none; color:inherit;">
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

    <!-- Gráfico de disponibilidad semanal -->
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

<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 800, easing: 'ease-in-out', once: true });</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('graficoLavadores').getContext('2d');
const grafico = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($diasSemana) ?>,
        datasets: [{
            label: 'Lavadores activos',
            data: <?= json_encode(array_values($lavadoresPorDia)) ?>,
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
