<?php 
include_once "../conexion.php";

if (isset($_REQUEST['btn_registrar'])) {
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $usuario  = $_POST['usuario']; 
    $password = $_POST['password'];

    // Validar si el usuario ya existe
    $sql = "SELECT id FROM usuario WHERE usuario = '$usuario'";
    $ejecutar = $con->query($sql);
    
    if ($ejecutar->num_rows > 0) { 
        echo "<script>alert('El usuario ya existe, por favor ingrese otro');</script>"; 
    } else {
        // Manejo de imagen
        $imgNombre = $_FILES['imagen']['name'];
        $imgTmp    = $_FILES['imagen']['tmp_name'];
        $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
        $imgFinal  = strtolower($usuario . "_" . time() . "." . $ext);
        $imgDestino = "../IMG/usuarios/" . $imgFinal;

        // Subir la imagen
        if (move_uploaded_file($imgTmp, $imgDestino)) {
            $sql = "INSERT INTO usuario(nombre, email, usuario, password, imagen)
                    VALUES('$nombre', '$email', '$usuario', '$password', '$imgFinal')";
        } else {
            // Si no subió imagen, guarda NULL
            $sql = "INSERT INTO usuario(nombre, email, usuario, password, imagen)
                    VALUES('$nombre', '$email', '$usuario', '$password', NULL)";
        }

        $ejecutar = $con->query($sql);

        if ($ejecutar > 0) {
            echo "<script>alert('Se ha registrado correctamente');</script>";
        } else {
            echo "<script>alert('No se ha registrado');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de nuevo usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>

<?php 
    include_once "headerregister.php"; 
    
?>

<div class="content-wrapper">
        <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2"> 
          <div class="col-sm-6">
               <h1>Registrarme</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Registrarme</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <section class="content">
      <div class="container">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-body">
    <form action="register.php" method="POST" enctype="multipart/form-data">
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
            <label class="form-label">Imagen de perfil:</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary" name="btn_registrar">Registrarme</button>
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

</body>
</html>

<?php
include 'footer.php';
?>
