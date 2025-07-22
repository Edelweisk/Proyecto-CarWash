<?php
require_once('../conexion.php');
include 'header.php';
include '../control.php';

// Exportar a Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=reporte_economico_debugcarwash.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    $sueldosMensuales = $con->query("
        SELECT u.nombre, SUM(se.monto) AS total_sueldo
        FROM usuario u
        LEFT JOIN salario_empleado se ON u.id = se.id_usuario
        WHERE u.rol = 'TecnicoLavado'
        AND MONTH(se.fechaRegistro) = MONTH(CURRENT_DATE())
        AND YEAR(se.fechaRegistro) = YEAR(CURRENT_DATE())
        GROUP BY u.id
    ");

    $gananciasSemanales = $con->query("
        SELECT YEARWEEK(fecha, 1) as semana, SUM(tl.precio) AS total_ganancias
        FROM servicios s
        INNER JOIN tipo_lavado tl ON s.id_tipo_lavado = tl.id
        WHERE YEAR(fecha) = YEAR(CURRENT_DATE())
        AND WEEK(fecha, 1) = WEEK(CURRENT_DATE(), 1)
        GROUP BY semana
    ");

    echo "<h2>Reporte Económico - Debug Car Wash</h2>";

    echo "<h3>Sueldos Mensuales (Mes Actual)</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Empleado</th><th>Total Sueldo ($)</th></tr>";

    $totalSueldos = 0;
    while ($row = $sueldosMensuales->fetch_assoc()) {
        $total = $row['total_sueldo'] ?? 0;
        echo "<tr><td>{$row['nombre']}</td><td align='right'>" . number_format($total, 2) . "</td></tr>";
        $totalSueldos += $total;
    }
    echo "<tr><th>Total General</th><th align='right'>" . number_format($totalSueldos, 2) . "</th></tr>";
    echo "</table><br>";

    echo "<h3>Ganancias Semanales (Semana Actual)</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Semana</th><th>Total Ganancias ($)</th></tr>";

    $totalGanancias = 0;
    while ($row = $gananciasSemanales->fetch_assoc()) {
        $total = $row['total_ganancias'] ?? 0;
        echo "<tr><td>{$row['semana']}</td><td align='right'>" . number_format($total, 2) . "</td></tr>";
        $totalGanancias += $total;
    }
    echo "<tr><th>Total General</th><th align='right'>" . number_format($totalGanancias, 2) . "</th></tr>";
    echo "</table>";
    exit();
}

// Mostrar reporte en pantalla (HTML)
$sueldosMensuales = $con->query("
    SELECT u.nombre, SUM(se.monto) AS total_sueldo
    FROM usuario u
    LEFT JOIN salario_empleado se ON u.id = se.id_usuario
    WHERE u.rol = 'TecnicoLavado'
    AND MONTH(se.fechaRegistro) = MONTH(CURRENT_DATE())
    AND YEAR(se.fechaRegistro) = YEAR(CURRENT_DATE())
    GROUP BY u.id
");

$gananciasSemanales = $con->query("
    SELECT YEARWEEK(fecha, 1) as semana, SUM(tl.precio) AS total_ganancias
    FROM servicios s
    INNER JOIN tipo_lavado tl ON s.id_tipo_lavado = tl.id
    WHERE YEAR(fecha) = YEAR(CURRENT_DATE())
    AND WEEK(fecha, 1) = WEEK(CURRENT_DATE(), 1)
    GROUP BY semana
");
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>📊 Reporte Económico - Debug Car Wash</h1>
        <div class="mb-3">
            <button onclick="window.print()" class="btn btn-info">🖨️ Imprimir Reporte</button>
            <a href="?export=excel" class="btn btn-success">📊 Exportar a Excel</a>
        </div>
    </section>

    <section class="content">
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

<style>
@media print {
    body {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        background: white !important;
        color: black !important;
    }
    .btn, .content-header > div {
        display: none !important;
    }
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

<?php include 'footer.php'; ?>
