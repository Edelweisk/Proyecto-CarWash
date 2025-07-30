<?php
// =======================
// Conexiones y encabezado
// =======================

require_once('../conexion.php'); // Conecta con la base de datos
include 'header.php';           // Incluye la cabecera del panel de administración
include '../control.php';       // Verifica sesión y permisos del usuario

// =============================
// Validación del ID de usuario
// =============================

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0; // Obtiene el ID desde la URL y lo valida
if ($id === 0) {
    // Si el ID no es válido, redirige a la lista de usuarios con un mensaje
    echo "<script>alert('ID de usuario no válido'); window.location = 'usuarios.php';</script>";
    exit();
}

// ===========================
// Consulta de datos del usuario
// ===========================

$stmt = $con->prepare("SELECT * FROM usuario WHERE id = ?"); // Prepara la consulta SQL
$stmt->bind_param("i", $id); // Asocia el parámetro ID
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Si no se encuentra el usuario, muestra una alerta y redirige
    echo "<script>alert('Usuario no encontrado'); window.location = 'usuarios.php';</script>";
    exit();
}

$usuarioData = $result->fetch_assoc(); // Almacena los datos del usuario en un arreglo asociativo

// ===============================
// Procesamiento del formulario POST
// ===============================

if (isset($_POST['btn_editar'])) {
    // Se ejecuta al enviar el formulario para editar el usuario

    // ==============================
    // Recolección de datos del formulario
    // ==============================

    $nombre           = trim($_POST['nombre']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $lugar_nacimiento = isset($_POST['lugar_nacimiento']) ? trim($_POST['lugar_nacimiento']) : $usuarioData['lugar_nacimiento'];
    $numero_identificacion = trim($_POST['numero_identificacion']);
    $nacionalidad     = trim($_POST['nacionalidad']);
    $estado_civil     = $_POST['estado_civil'];
    $direccion        = trim($_POST['direccion']);
    $telefono         = trim($_POST['telefono']);
    $numero_emergencia= trim($_POST['numero_emergencia']);
    $email            = trim($_POST['email']);
    $usuario          = trim($_POST['usuario']);
    $password         = $_POST['password'];
    $rol              = $_POST['rol'];
    $estado           = $_POST['estado'];

    // ======================
    // Validación del rol
    // ======================

    $roles_validos = ['Secretaria', 'TecnicoLavado', 'administrador'];
    if (!in_array($rol, $roles_validos)) {
        echo "<script>alert('Rol inválido.');</script>";
        exit();
    }

    // ======================
    // Manejo de imagen
    // ======================

    $imgFinal = $usuarioData['imagen']; // Se mantiene la imagen actual como predeterminada

    // Si se sube una nueva imagen
    if (!empty($_FILES['nueva_imagen']['name'])) {
        $imgNombre = $_FILES['nueva_imagen']['name'];
        $imgTmp    = $_FILES['nueva_imagen']['tmp_name'];
        $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
        $imgFinal  = strtolower($usuario . '_' . time() . '.' . $ext); // Nuevo nombre único
        $imgDestino = "../IMG/usuarios/" . $imgFinal;

        // Guardar imagen en el servidor
        if (move_uploaded_file($imgTmp, $imgDestino)) {
            // Borrar la imagen anterior si no es la predeterminada
            if (!empty($usuarioData['imagen']) && $usuarioData['imagen'] !== 'default.png' && file_exists("../IMG/usuarios/" . $usuarioData['imagen'])) {
                unlink("../IMG/usuarios/" . $usuarioData['imagen']);
            }
        } else {
            echo "<script>alert('No se pudo subir la nueva imagen');</script>";
            exit();
        }
    }

    // ===========================================
    // Actualización de los datos del usuario
    // ===========================================

    $stmt = $con->prepare("UPDATE usuario SET nombre=?, fecha_nacimiento=?, lugar_nacimiento=?, numero_identificacion=?, nacionalidad=?, estado_civil=?, direccion=?, telefono=?, numero_emergencia=?, email=?, usuario=?, password=?, rol=?, estado=?, imagen=? WHERE id=?");

    $stmt->bind_param(
        "sssssssssssssssi",
        $nombre,
        $fecha_nacimiento,
        $lugar_nacimiento,
        $numero_identificacion,
        $nacionalidad,
        $estado_civil,
        $direccion,
        $telefono,
        $numero_emergencia,
        $email,
        $usuario,
        $password,
        $rol,
        $estado,
        $imgFinal,
        $id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Usuario actualizado correctamente'); window.location = 'usuarios.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al actualizar usuario: " . $stmt->error . "');</script>";
    }
}
?>

<!-- ===================== -->
<!-- Formulario HTML Editar -->
<!-- ===================== -->

<!-- Enlace al CSS personalizado -->
<link rel="stylesheet" href="../CSS/EditarUserCSS/editar.css" />

<div class="content-wrapper">
  <section class="content-header">
    <!-- Título y breadcrumb -->
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Editar Usuario</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="usuarios.php">Usuarios</a></li>
            <li class="breadcrumb-item active">Editar</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Formulario de edición -->
  <section class="content">
    <div class="container-fluid d-flex justify-content-center">
      <div class="card editar-user-card">
        <div class="card-body p-4">

          <!-- Avatar del usuario -->
          <div class="text-center mb-4">
            <?php if (!empty($usuarioData['imagen']) && file_exists("../IMG/usuarios/" . $usuarioData['imagen'])): ?>
              <img src="../IMG/usuarios/<?= htmlspecialchars($usuarioData['imagen']); ?>" class="editar-user-avatar img-thumbnail rounded-circle" alt="Avatar de usuario">
            <?php else: ?>
              <img src="../IMG/usuarios/default.png" class="editar-user-avatar img-thumbnail rounded-circle" alt="Sin imagen">
            <?php endif; ?>
          </div>

          <!-- Formulario de actualización -->
          <form action="" method="POST" enctype="multipart/form-data" id="editar-user-form">

            <!-- Campos personales -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nombre completo:</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuarioData['nombre']); ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label>Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($usuarioData['fecha_nacimiento']); ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Lugar de nacimiento:</label>
                <input type="text" name="lugar_nacimiento" class="form-control" value="<?= htmlspecialchars($usuarioData['lugar_nacimiento']); ?>">
              </div>
              <div class="form-group col-md-6">
                <label>Identificación:</label>
                <input type="text" name="numero_identificacion" class="form-control" value="<?= htmlspecialchars($usuarioData['numero_identificacion']); ?>" required>
              </div>
            </div>

            <!-- Contacto -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Dirección:</label>
                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($usuarioData['direccion']); ?>">
              </div>
              <div class="form-group col-md-6">
                <label>Teléfono:</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuarioData['telefono']); ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Contacto de Emergencia:</label>
                <input type="text" name="numero_emergencia" class="form-control" value="<?= htmlspecialchars($usuarioData['numero_emergencia']); ?>">
              </div>
              <div class="form-group col-md-6">
                <label>Correo electrónico:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuarioData['email']); ?>" required>
              </div>
            </div>

            <!-- Credenciales -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Usuario:</label>
                <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($usuarioData['usuario']); ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label>Contraseña:</label>
                <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($usuarioData['password']); ?>" required>
              </div>
            </div>

            <!-- Rol y estado -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Rol:</label>
                <select name="rol" class="form-control" required>
                  <option value="Secretaria" <?= $usuarioData['rol'] === 'Secretaria' ? 'selected' : ''; ?>>Secretaria</option>
                  <option value="TecnicoLavado" <?= $usuarioData['rol'] === 'TecnicoLavado' ? 'selected' : ''; ?>>Técnico de Lavado</option>
                  <option value="administrador" <?= $usuarioData['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label>Estado:</label>
                <select name="estado" class="form-control" required>
                  <option value="activo" <?= $usuarioData['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                  <option value="inactivo" <?= $usuarioData['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
              </div>
            </div>

            <!-- Imagen nueva -->
            <div class="form-group">
              <label>Nueva imagen (opcional):</label>
              <input type="file" name="nueva_imagen" class="form-control-file" accept="image/*">
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-between mt-4">
              <button type="submit" name="btn_editar" class="btn btn-success">
                <i class="fas fa-save"></i> Actualizar
              </button>
              <button type="submit" name="btn_eliminar" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                <i class="fas fa-trash-alt"></i> Eliminar
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
