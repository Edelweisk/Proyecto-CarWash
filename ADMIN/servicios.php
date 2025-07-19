<?php
require_once('../conexion.php');
include 'header.php';

// Consulta para obtener todos los servicios junto con el nombre del empleado
$sql = "
    SELECT s.*, u.nombre AS empleado_nombre 
    FROM servicios s 
    LEFT JOIN usuario u ON s.id_empleado = u.id 
    ORDER BY s.fecha DESC
";
$result = $con->query($sql);
?>

<link rel="stylesheet" href="../CSS/ServiciosCSS/Servicios.css" />

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-8">
          <h1>Listado de Servicios</h1>
        </div>
        <div class="col-sm-4 text-end">
          <a href="CrearServicio.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Servicio</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-outline card-primary">
        <div class="card-body table-responsive p-0" style="max-height: 500px;">
          <table class="table table-hover table-bordered text-nowrap">
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
                    <td><?php echo htmlspecialchars($fila['id']); ?></td>
                    <td><?php echo htmlspecialchars($fila['empleado_nombre'] ?? 'Desconocido'); ?></td>
                    <td><?php echo htmlspecialchars($fila['tipo_carro']); ?></td>
                    <td><?php echo htmlspecialchars($fila['placa']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($fila['fecha'])); ?></td>
                    <td>
                      <a href="detalleServicio.php?id=<?php echo $fila['id']; ?>" class="btn btn-info btn-sm" title="Ver Detalles">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="guardarServicio.php?id=<?php echo $fila['id']; ?>" class="btn btn-warning btn-sm" title="Editar Servicio">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="eliminarServicio.php?id=<?php echo $fila['id']; ?>" class="btn btn-danger btn-sm" title="Eliminar Servicio" onclick="return confirm('¿Seguro que desea eliminar este servicio?');">
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

<?php include 'footer.php'; ?>
