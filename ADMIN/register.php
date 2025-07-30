<?php 
include_once "../conexion.php"; // Incluye la conexión a la base de datos

// Si el formulario fue enviado con el botón 'btn_registrar'
if (isset($_REQUEST['btn_registrar'])) {

    // ✅ Recolectar y limpiar los datos del formulario
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

    // 🔐 Validar si el usuario ya existe usando sentencia preparada
    $stmt = $con->prepare("CALL validar(?)");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->store_result();

    // Si el usuario ya existe, se muestra un mensaje de error
    if ($stmt->num_rows > 0) { 
        echo "<script>alert('El usuario ya existe, por favor ingrese otro');</script>"; 
    } else {
        // 🖼️ Procesamiento de la imagen de perfil
        $imgFinal = 'default.png'; // Imagen por defecto
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imgNombre = $_FILES['imagen']['name'];            // Nombre original
            $imgTmp    = $_FILES['imagen']['tmp_name'];        // Ruta temporal
            $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION); // Obtener extensión
            $imgFinal  = strtolower($usuario . "_" . time() . "." . $ext); // Nombre final único
            $imgDestino = "../IMG/usuarios/" . $imgFinal;      // Ruta destino

            // Mover imagen a carpeta final
            if (!move_uploaded_file($imgTmp, $imgDestino)) {
                $imgFinal = 'default.png'; // Si falla, se usa default
            }
        }

        // 📥 Insertar todos los datos del empleado a la base de datos
        $insert = $con->prepare("INSERT INTO usuario 
        (nombre, fecha_nacimiento, lugar_nacimiento, numero_identificacion, nacionalidad, estado_civil, direccion, telefono, numero_emergencia, email, usuario, password, rol, imagen) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $insert->bind_param("ssssssssssssss", 
            $nombre, $fecha_nacimiento, $lugar_nacimiento, 
            $numero_identificacion, $nacionalidad, $estado_civil, 
            $direccion, $telefono, $numero_emergencia, $email, 
            $usuario, $password, $rol, $imgFinal);

        // Confirmar si el registro fue exitoso
        if ($insert->execute()) {
            echo "<script>alert('Empleado registrado correctamente');</script>";
        } else {
            echo "<script>alert('Error al registrar el empleado: " . $con->error . "');</script>";
        }
    }

    // Cierre de recursos
    $stmt->close();
    $con->close();
}

?>

<!-- Metadatos básicos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrar Empleado - Debug Car Wash</title>

    <!-- Bootstrap y estilos personalizados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../CSS/CSSlogin/registro.css" />
</head>
<body>

<!-- Animación de carga -->
<?php include_once "../CSS/Preloader/Preoloader.php"; ?>

<!-- Encabezado personalizado -->
<?php include_once "headerregister.php"; ?>

<!-- Contenedor principal del contenido -->
<div class="content-wrapper">

  <!-- Título -->
  <section class="text-center mt-4">
    <h1 class="text-black">Registrar nuevo empleado</h1>
  </section>

  <!-- Contenido del formulario -->
  <section class="content">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-xl-10 col-xxl-9">
          <div class="card shadow">
            <div class="card-body p-5">

              <!-- FORMULARIO DE REGISTRO -->
              <form action="register.php" method="POST" enctype="multipart/form-data" class="formulario-amplio">

                <!-- Campo: Nombre y Fecha de nacimiento -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Nombres y Apellidos:</label>
                    <input type="text" class="form-control" name="nombre" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Fecha de nacimiento:</label>
                    <input type="date" class="form-control" name="fecha_nacimiento" required />
                  </div>
                </div>

                <!-- Lugar de nacimiento y número de ID -->
                <div class="row mb-3">
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
                <div class="row mb-3">
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

                <!-- Dirección completa -->
                <div class="mb-3">
                  <label class="form-label">Dirección:</label>
                  <input type="text" class="form-control" name="direccion" required />
                </div>

                <!-- Teléfono y emergencia -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Número de teléfono:</label>
                    <input type="tel" class="form-control" name="telefono" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Número de emergencia:</label>
                    <input type="tel" class="form-control" name="numero_emergencia" required />
                  </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                  <label class="form-label">Correo electrónico:</label>
                  <input type="email" class="form-control" name="email" required />
                </div>

                <!-- Usuario y contraseña -->
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">Nombre de usuario:</label>
                    <input type="text" class="form-control" name="usuario" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" name="password" required />
                  </div>
                </div>

                <!-- Rol dentro del sistema -->
                <div class="mb-3">
                  <label class="form-label">Rol del empleado:</label>
                  <select class="form-select" name="rol" required>
                    <option value="" disabled selected>Seleccione un rol</option>
                    <option value="Secretaria">Secretaria</option>
                    <option value="TecnicoLavado">Técnico de Lavado</option>
                    <option value="administrador">Administrador</option>
                  </select>
                </div>

                <!-- Subida de imagen de perfil -->
                <div class="mb-3">
                  <label class="form-label">Imagen de perfil:</label>
                  <input type="file" class="form-control" name="imagen" accept="image/*" />
                </div>

                <!-- Botón de envío -->
                <button type="submit" class="btn btn-success w-100" name="btn_registrar">Registrar Empleado</button>

              </form>
              <!-- Fin del formulario -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Footer personalizado -->
<?php include 'footerlogin.php'; ?>
</body>
</html>
