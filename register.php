<?php 
  include_once "conexion.php";
    if(isset($_REQUEST['btn_registrar'])){
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $usuario = $_POST['usuario']; 
        $password = $_POST['password'];

        $sql = "SELECT id, nombre FROM usuario WHERE usuario = '$usuario'";
        $ejecutar = $con->query($sql);
        $filas = $ejecutar->num_rows;
        if($filas > 0){ 
            echo "
            <script>
                alert('Este usuario ya existe, por favor registro uno nuevo');
            </script> 
            ";
        }else{
            $sql = "INSERT INTO usuario(nombre, email, usuario, password)
                    VALUES('$nombre', '$email', '$usuario', '$password')";
            $ejecutar = $con->query($sql);
            if ($ejecutar > 0){
                echo "
            <script>
                alert('Se ha registrado exitosamente');
            </script> 
            ";
            }else{
                echo "
            <script>
                alert('No ha sido registrado');
            </script> 
            ";
            }
        }
    }
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>

<?php 
    include_once "header.php"; 
?>

<div class="container">
    <h1>Registrarme</h1>
    <form action="register.php" method="POST">
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
        <button type="submit" class="btn btn-success" name="btn_registrar">Registrarme</button>
        <a href="index.php" class="btn btn-danger">Regresar</a>
  </form>
</div>

</body>
</html>
