<?php
// Conexión a la base de datos
require_once('../conexion.php');

// Incluir la cabecera HTML y lógica general de la página
include 'header.php';

// Consulta para obtener todos los servicios junto con el nombre del empleado que lo realizó
$sql = "
    SELECT s.*, u.nombre AS empleado_nombre 
    FROM servicios s 
    LEFT JOIN usuario u ON s.id_empleado = u.id 
    ORDER BY s.fecha DESC
";
// Ejecutar la consulta
$result = $con->query($sql);
?>

<!-- Incluir estilos CSS específicos para la tabla de servicios -->
<link rel="stylesheet" href="../CSS/ServiciosCSS/Servicios.css" />
<!-- Incluir librería SweetAlert2 para diálogos y alertas bonitas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <!-- Título de la página -->
        <div class="col-sm-8">
          <h1>Listado de Servicios</h1>
        </div>
        <!-- Botón para crear un nuevo servicio -->
        <div class="col-sm-4 text-end">
          <a href="crearServicio.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Servicio
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-outline card-primary">
        <div class="card-body table-responsive">
          <!-- Tabla que muestra el listado de servicios -->
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
                <!-- Recorrer resultados y mostrar cada servicio -->
                <?php while ($fila = $result->fetch_assoc()): ?>
                  <tr id="fila-<?= $fila['id'] ?>">
                    <td><?= htmlspecialchars($fila['id']) ?></td>
                    <td><?= htmlspecialchars($fila['empleado_nombre'] ?? 'Desconocido') ?></td>
                    <td><?= htmlspecialchars($fila['tipo_carro']) ?></td>
                    <td><?= htmlspecialchars($fila['placa']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($fila['fecha'])) ?></td>
                    <td>
                      <!-- Botón para ver detalles del servicio -->
                      <a href="detalleServicio.php?id=<?= $fila['id']; ?>" class="btn btn-info btn-sm" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                      </a>
                      
                      <!-- Botones para editar y eliminar solo si el usuario es administrador -->
                      <?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?>
                        <!-- Botón para editar servicio -->
                        <a href="crearServicio.php?id=<?= $fila['id']; ?>" class="btn btn-warning btn-sm" title="Editar servicio">
                          <i class="fas fa-edit"></i>
                        </a>
                        <!-- Botón para eliminar servicio con confirmación -->
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?= $fila['id']; ?>)" title="Eliminar servicio">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <!-- Mensaje cuando no hay servicios -->
                <tr><td colspan="6" class="text-center">No hay servicios registrados.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
// Inicialización del plugin DataTables para tabla avanzada con botones de exportación
$(document).ready(function() {
  $('#tablaServicios').DataTable({
    dom: 'Bfrtip',  // Elementos del DOM para mostrar botones y filtros
    buttons: [
      {
        extend: 'excelHtml5',  // Botón para exportar a Excel
        text: '<i class="fas fa-file-excel"></i> Exportar Excel',
        className: 'btn btn-success btn-sm'
      },
      {
        extend: 'pdfHtml5',  // Botón para exportar a PDF
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

// Función para confirmar antes de eliminar un servicio
function confirmarEliminacion(id) {
  Swal.fire({
    title: '¿Eliminar este servicio?',
    text: "Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6'
  }).then((result) => {
    if (result.isConfirmed) {
      // Realiza llamada AJAX para eliminar el servicio
      fetch('eliminarServicio.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
      })
      .then(res => res.text())
      .then(data => {
        if (data === 'ok') {
          // Eliminar fila de la tabla si fue exitoso
          document.getElementById('fila-' + id).remove();
          Swal.fire('Eliminado', 'El servicio fue eliminado correctamente.', 'success');
        } else {
          Swal.fire('Error', 'Hubo un problema al eliminar el servicio.', 'error');
        }
      });
    }
  });
}
</script>

<?php include 'footer.php'; ?>
