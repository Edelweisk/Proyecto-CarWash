<?php
require_once('../conexion.php');
include 'header.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    echo "<script>alert('ID de usuario no v\u00e1lido'); window.location = 'usuarios.php';</script>";
    exit();
}

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
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $usuario  = $_POST['usuario'];
    $password = $_POST['password'];

    $imgFinal = $usuarioData['imagen'];

    if (!empty($_FILES['nueva_imagen']['name'])) {
        $imgNombre = $_FILES['nueva_imagen']['name'];
        $imgTmp    = $_FILES['nueva_imagen']['tmp_name'];
        $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
        $imgFinal  = strtolower($usuario . '_' . time() . '.' . $ext);
        $imgDestino = "../IMG/usuarios/" . $imgFinal;

        if (move_uploaded_file($imgTmp, $imgDestino)) {
            if (!empty($usuarioData['imagen']) && file_exists("../IMG/usuarios/" . $usuarioData['imagen'])) {
                unlink("../IMG/usuarios/" . $usuarioData['imagen']);
            }
        } else {
            echo "<script>alert('No se pudo subir la nueva imagen');</script>";
            exit();
        }
    }

    $stmt = $con->prepare("UPDATE usuario SET nombre=?, email=?, usuario=?, password=?, imagen=? WHERE id=?");
    $stmt->bind_param("sssssi", $nombre, $email, $usuario, $password, $imgFinal, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario actualizado correctamente'); window.location = 'usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar usuario');</script>";
    }
}

if (isset($_POST['btn_eliminar'])) {
    if (!empty($usuarioData['imagen']) && file_exists("../IMG/usuarios/" . $usuarioData['imagen'])) {
        unlink("../IMG/usuarios/" . $usuarioData['imagen']);
    }

    $stmt = $con->prepare("DELETE FROM usuario WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario eliminado correctamente'); window.location = 'usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar usuario');</script>";
    }
}
?>

<!-- Agrega esta línea después del include del header -->
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
              <img src="../IMG/usuarios/<?= $usuarioData['imagen']; ?>" class="editar-user-avatar img-thumbnail rounded-circle" alt="Avatar de usuario">
            <?php else: ?>
              <img src="../IMG/usuarios/default.png" class="editar-user-avatar img-thumbnail rounded-circle" alt="Sin imagen">
            <?php endif; ?>
          </div>

          <form action="" method="POST" enctype="multipart/form-data" id="editar-user-form">
            <input type="hidden" name="id_usuario_editar" value="<?= $usuarioData['id']; ?>">

            <div class="form-group">
              <label for="nombre_usuario_editar">Nombre completo:</label>
              <input type="text" id="nombre_usuario_editar" name="nombre" class="form-control" value="<?= htmlspecialchars($usuarioData['nombre']); ?>" required>
            </div>

            <div class="form-group">
              <label for="email_usuario_editar">Correo electrónico:</label>
              <input type="email" id="email_usuario_editar" name="email" class="form-control" value="<?= htmlspecialchars($usuarioData['email']); ?>" required>
            </div>

            <div class="form-group">
              <label for="username_usuario_editar">Usuario:</label>
              <input type="text" id="username_usuario_editar" name="usuario" class="form-control" value="<?= htmlspecialchars($usuarioData['usuario']); ?>" required>
            </div>

            <div class="form-group">
              <label for="password_usuario_editar">Contraseña:</label>
              <input type="text" id="password_usuario_editar" name="password" class="form-control" value="<?= htmlspecialchars($usuarioData['password']); ?>" required>
            </div>

            <div class="form-group">
              <label for="imagen_nueva_usuario_editar">Nueva imagen (opcional):</label>
              <input type="file" id="imagen_nueva_usuario_editar" name="nueva_imagen" class="form-control-file" accept="image/*">
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
