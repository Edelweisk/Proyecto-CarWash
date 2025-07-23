<?php
require_once("../conexion.php");


session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['btn_ingresar'])) {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    if ($usuario === "" || $password === "") {
        echo "<script>alert('Por favor complete ambos campos.');</script>";
    } else {
        $stmt = $con->prepare("SELECT id, nombre, imagen, password, estado FROM usuario WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $fila = $resultado->fetch_assoc();

            if ($fila['estado'] !== 'activo') {
                echo "<script>alert('Usuario inactivo, contacte con el administrador.');</script>";
            } else {
                // Compara la contraseña en texto plano
                if ($fila['password'] === $password) {
                    $_SESSION['id_usuario'] = $fila['id'];
                    $_SESSION['nombre'] = $fila['nombre'];
                    $_SESSION['imagen'] = $fila['imagen'];
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<script>alert('Usuario o contraseña incorrectos.');</script>";
                }
            }
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos.');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Debug Car Wash</title>

  <!-- Fuente Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

  <!-- Estilo personalizado -->
  <link rel="stylesheet" href="../CSS/CSSlogin/backgronud.css" />
</head>
<body>

  <div class="login-container">
    <div class="login-card shadow-lg">

      <!-- Burbujas animadas sutiles -->
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

      <div class="text-center mb-4">
       <img src="../IMG/debuglogo2.png" alt="Logo" style="max-width: 300px; max-height: 300px;"> 
        <p class="subtitle">Iniciar sesión</p>
      </div>

      <form action="login.php" method="post" autocomplete="off" novalidate>
        <div class="form-group position-relative">
          <i class="fas fa-user icon-input"></i>
          <input
            type="text"
            class="form-control"
            placeholder="Usuario"
            name="usuario"
            required
            autofocus
            autocomplete="username"
          />
          <div class="invalid-feedback">Por favor ingrese su usuario.</div>
        </div>

        <div class="form-group position-relative">
          <i class="fas fa-lock icon-input"></i>
          <input
            type="password"
            class="form-control"
            placeholder="Contraseña"
            name="password"
            required
            autocomplete="current-password"
          />
          <div class="invalid-feedback">Por favor ingrese su contraseña.</div>
        </div>

        <button type="submit" name="btn_ingresar" class="btn btn-primary btn-block">Entrar</button>
      </form>

      <div class="text-center mt-3">
        <p> ¿Nuevo miembro? <a href="register.php" class="register-link"> ¡Regístrate aquí!</a> </p>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script>
    // Validación básica Bootstrap 4
    (function () {
      'use strict';
      window.addEventListener(
        'load',
        function () {
          const forms = document.getElementsByTagName('form');
          Array.prototype.filter.call(forms, function (form) {
            form.addEventListener(
              'submit',
              function (event) {
                if (form.checkValidity() === false) {
                  event.preventDefault();
                  event.stopPropagation();
                }
                form.classList.add('was-validated');
              },
              false
            );
          });
        },
        false
      );
    })();
  </script>
</body>
</html>
