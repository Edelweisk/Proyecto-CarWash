<?php
// Incluir conexión a base de datos
require_once('../conexion.php');
// Incluir plantilla header
include 'header.php';
// Incluir control de acceso / sesión
include '../control.php';

// Bloque para exportar el reporte en formato Excel si se recibe la petición ?export=excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    // Establecer cabeceras HTTP para descarga de archivo Excel
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=reporte_economico_debugcarwash.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Consulta: obtener sueldos mensuales actuales de técnicos de lavado
    $sueldosMensuales = $con->query("CALL reporte_sueldos_mensuales()");
    $con->next_result(); // Para evitar conflictos si se ejecutan más consultas

    // Consulta: obtener ganancias semanales actuales (semana actual) 
    $gananciasSemanales = $con->query("CALL reporte_ganancias_semanales()");
    $con->next_result();

    // Mostrar título del reporte
    echo "<h2>Reporte Económico - Debug Car Wash</h2>";

    // Mostrar tabla con sueldos mensuales
    echo "<h3>Sueldos Mensuales (Mes Actual)</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Empleado</th><th>Total Sueldo ($)</th></tr>";

    // Variable para acumulado total sueldos
    $totalSueldos = 0;
    while ($row = $sueldosMensuales->fetch_assoc()) {
        $total = $row['total_sueldo'] ?? 0;
        echo "<tr><td>{$row['nombre']}</td><td align='right'>" . number_format($total, 2) . "</td></tr>";
        $totalSueldos += $total;
    }
    // Mostrar total general de sueldos
    echo "<tr><th>Total General</th><th align='right'>" . number_format($totalSueldos, 2) . "</th></tr>";
    echo "</table><br>";

    // Mostrar tabla con ganancias semanales
    echo "<h3>Ganancias Semanales (Semana Actual)</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Semana</th><th>Total Ganancias ($)</th></tr>";

    // Variable para acumulado total ganancias
    $totalGanancias = 0;
    while ($row = $gananciasSemanales->fetch_assoc()) {
        $total = $row['total_ganancias'] ?? 0;
        echo "<tr><td>{$row['semana']}</td><td align='right'>" . number_format($total, 2) . "</td></tr>";
        $totalGanancias += $total;
    }
    // Mostrar total general de ganancias
    echo "<tr><th>Total General</th><th align='right'>" . number_format($totalGanancias, 2) . "</th></tr>";
    echo "</table>";
    exit(); // Terminar script tras exportar para no mostrar HTML
}

// Si no es exportación, cargar datos para mostrar en pantalla (HTML)

// Consulta sueldos mensuales actuales para mostrar tabla
$sueldosMensuales = $con->query("CALL reporte_sueldos_mensuales()");
    $con->next_result();

    $gananciasSemanales = $con->query("CALL reporte_ganancias_semanales()");
    $con->next_result();
?>

<!-- Contenido HTML del reporte -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>📊 Reporte Económico - Debug Car Wash</h1>
        <!-- Botones para imprimir y exportar -->
        <div class="mb-3">
            <button onclick="window.print()" class="btn btn-info">🖨️ Imprimir Reporte</button>
            <a href="?export=excel" class="btn btn-success">📊 Exportar a Excel</a>
        </div>
    </section>

    <section class="content">
        <!-- Tabla sueldos mensuales -->
        <div class="card p-4 shadow mb-4">
            <h3>Sueldos Mensuales (Mes Actual)</h3>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Empleado</th>
                        <th class="text-end">Total Sueldo ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalSueldos = 0;
                    while ($row = $sueldosMensuales->fetch_assoc()):
                        $total = $row['total_sueldo'] ?? 0;
                        $totalSueldos += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                            <td class="text-end"><?= number_format($total, 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="table-secondary fw-bold">
                        <td>Total General</td>
                        <td class="text-end"><?= number_format($totalSueldos, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabla ganancias semanales -->
        <div class="card p-4 shadow">
            <h3>Ganancias Semanales (Semana Actual)</h3>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Semana</th>
                        <th class="text-end">Total Ganancias ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalGanancias = 0;
                    while ($row = $gananciasSemanales->fetch_assoc()):
                        $total = $row['total_ganancias'] ?? 0;
                        $totalGanancias += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['semana']) ?></td>
                            <td class="text-end"><?= number_format($total, 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="table-secondary fw-bold">
                        <td>Total General</td>
                        <td class="text-end"><?= number_format($totalGanancias, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Estilos específicos para impresión -->
<style>
@media print {
    body {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        background: white !important;
        color: black !important;
    }
    /* Ocultar botones y cabeceras en impresión */
    .btn, .content-header > div {
        display: none !important;
    }
    /* Mejorar estilo de tablas en impresión */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    table, th, td {
        border: 1px solid black !important;
    }
    th, td {
        padding: 8px !important;
        text-align: left !important;
    }
}
</style>

<!-- Incluir footer de plantilla -->
<?php include 'footer.php'; ?>
