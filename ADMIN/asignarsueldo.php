<?php
require_once('../conexion.php');
include '../control.php';
include 'header.php';

// Obtener el presupuesto máximo desde la tabla salario_limite
$resLimite = $con->query("SELECT monto_maximo FROM salario_limite ORDER BY id DESC LIMIT 1");
$presupuestoMaximo = $resLimite->fetch_assoc()['monto_maximo'] ?? 10000;

// Procesar actualización de sueldos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sueldos'])) {
    $totalAsignado = 0;

    foreach ($_POST['sueldos'] as $id => $sueldo) {
        $sueldo = floatval($sueldo);
        $totalAsignado += $sueldo;
    }

    if ($totalAsignado > $presupuestoMaximo) {
        echo "<script>
            alert('⚠️ Error: El total asignado ($' + $totalAsignado.toFixed(2) + ') supera el presupuesto máximo de $presupuestoMaximo.');
            window.history.back();
        </script>";
        exit();
    } else {
        foreach ($_POST['sueldos'] as $id => $sueldo) {
            // Revisar si ya tiene un registro de sueldo
            $check = $con->prepare("SELECT id FROM salario_empleado WHERE id_usuario = ? ORDER BY fechaRegistro DESC LIMIT 1");
            $check->bind_param("i", $id);
            $check->execute();
            $resCheck = $check->get_result();

            if ($resCheck->num_rows > 0) {
                // Actualizar el sueldo más reciente
                $row = $resCheck->fetch_assoc();
                $update = $con->prepare("UPDATE salario_empleado SET monto = ?, fechaRegistro = NOW() WHERE id = ?");
                $update->bind_param("di", $sueldo, $row['id']);
                $update->execute();
            } else {
                // Insertar nuevo registro de sueldo
                $insert = $con->prepare("INSERT INTO salario_empleado (id_usuario, monto) VALUES (?, ?)");
                $insert->bind_param("id", $id, $sueldo);
                $insert->execute();
            }
        }
        echo "<script>
            alert('✅ Sueldos actualizados correctamente. Total asignado: $$totalAsignado');
            window.location.href = 'asignarsueldo.php';
        </script>";
        exit();
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>💼 Gestión de Sueldos <small>Control de presupuesto mensual</small></h1>
    </section>

    <section class="content">
        <div class="card p-4 shadow">
            <h4 class="mb-3">Presupuesto disponible: <span class="text-success">$<?= number_format($presupuestoMaximo, 2) ?></span></h4>

            <form method="post" id="formSueldos">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Empleado</th>
                            <th>Asignar Sueldo ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Traer empleados con su último sueldo asignado (si existe)
                        $sql = "
                            SELECT u.id, u.nombre, se.monto
                            FROM usuario u
                            LEFT JOIN (
                                SELECT se1.id_usuario, se1.monto
                                FROM salario_empleado se1
                                INNER JOIN (
                                    SELECT id_usuario, MAX(fechaRegistro) AS max_fecha
                                    FROM salario_empleado
                                    GROUP BY id_usuario
                                ) se2 ON se1.id_usuario = se2.id_usuario AND se1.fechaRegistro = se2.max_fecha
                            ) se ON u.id = se.id_usuario
                            WHERE u.rol IN ('Secretaria', 'TecnicoLavado', 'administrador')
                            ORDER BY u.nombre ASC
                        ";
                        $result = $con->query($sql);
                        while ($emp = $result->fetch_assoc()) {
                            $sueldoActual = $emp['monto'] ?? 0;
                            echo "<tr>
                                <td>" . htmlspecialchars($emp['nombre']) . "</td>
                                <td>
                                    <input type='number' step='0.01' min='0' max='" . $presupuestoMaximo . "'
                                        name='sueldos[{$emp['id']}]'
                                        class='form-control sueldo-input'
                                        value='" . htmlspecialchars($sueldoActual) . "'>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="alert alert-warning limit-alert" id="alertaLimite" style="display: none;"></div>
                <p><strong>Total Asignado:</strong> $<span id="totalAsignado">0.00</span></p>

                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>💾 Guardar Sueldos</button>
            </form>
        </div>
    </section>
</div>

<script>
    const inputs = document.querySelectorAll(".sueldo-input");
    const totalSpan = document.getElementById("totalAsignado");
    const alerta = document.getElementById("alertaLimite");
    const btnGuardar = document.getElementById("btnGuardar");
    const presupuestoMaximo = <?= json_encode($presupuestoMaximo) ?>;

    function actualizarTotal() {
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        totalSpan.textContent = total.toFixed(2);

        if (total > presupuestoMaximo) {
            alerta.textContent = `⚠️ Te estás excediendo del presupuesto mensual ($${total.toFixed(2)} / $${presupuestoMaximo})`;
            alerta.style.display = 'block';
            alerta.classList.add('alert-danger');
            btnGuardar.disabled = true;
        } else {
            alerta.style.display = 'none';
            alerta.classList.remove('alert-danger');
            btnGuardar.disabled = false;
        }
    }

    inputs.forEach(input => input.addEventListener("input", actualizarTotal));
    actualizarTotal();
</script>

<?php include 'footer.php'; ?>
