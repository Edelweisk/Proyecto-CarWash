<?php 
// Incluir conexión a base de datos
include_once "../conexion.php";

// Incluir header de la plantilla
include 'header.php'; 

// Control de sesión y permisos (seguridad)
include '../control.php';

// Si se envió el formulario para registrar nuevo empleado
if (isset($_REQUEST['btn_registrar'])) {
    // Recoger y sanitizar datos del formulario
    $nombre            = trim($_POST['nombre']);
    $fecha_nacimiento  = $_POST['fecha_nacimiento'];
    $lugar_nacimiento  = trim($_POST['lugar_nacimiento']);
    $numero_identificacion = trim($_POST['numero_identificacion']);
    $nacionalidad      = trim($_POST['nacionalidad']);
    $estado_civil      = $_POST['estado_civil'];
    $direccion         = trim($_POST['direccion']);
    $telefono          = trim($_POST['telefono']);
    $numero_emergencia = trim($_POST['numero_emergencia']);
    $email             = trim($_POST['email']);
    $usuario           = trim($_POST['usuario']);
    $password          = $_POST['password'];
    $rol               = $_POST['rol'];

    // Validar que no exista ya un usuario con ese nombre (consulta segura con prepared statement)
    $stmt = $con->prepare("SELECT id FROM usuario WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    // Si existe el usuario, mostrar alerta y evitar duplicados
    if ($stmt->num_rows > 0) { 
        echo "<script>alert('El usuario ya existe, por favor ingrese otro');</script>"; 
    } else {
        // Procesar imagen de perfil, valor por defecto si no se carga ninguna
        $imgFinal = 'default.png'; 
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imgNombre = $_FILES['imagen']['name'];
            $imgTmp    = $_FILES['imagen']['tmp_name'];
            $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
            // Generar nombre único para evitar colisiones
            $imgFinal  = strtolower($usuario . "_" . time() . "." . $ext);
            $imgDestino = "../IMG/usuarios/" . $imgFinal;

            // Mover imagen al directorio final, si falla, usar imagen por defecto
            if (!move_uploaded_file($imgTmp, $imgDestino)) {
                $imgFinal = 'default.png';
            }
        }

        // Insertar el nuevo usuario en la base de datos de forma segura (prepared statement)
        $insert = $con->prepare("INSERT INTO usuario (nombre, fecha_nacimiento, lugar_nacimiento, numero_identificacion, nacionalidad, estado_civil, direccion, telefono, numero_emergencia, email, usuario, password, rol, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssssssssssss", $nombre, $fecha_nacimiento, $lugar_nacimiento, $numero_identificacion, $nacionalidad, $estado_civil, $direccion, $telefono, $numero_emergencia, $email, $usuario, $password, $rol, $imgFinal);

        // Ejecutar inserción y notificar éxito o error
        if ($insert->execute()) {
            echo "<script>alert('Empleado registrado correctamente');</script>";
        } else {
            echo "<script>alert('Error al registrar el empleado: " . $con->error . "');</script>";
        }
    }
    // Cerrar statement y conexión para liberar recursos
    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <!-- Metadatos básicos para correcta visualización -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <title>Registrar Empleado - Debug Car Wash</title>

  <!-- Bootstrap para estilos responsivos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Librería AOS para animaciones de scroll -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

  <!-- CSS personalizado para el formulario -->
  <link rel="stylesheet" href="../CSS/RegisterCss/Register.Css" />
</head>
<body>

<div class="content-wrapper">

  <!-- Título principal con animación -->
  <section class="text-center mt-4" data-aos="fade-down">
    <h1 class="text-black">Registrar nuevo empleado</h1>
  </section>

  <!-- Contenedor del formulario -->
  <section class="content">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-xl-10 col-xxl-9">
          <div class="card shadow" data-aos="zoom-in">
            <div class="card-body p-5">

              <!-- Formulario para registrar empleado, con validación HTML5 y subida de archivos -->
              <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

                <!-- Campos: nombre y fecha de nacimiento con animación -->
                <div class="row mb-3" data-aos="fade-up">
                  <div class="col-md-6">
                    <label class="form-label">Nombres y Apellidos:</label>
                    <input type="text" class="form-control" name="nombre" required placeholder="Ej: Ana Pérez" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Fecha de nacimiento:</label>
                    <input type="date" class="form-control" name="fecha_nacimiento" required />
                  </div>
                </div>

                <!-- Lugar de nacimiento e identificación -->
                <div class="row mb-3" data-aos="fade-up" data-aos-delay="100">
                  <div class="col-md-6">
                    <label class="form-label">Lugar de nacimiento:</label>
                    <input type="text" class="form-control" name="lugar_nacimiento" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Número de identificación (ID):</label>
                    <input type="text" class="form-control" name="numero_identificacion" required />
                  </div>
                </div>

                <!-- Nacionalidad y estado civil -->
                <div class="row mb-3" data-aos="fade-up" data-aos-delay="200">
                  <div class="col-md-6">
                    <label class="form-label">Nacionalidad:</label>
                    <input type="text" class="form-control" name="nacionalidad" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Estado civil:</label>
                    <select class="form-select" name="estado_civil" required>
                      <option value="" disabled selected>Seleccione estado civil</option>
                      <option value="Soltero">Soltero(a)</option>
                      <option value="Casado">Casado(a)</option>
                      <option value="Divorciado">Divorciado(a)</option>
                      <option value="Viudo">Viudo(a)</option>
                    </select>
                  </div>
                </div>

                <!-- Dirección -->
                <div class="mb-3" data-aos="fade-up" data-aos-delay="300">
                  <label class="form-label">Dirección:</label>
                  <input type="text" class="form-control" name="direccion" required />
                </div>

                <!-- Teléfonos -->
                <div class="row mb-3" data-aos="fade-up" data-aos-delay="400">
                  <div class="col-md-6">
                    <label class="form-label">Número de teléfono:</label>
                    <input type="tel" class="form-control" name="telefono" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Número de emergencia:</label>
                    <input type="tel" class="form-control" name="numero_emergencia" required />
                  </div>
                </div>

                <!-- Correo electrónico -->
                <div class="mb-3" data-aos="fade-up" data-aos-delay="500">
                  <label class="form-label">Correo electrónico:</label>
                  <input type="email" class="form-control" name="email" required />
                </div>

                <!-- Usuario y contraseña -->
                <div class="row mb-3" data-aos="fade-up" data-aos-delay="600">
                  <div class="col-md-6">
                    <label class="form-label">Nombre de usuario:</label>
                    <input type="text" class="form-control" name="usuario" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" name="password" required />
                  </div>
                </div>

                <!-- Selección de rol -->
                <div class="mb-3" data-aos="fade-up" data-aos-delay="700">
                  <label class="form-label">Rol del empleado:</label>
                  <select class="form-select" name="rol" required>
                    <option value="" disabled selected>Seleccione un rol</option>
                    <option value="Secretaria">Secretaria</option>
                    <option value="TecnicoLavado">Técnico de Lavado</option>
                  </select>
                </div>

                <!-- Subida de imagen -->
                <div class="mb-4" data-aos="fade-up" data-aos-delay="800">
                  <label class="form-label">Imagen de perfil:</label>
                  <input type="file" class="form-control" name="imagen" accept="image/*" />
                </div>

                <!-- Botón enviar -->
                <button type="submit" class="btn btn-primary w-100" name="btn_registrar" data-aos="fade-up" data-aos-delay="900">
                  Registrar Empleado
                </button>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Incluir footer -->
<?php include 'footer.php'; ?>

<!-- Librería AOS para animaciones -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,        // Duración animación en ms
    easing: 'ease-in-out',// Tipo de easing suave
    once: true           // Animar sólo una vez al entrar en viewport
  });
</script>

</body>
</html>
