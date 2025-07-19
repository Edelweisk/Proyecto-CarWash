<?php
require_once('../conexion.php');
include 'header.php';

$sql = "
    SELECT s.*, u.nombre AS empleado_nombre 
    FROM servicios s 
    LEFT JOIN usuario u ON s.id_empleado = u.id 
    ORDER BY s.fecha DESC
";
$result = $con->query($sql);
?>

<!-- Estilos específicos -->
<link rel="stylesheet" href="../CSS/ServiciosCSS/Servicios.css" />

<div class="content-wrapper">
  <!-- Encabezado -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-8">
          <h1>Listado de Servicios</h1>
        </div>
        <div class="col-sm-4 text-end">
          <a href="crearServicio.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Servicio
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Tabla de servicios -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-outline card-primary">
        <div class="card-body table-responsive">
          <table id="tablaServicios" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>Tipo de carro</th>
                <th>Placa</th>
                <th>Fecha y hora</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($fila = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($fila['id']) ?></td>
                    <td><?= htmlspecialchars($fila['empleado_nombre'] ?? 'Desconocido') ?></td>
                    <td><?= htmlspecialchars($fila['tipo_carro']) ?></td>
                    <td><?= htmlspecialchars($fila['placa']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($fila['fecha'])) ?></td>
                    <td>
                      <a href="detalleServicio.php?id=<?= $fila['id']; ?>" class="btn btn-info btn-sm" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="crearServicio.php?id=<?= $fila['id']; ?>" class="btn btn-warning btn-sm" title="Editar servicio">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="eliminarServicio.php?id=<?= $fila['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar este servicio?');" title="Eliminar servicio">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">No hay servicios registrados.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- DataTables con exportación -->
<script>
$(document).ready(function() {
  $('#tablaServicios').DataTable({
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '<i class="fas fa-file-excel"></i> Exportar Excel',
        className: 'btn btn-success btn-sm'
      },
      {
        extend: 'pdfHtml5',
        text: '<i class="fas fa-file-pdf"></i> Exportar PDF',
        className: 'btn btn-danger btn-sm'
      }
    ],
    language: {
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron registros",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior"
      }
    }
  });
});
</script>

<?php include 'footer.php'; ?>
