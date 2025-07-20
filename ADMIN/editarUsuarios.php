<?php
require_once('../conexion.php');
include 'header.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    echo "<script>alert('ID de usuario no válido'); window.location = 'usuarios.php';</script>";
    exit();
}

// Obtener datos actuales del usuario
$stmt = $con->prepare("SELECT * FROM usuario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Usuario no encontrado'); window.location = 'usuarios.php';</script>";
    exit();
}

$usuarioData = $result->fetch_assoc();

if (isset($_POST['btn_editar'])) {
    // Recoger datos desde POST con validaciones mínimas
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

    // Validar rol con valores válidos
    $roles_validos = ['Secretaria', 'TecnicoLavado', 'administrador'];
    if (!in_array($rol, $roles_validos)) {
        echo "<script>alert('Rol inválido.');</script>";
        exit();
    }

    // Manejo de imagen
    $imgFinal = $usuarioData['imagen']; // por defecto la que ya tiene

    if (!empty($_FILES['nueva_imagen']['name'])) {
        $imgNombre = $_FILES['nueva_imagen']['name'];
        $imgTmp    = $_FILES['nueva_imagen']['tmp_name'];
        $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
        $imgFinal  = strtolower($usuario . '_' . time() . '.' . $ext);
        $imgDestino = "../IMG/usuarios/" . $imgFinal;

        if (move_uploaded_file($imgTmp, $imgDestino)) {
            // Borrar imagen anterior si existe y no es default.png
            if (!empty($usuarioData['imagen']) && $usuarioData['imagen'] !== 'default.png' && file_exists("../IMG/usuarios/" . $usuarioData['imagen'])) {
                unlink("../IMG/usuarios/" . $usuarioData['imagen']);
            }
        } else {
            echo "<script>alert('No se pudo subir la nueva imagen');</script>";
            exit();
        }
    }

    // NOTA: Aquí podrías hashear la contraseña si quieres seguridad real.
    // Por ejemplo: $password = password_hash($password, PASSWORD_DEFAULT);

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

<!-- Asegúrate de que el formulario tenga campo lugar_nacimiento, y que coincida con los nombres en PHP -->
<link rel="stylesheet" href="../CSS/EditarUserCSS/editar.css" />

<div class="content-wrapper">
  <section class="content-header">
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

  <section class="content">
    <div class="container-fluid d-flex justify-content-center">
      <div class="card editar-user-card">
        <div class="card-body p-4">
          <div class="text-center mb-4">
            <?php if (!empty($usuarioData['imagen']) && file_exists("../IMG/usuarios/" . $usuarioData['imagen'])): ?>
              <img src="../IMG/usuarios/<?= htmlspecialchars($usuarioData['imagen']); ?>" class="editar-user-avatar img-thumbnail rounded-circle" alt="Avatar de usuario">
            <?php else: ?>
              <img src="../IMG/usuarios/default.png" class="editar-user-avatar img-thumbnail rounded-circle" alt="Sin imagen">
            <?php endif; ?>
          </div>

          <form action="" method="POST" enctype="multipart/form-data" id="editar-user-form">
            <input type="hidden" name="id" value="<?= $usuarioData['id']; ?>">

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

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nacionalidad:</label>
                <input type="text" name="nacionalidad" class="form-control" value="<?= htmlspecialchars($usuarioData['nacionalidad']); ?>">
              </div>
              <div class="form-group col-md-6">
                <label>Estado Civil:</label>
                <select name="estado_civil" class="form-control" required>
                  <option value="Soltero" <?= $usuarioData['estado_civil'] === 'Soltero' ? 'selected' : ''; ?>>Soltero</option>
                  <option value="Casado" <?= $usuarioData['estado_civil'] === 'Casado' ? 'selected' : ''; ?>>Casado</option>
                  <option value="Divorciado" <?= $usuarioData['estado_civil'] === 'Divorciado' ? 'selected' : ''; ?>>Divorciado</option>
                  <option value="Viudo" <?= $usuarioData['estado_civil'] === 'Viudo' ? 'selected' : ''; ?>>Viudo</option>
                </select>
              </div>
            </div>

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

            <div class="form-group">
              <label>Nueva imagen (opcional):</label>
              <input type="file" name="nueva_imagen" class="form-control-file" accept="image/*">
            </div>

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
