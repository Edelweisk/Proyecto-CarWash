<?php
require_once('../conexion.php');
session_start();

// Validar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id_usuario'];

// Sentencia preparada para evitar inyección SQL
$stmt = $con->prepare("SELECT nombre, email, usuario, imagen FROM usuario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

$nombre = "Usuario";
$email = "";
$usuario = "";
$imagen = "default.png";

if ($fila = $resultado->fetch_assoc()) {
    $nombre  = $fila['nombre'];
    $email   = $fila['email'];
    $usuario = $fila['usuario'];

    if (!empty($fila['imagen']) && file_exists("../IMG/usuarios/" . $fila['imagen'])) {
        $imagen = $fila['imagen'];
    }
}

$stmt->close();

// Variables para menú activo
$pagina = basename($_SERVER['PHP_SELF']);
$activo_dashboard = ($pagina === 'index.php') ? 'active' : '';
$activo_usuarios = ($pagina === 'usuarios.php') ? 'active' : '';
$activo_crearUsuarios = ($pagina === 'crearUsuarios.php') ? 'active' : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Debug Car Wash | Dashboard</title>

  <!-- Fuentes y estilos -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css" />
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css" />
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css" />
  <link rel="stylesheet" href="dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="../CSS/headerCSS/Header.css" />
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css" />
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css" />
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css" />
  <link rel="stylesheet" href="../CSS/custom-admin.css" />
  <style>
    /* Quitar flecha desplegable del título Gestión de Empleados */
    .nav-item.menu-open > p.nav-link > i.right.fas.fa-angle-left {
      display: none !important;
    }

    /* Cursor normal y sin interacción */
    .nav-item.menu-open > p.nav-link {
      cursor: default !important;
      pointer-events: none;
      user-select: none;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../CSS/CSSlogin/Imagenes/LogoCarWash.PNG" alt="Debug Car Wash Logo" height="60" width="60" />
  </div>

  <!-- Navbar principal -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="Colapsar menú lateral">
          <i class="fas fa-bars"></i>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button" title="Buscar">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline" action="#" method="GET">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" name="q" placeholder="Buscar" aria-label="Buscar" />
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit" title="Buscar">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search" aria-label="Cerrar búsqueda" title="Cerrar búsqueda">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Pantalla completa">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

      <li class="nav-item">
        <a href="salir.php" class="nav-link" title="Cerrar sesión">
          <i class="fas fa-sign-out-alt"></i> Salir
        </a>
      </li>
    </ul>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link d-flex align-items-center">
      <img src="../CSS/CSSlogin/Imagenes/LogoCarWash.png" alt="Debug Car Wash Logo" class="brand-image img-circle elevation-3" style="opacity: 0.8; width: 35px; height: 35px" />
      <span class="brand-text font-weight-light ml-2">Debug Car Wash</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image me-2">
          <img src="../IMG/usuarios/<?php echo htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8'); ?>" class="img-circle elevation-2" alt="Imagen de usuario" style="width: 40px; height: 50px" />
        </div>
        <div class="info">
          <a href="#" class="d-block" style="margin-left: 0;"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
      </div>

      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Buscar" />
          <div class="input-group-append">
            <button class="btn btn-sidebar" title="Buscar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="index.php" class="nav-link <?php echo $activo_dashboard; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item menu-open">
            <p class="nav-link <?php echo ($activo_usuarios || $activo_crearUsuarios) ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-users-cog"></i>
              Gestión de Empleados
            </p>
            <ul class="nav nav-treeview" style="display: block;">
              <li class="nav-item">
                <a href="usuarios.php" class="nav-link <?php echo $activo_usuarios; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lista de Empleados</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="Registrar_Empleado.php" class="nav-link <?php echo $activo_crearUsuarios; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Agregar Nuevo Empleado</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="serviciosofrecidos.php" class="nav-link">
              <i class="nav-icon fas fa-circle"></i>
              <p>Servicios disponibles</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="salir.php" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Cerrar Sesión</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    // Inicializa pushmenu para el sidebar
    $('[data-widget="pushmenu"]').PushMenu();
  });
</script>
</body>
</html>
