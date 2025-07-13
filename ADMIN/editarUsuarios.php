<?php
require_once('../conexion.php');
include 'header.php';

@$id = $_REQUEST['id'];
$sql = "SELECT * FROM usuario WHERE id = '$id'";
$ejecutar = $con->query($sql);
while($rows = mysqli_fetch_array($ejecutar)){
  $id = $rows[0];
  $nombre = $rows[1];
  $email = $rows[2];
  $usuario = $rows[3];
  $password = $rows[4];
  $imagenActual = $rows[6]; // imagen
}

if (isset($_REQUEST['btn_editar'])) {
    $id       = $_POST['id'];
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $usuario  = $_POST['usuario']; 
    $password = $_POST['password'];

    // Verificar si se subió nueva imagen
    if ($_FILES['nueva_imagen']['name']) {
        $imgNombre = $_FILES['nueva_imagen']['name'];
        $imgTmp    = $_FILES['nueva_imagen']['tmp_name'];
        $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
        $imgFinal  = strtolower($usuario . "_" . time() . "." . $ext);
        $imgDestino = "../IMG/usuarios/" . $imgFinal;

        if (move_uploaded_file($imgTmp, $imgDestino)) {
            // Eliminar imagen anterior si existe
            if (!empty($imagenActual) && file_exists("../IMG/usuarios/" . $imagenActual)) {
                unlink("../IMG/usuarios/" . $imagenActual);
            }
            // Actualizar con nueva imagen
            $sql = "UPDATE usuario SET 
                nombre='$nombre', 
                email='$email', 
                usuario='$usuario', 
                password='$password', 
                imagen='$imgFinal' 
                WHERE id='$id'";
        } else {
            echo "<script>alert('No se pudo subir la nueva imagen');</script>";
            exit();
        }
    } else {
        // Sin imagen nueva
        $sql = "UPDATE usuario SET 
            nombre='$nombre', 
            email='$email', 
            usuario='$usuario', 
            password='$password' 
            WHERE id='$id'";
    }

    $ejecutar = $con->query($sql);
    if ($ejecutar > 0) {  
        echo "
        <script>
            alert('El usuario se actualizó correctamente');
            window.location = 'usuarios.php';
        </script>";
    } else {
        echo "
        <script>
            alert('El usuario no se pudo actualizar');
            window.location = 'editarUsuarios.php?id=$id';
        </script>";
    }
}

if (isset($_REQUEST['btn_eliminar'])) {
    $id = $_POST['id'];

    // Borrar imagen si existe
    if (!empty($imagenActual) && file_exists("../IMG/usuarios/" . $imagenActual)) {
        unlink("../IMG/usuarios/" . $imagenActual);
    }

    $sql = "DELETE FROM usuario WHERE id = '$id'";
    $ejecutar = $con->query($sql);

    if ($ejecutar > 0) {  
        echo "
        <script>
            alert('El usuario se eliminó con éxito');
            window.location = 'usuarios.php';
        </script>";
    } else {
        echo "
        <script>
            alert('El usuario no se pudo eliminar');
            window.location = 'editarUsuarios.php?id=$id';
        </script>";
    }
}
?>

<div class="content-wrapper">
        <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2"> 
          <div class="col-sm-6">
               <h1>Editar Usuario</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="usuarios.php">Regresar</a></li>
              <li class="breadcrumb-item active">Editar Usuarios</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-body">
 <form action="editarUsuarios.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
       <div class="mb-3">
            <label class="form-label">N°</label>
            <input type="text" class="form-control" name="id" value="<?php echo $id; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo $nombre; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Correo:</label>
            <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario:</label>
            <input type="text" class="form-control" name="usuario" value="<?php echo $usuario; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña:</label>
            <input type="text" class="form-control" name="password" value="<?php echo $password; ?>">
        </div>
        <div class="mb-3">
    <label class="form-label">Imagen actual:</label><br>
    <?php if (!empty($imagenActual)) { ?>
        <img src="../IMG/usuarios/<?php echo $imagenActual; ?>" width="120">
    <?php } else { ?>
        <p>No tiene imagen</p>
    <?php } ?>
        </div>

<div class="mb-3">
    <label class="form-label">Nueva imagen de perfil:</label>
    <input type="file" class="form-control" name="nueva_imagen" accept="image/*">
</div>

        <button type="submit" class="btn btn-primary" name="btn_editar">Actualizar Usuarios</button>
        <button type="submit" class="btn btn-danger" name="btn_eliminar">Eliminar Usuarios</button>
</form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>


</div>

<?php
include 'footer.php';
?>
