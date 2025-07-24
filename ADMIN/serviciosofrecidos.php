<?php
include 'header.php';
include '../conexion.php';

$sql = "SELECT nombre, descripcion, precio, imagen FROM tipo_lavado";
$resultado = mysqli_query($con, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<link rel="stylesheet" href="../CSS/serviciosCSS/servicioactivo.css">
<div class="content-wrapper">
  <section class="content-header">
    <h1>Servicios disponibles para tu vehículo</h1>
  </section>

  <section class="content">
    <div class="lavados-container">

    <?php if(mysqli_num_rows($resultado) === 0): ?>
      <p>No hay tipos de lavado disponibles.</p>
    <?php else: ?>
      <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
        <?php
          $nombreImg = htmlspecialchars($fila['imagen']);
          $rutaRelativa = "/IMG/servicios/" . $nombreImg;
          $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . $rutaRelativa;

          if (!file_exists($rutaAbsoluta)) {
              $rutaRelativa = "/IMG/servicios/LogoCarWash.png"; // Imagen por defecto
          }
        ?>
        <div class="card-lavado">
          <img src="<?php echo $rutaRelativa; ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
          <div class="contenido">
            <h4><?php echo htmlspecialchars($fila['nombre']); ?></h4>
            <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
            <p class="precio">$<?php echo number_format($fila['precio'], 2); ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>

    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
