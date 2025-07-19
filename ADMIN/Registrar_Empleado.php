<?php 
include_once "../conexion.php";
include 'header.php'; 

if (isset($_POST['btn_registrar'])) {
    $nombre   = $_POST['nombre'];
    $email    = $_POST['email'];
    $usuario  = $_POST['usuario']; 
    $password = $_POST['password'];
    $rol      = $_POST['rol'];

    $sql = "SELECT id FROM usuario WHERE usuario = '$usuario'";
    $ejecutar = $con->query($sql);
    
    if ($ejecutar->num_rows > 0) { 
        echo "<script>alert('El usuario ya existe, por favor ingrese otro');</script>"; 
    } else {
        $imgFinal = null;

        if (!empty($_FILES['imagen']['name'])) {
            $imgNombre = $_FILES['imagen']['name'];
            $imgTmp    = $_FILES['imagen']['tmp_name'];
            $ext       = pathinfo($imgNombre, PATHINFO_EXTENSION);
            $imgFinal  = strtolower($usuario . "_" . time() . "." . $ext);
            $imgDestino = "../IMG/usuarios/" . $imgFinal;
            move_uploaded_file($imgTmp, $imgDestino);
        }

        $sql = "INSERT INTO usuario(nombre, email, usuario, password, imagen, rol)
                VALUES('$nombre', '$email', '$usuario', '$password', '$imgFinal', '$rol')";

        if ($con->query($sql)) {
            echo "<script>alert('Empleado registrado correctamente');</script>";
        } else {
            echo "<script>alert('Error al registrar el empleado');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registrar Empleado - Debug Car Wash</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
  <link rel="stylesheet" href="../CSS/RegisterCss/Register.Css" />
</head>
<body>

<div class="content-wrapper">
  <section class="text-center mt-4" data-aos="fade-down">
    <h1 class="text-black">Registrar nuevo empleado</h1>
  </section>

  <section class="content">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-xl-10 col-xxl-9">
          <div class="card shadow" data-aos="zoom-in">
            <div class="card-body p-5">
              <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
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

                <div class="mb-3" data-aos="fade-up" data-aos-delay="300">
                  <label class="form-label">Dirección:</label>
                  <input type="text" class="form-control" name="direccion" required />
                </div>

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

                <div class="mb-3" data-aos="fade-up" data-aos-delay="500">
                  <label class="form-label">Correo electrónico:</label>
                  <input type="email" class="form-control" name="email" required />
                </div>

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

                <div class="mb-3" data-aos="fade-up" data-aos-delay="700">
                  <label class="form-label">Rol del empleado:</label>
                  <select class="form-select" name="rol" required>
                    <option value="" disabled selected>Seleccione un rol</option>
                    <option value="Secretaria">Secretaria</option>
                    <option value="TecnicoLavado">Técnico de Lavado</option>
                  </select>
                </div>

                <div class="mb-4" data-aos="fade-up" data-aos-delay="800">
                  <label class="form-label">Imagen de perfil:</label>
                  <input type="file" class="form-control" name="imagen" accept="image/*" />
                </div>

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

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true
  });
</script>

</body>
</html>
