<?php
require_once("../conexion.php");

session_start();
if (isset($_SESSION['id_usuario'])) {
  header("Location: index.php");
  exit();
}

if (isset($_REQUEST['btn_ingresar'])) {
  $usuario = $_POST['usuario'];
  $password = $_POST['password'];

  $sql = "SELECT id, nombre, imagen FROM usuario WHERE usuario = '$usuario' AND password = '$password'";
  $ejecutar = $con->query($sql);
  $row = $ejecutar->num_rows;
  if ($row > 0) {
    $row = $ejecutar->fetch_assoc();
    $_SESSION['id_usuario'] = $row['id'];
    $_SESSION['nombre'] = $row['nombre'];
    $_SESSION['imagen'] = $row['imagen'];
    header("Location: index.php");
    exit();
  } else {
    echo "<script>alert('El Usuario o la contraseña son incorrectos');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Proyecto</title>

  <!-- Fuente Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Bootstrap -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- Estilo moderno personalizado -->
  <link rel="stylesheet" href="../CSS/CSSlogin/Imagenes/backgronud.css">
</head>
<body>
  <div class="login-container">
  <div class="login-card">
    
    <!-- Burbujas decorativas animadas -->
<div class="bubbles">
  <span style="--size:25px; --left:10%; --duration:10s; --delay:0s;"></span>
  <span style="--size:20px; --left:25%; --duration:12s; --delay:1s;"></span>
  <span style="--size:30px; --left:40%; --duration:14s; --delay:2s;"></span>
  <span style="--size:18px; --left:55%; --duration:11s; --delay:3s;"></span>
  <span style="--size:22px; --left:70%; --duration:13s; --delay:0.5s;"></span>
  <span style="--size:15px; --left:85%; --duration:15s; --delay:1.5s;"></span>
  <span style="--size:28px; --left:20%; --duration:10s; --delay:2.5s;"></span>
  <span style="--size:32px; --left:35%; --duration:14s; --delay:0.8s;"></span>
  <span style="--size:17px; --left:60%; --duration:12s; --delay:1.2s;"></span>
  <span style="--size:23px; --left:75%; --duration:13s; --delay:2.2s;"></span>
  <span style="--size:26px; --left:15%; --duration:11s; --delay:3s;"></span>
  <span style="--size:21px; --left:45%; --duration:9s; --delay:2s;"></span>
  <span style="--size:19px; --left:65%; --duration:10s; --delay:1s;"></span>
  <span style="--size:30px; --left:80%; --duration:12s; --delay:0.3s;"></span>
  <span style="--size:24px; --left:5%; --duration:13s; --delay:1.7s;"></span>
  <span style="--size:22px; --left:90%; --duration:11s; --delay:2.5s;"></span>
  <span style="--size:16px; --left:38%; --duration:14s; --delay:0.6s;"></span>
  <span style="--size:20px; --left:52%; --duration:13s; --delay:0.9s;"></span>
  <span style="--size:28px; --left:68%; --duration:12s; --delay:2.4s;"></span>
  <span style="--size:18px; --left:82%; --duration:10s; --delay:1.1s;"></span>
    </div>

    <!-- Contenido del login -->
    <div class="text-center mb-4">
      <h2><b>Logo</b>Empresa</h2>
      <p>INICIAR SESIÓN</p>
    </div>

      <form action="login.php" method="post">
        <div class="form-group position-relative">
          <i class="fas fa-user"></i>
          <input type="text" class="form-control" placeholder="Ingrese su Usuario" name="usuario" required>
        </div>
        <div class="form-group position-relative">
          <i class="fas fa-lock"></i>
          <input type="password" class="form-control" placeholder="Ingrese su Contraseña" name="password" required>
        </div>
        <button type="submit" name="btn_ingresar" class="btn btn-primary btn-block">Entrar</button>
      </form>

      <div class="text-center mt-3">
        <a href="register.php" class="register-link">Registrar nuevos Miembros</a>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
