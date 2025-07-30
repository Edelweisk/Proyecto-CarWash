<?php
// =============================
// CONFIGURACIÓN INICIAL Y SESIÓN
// =============================

// Conexión a la base de datos
require_once('../conexion.php');

// Control de sesión (verifica login, roles, etc.)
include '../control.php';

// Establece la zona horaria de Panamá para funciones de fecha y hora
date_default_timezone_set('America/Panama');

// =============================
// VALIDACIÓN DE SESIÓN ACTIVA
// =============================
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php"); // Redirige al login si no hay sesión
    exit();
}

// =============================
// OBTENER DATOS DEL USUARIO ACTUAL
// =============================
$id = $_SESSION['id_usuario']; // ID del usuario logueado

// Consulta segura usando prepared statement
$stmt = $con->prepare("CALL header(?)");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

// Valores por defecto por si no se encuentra información
$nombre = "Usuario";
$email = "";
$usuario = "";
$imagen = "default.png";

// Si se encontró el usuario, extrae sus datos
if ($fila = $resultado->fetch_assoc()) {
    $nombre  = $fila['nombre'];
    $email   = $fila['email'];
    $usuario = $fila['usuario'];

    // Verifica si la imagen existe físicamente y no está vacía
    if (!empty($fila['imagen']) && file_exists("../IMG/usuarios/" . $fila['imagen'])) {
        $imagen = $fila['imagen'];
    }
}
$stmt->close();

// =============================
// CLASE ACTIVA EN EL MENÚ LATERAL SEGÚN LA PÁGINA
// =============================
$pagina = basename($_SERVER['PHP_SELF']); // Página actual
$activo_dashboard = ($pagina === 'index.php') ? 'active' : '';
$activo_usuarios = ($pagina === 'usuarios.php') ? 'active' : '';
$activo_crearUsuarios = ($pagina === 'crearUsuarios.php') ? 'active' : '';
?>

<!-- A partir de aquí comienza la salida HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Debug Car Wash | Dashboard</title>

  <!-- ============================= -->
  <!-- HOJAS DE ESTILO Y PLUGINS -->
  <!-- ============================= -->
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
    /* Personalización: Oculta el ícono desplegable del menú Gestión de Empleados */
    .nav-item.menu-open > p.nav-link > i.right.fas.fa-angle-left {
      display: none !important;
    }

    /* Desactiva clics e interacciones en el título del grupo */
    .nav-item.menu-open > p.nav-link {
      cursor: default !important;
      pointer-events: none;
      user-select: none;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- ============================= -->
  <!-- PRELOADER DE CARGA INICIAL -->
  <!-- ============================= -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../CSS/CSSlogin/Imagenes/LogoCarWash2.PNG" alt="Debug Car Wash Logo" height="60" width="60" />
  </div>

  <!-- ============================= -->
  <!-- BARRA DE NAVEGACIÓN SUPERIOR -->
  <!-- ============================= -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <!-- Botón para colapsar/expandir el sidebar -->
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="Colapsar menú lateral">
          <i class="fas fa-bars"></i>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <!-- Botón para activar pantalla completa -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Pantalla completa">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

      <!-- Botón para cerrar sesión -->
      <li class="nav-item">
        <a href="salir.php" class="nav-link" title="Cerrar sesión">
          <i class="fas fa-sign-out-alt"></i> Salir
        </a>
      </li>
    </ul>
  </nav>

  <!-- ============================= -->
  <!-- MENÚ LATERAL / SIDEBAR -->
  <!-- ============================= -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Logo de marca y nombre del sistema -->
    <a href="index.php" class="brand-link d-flex align-items-center">
      <img src="../CSS/CSSlogin/Imagenes/LogoCarWash.png" alt="Debug Car Wash Logo" class="brand-image img-circle elevation-3" style="opacity: 0.8; width: 35px; height: 35px" />
      <span class="brand-text font-weight-light ml-2">Debug Car Wash</span>
    </a>

    <div class="sidebar">
      <!-- Perfil del usuario (foto y nombre) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image me-2">
          <img src="../IMG/usuarios/<?php echo htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8'); ?>" class="img-circle elevation-2" alt="Imagen de usuario" style="width: 40px; height: 50px" />
        </div>
        <div class="info">
          <a href="#" class="d-block" style="margin-left: 0;"><?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
      </div>

      <!-- Buscador dentro del menú lateral -->
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

      <!-- ============================= -->
      <!-- ENLACES DEL MENÚ LATERAL -->
      <!-- ============================= -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false">

          <!-- Dashboard -->
          <li class="nav-item">
            <a href="index.php" class="nav-link <?php echo $activo_dashboard; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <!-- Gestión de empleados (solo para administrador) -->
<?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?>
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
<?php endif; ?>

          <!-- Servicios ofrecidos -->
          <li class="nav-item">
            <a href="serviciosofrecidos.php" class="nav-link">
              <i class="nav-icon fas fa-circle"></i>
              <p>Servicios disponibles</p>
            </a>
          </li>

          <!-- Página económica (solo admins) -->
<?php if (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador'): ?>
          <li class="nav-item">
            <a href="asignarsueldo.php" class="nav-link">
              <i class="nav-icon fas fa-circle"></i>
              <p>Economico</p>
            </a>
          </li>
<?php endif; ?>

          <!-- Cerrar sesión -->
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

<!-- ============================= -->
<!-- SCRIPTS JS NECESARIOS -->
<!-- ============================= -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    // Inicializa el botón de menú lateral
    $('[data-widget="pushmenu"]').PushMenu();
  });
</script>
</body>
</html>
