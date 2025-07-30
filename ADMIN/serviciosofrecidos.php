<?php
// Incluir la cabecera HTML y conexión a la base de datos
include 'header.php';
include '../conexion.php';

// Consulta para obtener los tipos de lavado con todos los detalles necesarios, incluyendo el ID
$sql = "SELECT id, nombre, descripcion, precio, imagen FROM tipo_lavado";

// Ejecutar la consulta
$resultado = mysqli_query($con, $sql);

// Validar si la consulta tuvo errores y detener la ejecución en caso de fallo
if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<!-- Enlace al archivo CSS específico para mostrar los servicios disponibles -->
<link rel="stylesheet" href="../CSS/serviciosCSS/servicioactivo.css">

<!-- Contenedor principal del contenido -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>Servicios disponibles para tu vehículo</h1>
  </section>

  <section class="content">
    <div class="lavados-container">
      <!-- Verificar si hay tipos de lavado disponibles -->
      <?php if(mysqli_num_rows($resultado) === 0): ?>
        <p>No hay tipos de lavado disponibles.</p>
      <?php else: ?>
        <!-- Recorrer cada tipo de lavado para mostrarlo en la interfaz -->
        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
          <?php
            // Obtener el nombre de la imagen desde la base de datos y construir la ruta relativa
            $nombreImg = htmlspecialchars($fila['imagen']);
            $rutaRelativa = "/IMG/servicios/" . $nombreImg;

            // Construir la ruta absoluta para verificar si la imagen existe en el servidor
            $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . $rutaRelativa;

            // Si la imagen no existe físicamente, usar una imagen por defecto
            if (!file_exists($rutaAbsoluta)) {
                $rutaRelativa = "/IMG/servicios/LogoCarWash.png"; // Imagen por defecto
            }
          ?>
          <!-- Tarjeta visual de cada tipo de lavado -->
          <div class="card-lavado">
            <img src="<?php echo $rutaRelativa; ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
            <div class="contenido">
              <!-- Nombre del servicio -->
              <h4><?php echo htmlspecialchars($fila['nombre']); ?></h4>
              <!-- Descripción del servicio -->
              <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
              <!-- Precio del servicio con formato -->
              <p class="precio">$<?php echo number_format($fila['precio'], 2); ?></p>
              <!-- Enlace que redirige al formulario de creación con el tipo de lavado preseleccionado -->
              <a href="CrearServicio.php?id_tipo_lavado=<?php echo $fila['id']; ?>" class="agregar-servicio">+ Agregar servicio</a>     
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php 
// Incluir el pie de página HTML
include 'footer.php'; 
?>
