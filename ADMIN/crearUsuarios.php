<?php
require_once('../conexion.php');
include 'header.php';

if (isset($_REQUEST['btn_registrar'])) {
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $usuario  = $_POST['usuario']; 
    $password = $_POST['password'];

    // Procesar imagen de perfil
    $imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorio = '../IMG/usuarios/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $nombreImagen = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $rutaImagen = $directorio . $nombreImagen;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            $imagen = $nombreImagen;
        }
    }

    // Verificar si el usuario ya existe
    $sql = "SELECT id FROM usuario WHERE usuario = '$usuario'";
    $ejecutar = $con->query($sql);
    $filas = $ejecutar->num_rows;

    if ($filas > 0) {
        echo "
        <script>
            alert('El usuario ya existe, por favor ingrese otro');
            window.location = 'crearUsuarios.php';
        </script>";
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO usuario(nombre, email, usuario, password, imagen)
                VALUES('$nombre', '$email', '$usuario', '$password', '$imagen')";
        $ejecutar = $con->query($sql);

        if ($ejecutar > 0) {
            echo "
            <script>
                alert('Se ha registrado con éxito');
                window.location = 'usuarios.php';
            </script>";
        } else {
            echo "
            <script>
                alert('No se ha podido registrar');
                window.location = 'crearUsuarios.php';
            </script>";
        }
    }
}
?>

<div class="content-wrapper">
        <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2"> 
          <div class="col-sm-6">
                <h1>Agregar Usuario</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="usuarios.php">Regresar</a></li>
              <li class="breadcrumb-item active">Agregar Usuarios</li>
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
                
    <form action="crearUsuarios.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre">
        </div>
        <div class="mb-3">
            <label class="form-label">Correo:</label>
            <input type="email" class="form-control" name="email">
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario:</label>
            <input type="text" class="form-control" name="usuario">
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña:</label>
            <input type="text" class="form-control" name="password">
        </div>
        <div class="mb-3">
        <label class="form-label">Imagen de Perfil:</label>
        <input type="file" class="form-control" name="imagen" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary" name="btn_registrar">Crear Usuarios</button>
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
