<?php
require_once('../conexion.php');
include 'header.php';
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" />
<link rel="stylesheet" href="../CSS/EmpleadosCSS/Empleado.css" />

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="fw-bold">Gestión de Empleados</h1>
        </div>
        <div class="col-sm-6 text-end">
          <a href="Registrar_Empleado.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Empleado
          </a>
        </div>
      </div>
    </div>
    
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm rounded border-0">
            <div class="card-body">
              <table id="usuariosTable" class="table table-striped table-bordered table-hover" style="width:100%">
                <thead class="table-primary text-center">
                  <tr>
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
                  $sql = "SELECT * FROM usuario ORDER BY fechaRegistro DESC";
                  $result = $con->query($sql);
                  $contador = 1;
                  while ($fila = $result->fetch_assoc()) {
                    $foto = !empty($fila['imagen']) && file_exists("../IMG/usuarios/" . $fila['imagen'])
                      ? "../IMG/usuarios/" . $fila['imagen']
                      : "../IMG/usuarios/default.png";
                  ?>
                    <tr>
                      <td class="text-center"><?= $contador++; ?></td>
                      <td class="text-center"><img src="<?= $foto ?>" alt="Foto" width="50" height="50" class="rounded-circle"></td>
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
                      <td><code><?= htmlspecialchars($fila['password']); ?></code></td>
                      <td><?= htmlspecialchars($fila['rol']); ?></td>
                      <td>
                        <span class="badge bg-<?= $fila['estado'] === 'activo' ? 'success' : 'secondary'; ?>">
                          <?= ucfirst($fila['estado']); ?>
                        </span>
                      </td>
                      <td><?= date('d/m/Y H:i', strtotime($fila['fechaRegistro'])); ?></td>
                      <td class="text-center">
                        <a href="editarUsuarios.php?id=<?= $fila['id']; ?>" class="btn btn-sm btn-warning me-1" title="Editar usuario">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $fila['id']; ?>" title="Eliminar usuario">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Scripts necesarios -->
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
    $('#usuariosTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
      },
      lengthMenu: [5, 10, 25, 50],
      pageLength: 10,
      responsive: true,
      order: [[15, 'desc']],
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'copyHtml5',
          text: '<i class="fas fa-copy"></i> Copiar',
          className: 'btn btn-secondary btn-sm'
        },
        {
          extend: 'excelHtml5',
          text: '<i class="fas fa-file-excel"></i> Excel',
          className: 'btn btn-success btn-sm'
        }
      ]
    });

    $('.btn-eliminar').click(function() {
      const userId = $(this).data('id');
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
          window.location.href = `eliminarUsuario.php?id=${userId}`;
        }
      });
    });
  });

  
</script>

<?php include 'footer.php'; ?>
