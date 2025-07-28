<?php
// Incluye la conexión a la base de datos y la cabecera común HTML
require_once('../conexion.php');
include 'header.php';
?>

<!-- Importación de estilos CSS para DataTables y estilos personalizados -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" />
<link rel="stylesheet" href="../CSS/EmpleadosCSS/Empleado.css" />

<div class="content-wrapper">
  <!-- Sección del encabezado del contenido -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <!-- Título principal de la página -->
          <h1 class="fw-bold">Gestión de Empleados</h1>
        </div>
        <div class="col-sm-6 text-end">
          <!-- Botón para registrar un nuevo empleado -->
          <a href="Registrar_Empleado.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Empleado
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección principal del contenido donde se muestra la tabla -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- Tarjeta que contiene la tabla de empleados -->
          <div class="card shadow-sm rounded border-0">
            <div class="card-body">
              <!-- Tabla que muestra los empleados con id, foto, datos personales, rol, estado y acciones -->
              <table id="usuariosTable" class="table table-striped table-bordered table-hover" style="width:100%">
                <thead class="table-primary text-center">
                  <tr>
                    <!-- Encabezados de columnas -->
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Fecha Nacimiento</th>
                    <th>Identificación</th>
                    <th>Nacionalidad</th>
                    <th>Estado Civil</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Emergencia</th>
                    <th>Correo</th>
                    <th>Usuario</th>
                    <th class="text-danger">Contraseña</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Consulta para obtener todos los usuarios ordenados por fecha de registro descendente
                  $sql = "SELECT * FROM usuario ORDER BY fechaRegistro DESC";
                  $result = $con->query($sql);
                  $contador = 1; // Contador para numerar las filas

                  // Recorrer cada fila de resultado para llenar la tabla
                  while ($fila = $result->fetch_assoc()) {
                    // Definir la ruta de la foto: si no existe imagen, usar imagen por defecto
                    $foto = !empty($fila['imagen']) && file_exists("../IMG/usuarios/" . $fila['imagen'])
                      ? "../IMG/usuarios/" . $fila['imagen']
                      : "../IMG/usuarios/default.png";
                  ?>
                    <tr>
                      <!-- Numeración -->
                      <td class="text-center"><?= $contador++; ?></td>
                      <!-- Foto redonda del usuario -->
                      <td class="text-center"><img src="<?= $foto ?>" alt="Foto" width="50" height="50" class="rounded-circle"></td>
                      <!-- Datos personales y de usuario con protección HTML para evitar XSS -->
                      <td><?= htmlspecialchars($fila['nombre']); ?></td>
                      <td><?= date('d/m/Y', strtotime($fila['fecha_nacimiento'])); ?></td>
                      <td><?= htmlspecialchars($fila['numero_identificacion']); ?></td>
                      <td><?= htmlspecialchars($fila['nacionalidad']); ?></td>
                      <td><?= htmlspecialchars($fila['estado_civil']); ?></td>
                      <td><?= htmlspecialchars($fila['direccion']); ?></td>
                      <td><?= htmlspecialchars($fila['telefono']); ?></td>
                      <td><?= htmlspecialchars($fila['numero_emergencia']); ?></td>
                      <td><?= htmlspecialchars($fila['email']); ?></td>
                      <td><?= htmlspecialchars($fila['usuario']); ?></td>
                      <!-- Mostrar la contraseña con formato de código (ojo: no recomendado mostrar contraseñas en texto plano en producción) -->
                      <td><code><?= htmlspecialchars($fila['password']); ?></code></td>
                      <td><?= htmlspecialchars($fila['rol']); ?></td>
                      <!-- Estado con badge de color según activo o inactivo -->
                      <td>
                        <span class="badge bg-<?= $fila['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                          <?= ucfirst($fila['estado']); ?>
                        </span>
                      </td>
                      <!-- Fecha y hora del registro -->
                      <td><?= date('d/m/Y H:i', strtotime($fila['fechaRegistro'])); ?></td>
                      <!-- Botones para editar y eliminar usuario -->
                      <td class="text-center">
                        <a href="editarUsuarios.php?id=<?= $fila['id']; ?>" class="btn btn-sm btn-warning me-1" title="Editar usuario">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $fila['id']; ?>" title="Eliminar usuario">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                  <?php } // Fin del while ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Inclusión de scripts JS externos para DataTables, exportación, y alertas -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function() {
    // Inicializar DataTable con opciones personalizadas
    $('#usuariosTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' // Traducción al español
      },
      lengthMenu: [5, 10, 25, 50], // Opciones de filas por página
      pageLength: 10, // Número de filas por defecto
      responsive: true, // Responsive para diferentes dispositivos
      order: [[15, 'desc']], // Ordenar inicialmente por fecha de registro (columna 16 - índice 15) descendente
      dom: 'Bfrtip', // Control del layout de botones, filtro, tabla, etc.
      buttons: [
        {
          extend: 'copyHtml5', // Botón para copiar tabla al portapapeles
          text: '<i class="fas fa-copy"></i> Copiar',
          className: 'btn btn-secondary btn-sm'
        },
        {
          extend: 'excelHtml5', // Botón para exportar tabla a Excel
          text: '<i class="fas fa-file-excel"></i> Excel',
          className: 'btn btn-success btn-sm'
        }
      ]
    });

    // Delegación de evento para botón eliminar usuario con confirmación SweetAlert2
    $('#usuariosTable').on('click', '.btn-eliminar', function() {
      const userId = $(this).data('id'); // Obtener el ID del usuario a eliminar
      Swal.fire({
        title: '¿Estás seguro?',
        text: "¡Esta acción eliminará el usuario permanentemente!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirigir a script PHP que elimina el usuario pasando el ID
          window.location.href = `eliminarUsuario.php?id=${userId}`;
        }
      });
    });
  });
</script>

<?php 
// Incluir el pie de página HTML común
include 'footer.php'; 
?>
