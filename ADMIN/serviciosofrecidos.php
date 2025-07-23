<?php
include 'header.php';
include '../conexion.php';

$sql = "SELECT nombre, descripcion, precio, imagen FROM tipo_lavado";
$resultado = mysqli_query($con, $sql);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($con));
}
?>

<style>

.lavados-container {
  padding: 2rem;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.card-lavado {
  background: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.card-lavado:hover {
  transform: translateY(-5px);
}

.card-lavado img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-bottom: 1px solid #ccc;
}

.card-lavado .contenido {
  padding: 1rem;
}

.card-lavado h4 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: #007bff;
}

.card-lavado p {
  margin: 0.3rem 0;
  font-size: 0.95rem;
  color: #444;
}

.card-lavado .precio {
  font-weight: bold;
  color: #28a745;
  font-size: 1.1rem;
}
</style>

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
