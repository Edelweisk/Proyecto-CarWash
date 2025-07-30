<?php
// Conexión a la base de datos
require_once('../conexion.php');

// Control de sesión o acceso (seguridad)
include '../control.php';

// Header común del panel
include 'header.php';

// ================================
// Obtener el presupuesto máximo definido (último valor en la tabla salario_limite)
// Si no hay ninguno, se establece por defecto en $10,000
// ================================
$resLimite = $con->query("SELECT monto_maximo FROM salario_limite ORDER BY id DESC LIMIT 1");
$presupuestoMaximo = $resLimite->fetch_assoc()['monto_maximo'] ?? 10000;

// ================================
// Procesamiento del formulario al hacer POST
// ================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sueldos'])) {
    $totalAsignado = 0;

    // Sumar todos los sueldos asignados
    foreach ($_POST['sueldos'] as $id => $sueldo) {
        $sueldo = floatval($sueldo); // Convertir a número flotante
        $totalAsignado += $sueldo;
    }

    // Validar si el total excede el presupuesto
    if ($totalAsignado > $presupuestoMaximo) {
        echo "<script>
            alert('⚠️ Error: El total asignado ($' + $totalAsignado.toFixed(2) + ') supera el presupuesto máximo de $presupuestoMaximo.');
            window.history.back();
        </script>";
        exit();
    } else {
        // Asignar/actualizar sueldo para cada empleado
        foreach ($_POST['sueldos'] as $id => $sueldo) {
            // Verificar si el empleado ya tiene un registro de sueldo
            $check = $con->prepare("SELECT id FROM salario_empleado WHERE id_usuario = ? ORDER BY fechaRegistro DESC LIMIT 1");
            $check->bind_param("i", $id);
            $check->execute();
            $resCheck = $check->get_result();

            if ($resCheck->num_rows > 0) {
                // Si ya tiene registro: actualizar el más reciente
                $row = $resCheck->fetch_assoc();
                $update = $con->prepare("UPDATE salario_empleado SET monto = ?, fechaRegistro = NOW() WHERE id = ?");
                $update->bind_param("di", $sueldo, $row['id']);
                $update->execute();
            } else {
                // Si no tiene: insertar nuevo sueldo
                $insert = $con->prepare("INSERT INTO salario_empleado (id_usuario, monto) VALUES (?, ?)");
                $insert->bind_param("id", $id, $sueldo);
                $insert->execute();
            }
        }

        // Confirmar éxito y redirigir
        echo "<script>
            alert('✅ Sueldos actualizados correctamente. Total asignado: $$totalAsignado');
            window.location.href = 'asignarsueldo.php';
        </script>";
        exit();
    }
}
?>


<!-- Contenedor principal del contenido -->
<div class="content-wrapper">
    <!-- Encabezado de la sección -->
    <section class="content-header">
        <h1>💼 Gestión de Sueldos <small>Control de presupuesto mensual</small></h1>
    </section>

    <!-- Contenido principal -->
    <section class="content">
        <div class="card p-4 shadow">
            <!-- Mostrar presupuesto disponible -->
            <h4 class="mb-3">Presupuesto disponible: 
              <span class="text-success">$<?= number_format($presupuestoMaximo, 2) ?></span></h4>

            <!-- Formulario para asignar sueldos -->
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
                        // ================================
                        // Consultar empleados y su sueldo actual (si tiene)
                        // ================================
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

                            // Campo de entrada para cada sueldo por empleado
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

                <!-- Alerta por exceso de presupuesto -->
                <div class="alert alert-warning limit-alert" id="alertaLimite" style="display: none;"></div>

                <!-- Total calculado en tiempo real -->
                <p><strong>Total Asignado:</strong> $<span id="totalAsignado">0.00</span></p>

                <!-- Botón de envío -->
                <button type="submit" class="btn btn-primary" id="btnGuardar" disabled>💾 Guardar Sueldos</button>
            </form>
        </div>
    </section>
</div>


<script>
    // Elementos clave
    const inputs = document.querySelectorAll(".sueldo-input");
    const totalSpan = document.getElementById("totalAsignado");
    const alerta = document.getElementById("alertaLimite");
    const btnGuardar = document.getElementById("btnGuardar");

    // Presupuesto máximo enviado desde PHP
    const presupuestoMaximo = <?= json_encode($presupuestoMaximo) ?>;

    // Función para actualizar el total asignado y validar contra el presupuesto
    function actualizarTotal() {
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        totalSpan.textContent = total.toFixed(2);

        // Mostrar alerta si se excede el presupuesto
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

    // Agrega eventos a todos los inputs para recalcular al cambiar valor
    inputs.forEach(input => input.addEventListener("input", actualizarTotal));

    // Inicializa el total al cargar la página
    actualizarTotal();
</script>


<?php include 'footer.php'; ?>
