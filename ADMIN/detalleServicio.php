<?php
// Importamos la conexión a la base de datos y la cabecera común del sistema (barra de navegación, estilos, etc.)
require_once('../conexion.php');
include 'header.php';

// Obtenemos el ID del servicio desde la URL. Si no se envía, asumimos 1 por defecto.
// Esto es útil para evitar errores si alguien entra sin un parámetro.
$idServicio = $_GET['id'] ?? 1;

// Verificamos que el ID recibido sea un número válido para evitar inyecciones u errores lógicos.
if (!is_numeric($idServicio)) {
    echo '<div class="alert alert-danger m-4">ID de servicio no válido.</div>';
    include 'footer.php';
    exit;
}

// Convertimos el ID a entero para asegurarnos que sea tratado como número en la consulta.
$idServicio = (int) $idServicio;

// Preparamos una consulta SQL para extraer información detallada del servicio:
// - Datos del servicio en sí (tabla 'servicios')
// - Información del empleado asignado (JOIN con 'usuario')
// - Información del tipo de lavado (JOIN con 'tipo_lavado')
// Esto nos permite mostrar todos los datos relacionados sin hacer múltiples consultas.
$stmt = $con->prepare("
    SELECT 
        s.*, 
        u.nombre AS empleado_nombre, 
        u.email AS empleado_email, 
        u.usuario AS empleado_usuario, 
        u.imagen AS empleado_imagen,
        tl.nombre AS tipo_lavado_nombre,
        tl.precio AS tipo_lavado_precio
    FROM servicios s
    LEFT JOIN usuario u ON s.id_empleado = u.id
    LEFT JOIN tipo_lavado tl ON s.id_tipo_lavado = tl.id
    WHERE s.id = ?
");

// Asociamos el ID recibido a la consulta preparada para prevenir inyecciones SQL.
$stmt->bind_param('i', $idServicio);
$stmt->execute();
$result = $stmt->get_result();

// Validamos si se encontró o no un servicio con ese ID. Si no existe, se muestra un mensaje.
if ($result->num_rows === 0) {
    echo '<div class="alert alert-warning m-4">No se encontró el servicio solicitado.</div>';
    include 'footer.php';
    exit;
}

// Si el servicio existe, guardamos todos sus datos en un array asociativo para mostrar en la vista.
$servicio = $result->fetch_assoc();
?>

<!-- Cargamos una hoja de estilos específica para personalizar la vista de detalle -->
<link rel="stylesheet" href="../CSS/serviciosCSS/verservicio.css" />

<!-- Contenedor general de contenido -->
<div class="content-wrapper">
  <!-- Sección superior: título de la página y botón de retorno -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-3 align-items-center">
        <div class="col-sm-8">
          <h1 class="text-primary">
            Detalle del Servicio <small class="text-muted">#<?= htmlspecialchars($servicio['id']); ?></small>
          </h1>
        </div>
        <div class="col-sm-4 text-end">
          <!-- Botón para regresar a la lista de servicios -->
          <a href="servicios.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Servicios
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección principal: cuerpo de la información del servicio -->
  <section class="content">
    <div class="container-fluid">

      <!-- Tarjeta que contiene los detalles del servicio -->
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title">Información Detallada del Servicio</h3>
        </div>
        <div class="card-body">

          <div class="row">
            <!-- Columna 1: Imagen e información del empleado -->
            <div class="col-md-3 text-center mb-4">
              <?php
                // Si el empleado tiene imagen y el archivo existe físicamente, la mostramos.
                // De lo contrario, se muestra un ícono genérico.
                $imgPath = "../uploads/" . $servicio['empleado_imagen'];
                if (!empty($servicio['empleado_imagen']) && file_exists($imgPath)):
              ?>
                  <img src="<?= htmlspecialchars($imgPath); ?>" class="img-fluid rounded-circle shadow mb-2" style="max-width: 150px;" alt="Empleado">
              <?php else: ?>
                  <i class="fas fa-user-circle fa-9x text-muted mb-2"></i>
              <?php endif; ?>

              <!-- Datos básicos del empleado -->
              <h5 class="fw-bold"><?= htmlspecialchars($servicio['empleado_nombre'] ?? 'Empleado desconocido'); ?></h5>
              <p class="mb-0 text-muted"><?= htmlspecialchars($servicio['empleado_usuario'] ?? ''); ?></p>
              <p class="text-muted"><?= htmlspecialchars($servicio['empleado_email'] ?? ''); ?></p>
            </div>

            <!-- Columna 2: Tabla con todos los detalles del servicio -->
            <div class="col-md-9">
              <table class="table table-hover table-bordered table-striped">
                <tbody>
                  <!-- Cada fila representa un dato clave del servicio -->
                  <tr>
                    <th style="width: 30%;">Tipo de Lavado</th>
                    <td><?= htmlspecialchars($servicio['tipo_lavado_nombre'] ?? 'N/A'); ?></td>
                  </tr>
                  <tr>
                    <th>Precio</th>
                    <td>$<?= number_format((float)($servicio['tipo_lavado_precio'] ?? 0), 2); ?></td>
                  </tr>
                  <tr>
                    <th>Tipo de Vehículo</th>
                    <td><?= htmlspecialchars($servicio['tipo_carro']); ?></td>
                  </tr>
                  <tr>
                    <th>Placa del Vehículo</th>
                    <td><?= htmlspecialchars($servicio['placa']); ?></td>
                  </tr>
                  <tr>
                    <th>Fecha y Hora del Servicio</th>
                    <td><?= date('d/m/Y H:i:s', strtotime($servicio['fecha'])); ?></td>
                  </tr>
                  <tr>
                    <th>Estado</th>
                    <td><?= ucfirst(htmlspecialchars($servicio['estado'])); ?></td>
                  </tr>
                  <tr>
                    <th>Método de Pago</th>
                    <td><?= ucfirst(htmlspecialchars($servicio['metodo_pago'] ?? 'No especificado')); ?></td>
                  </tr>
                  <tr>
                    <th>Observaciones</th>
                    <!-- Convertimos saltos de línea a etiquetas <br> para mantener el formato -->
                    <td><?= nl2br(htmlspecialchars($servicio['observaciones'] ?? '-')); ?></td>
                  </tr>
                </tbody>
              </table>

              <!-- Botón para editar el servicio actual -->
              <div class="text-end">
                <a href="crearServicio.php?id=<?= $servicio['id']; ?>" class="btn btn-warning">
                  <i class="fas fa-edit"></i> Editar Servicio
                </a>
              </div>

            </div> <!-- Fin columna derecha -->
          </div> <!-- Fin fila -->
        </div> <!-- Fin card-body -->
      </div> <!-- Fin card -->

    </div> <!-- Fin container-fluid -->
  </section>
</div>

<!-- Pie de página común del sistema -->
<?php include 'footer.php'; ?>
