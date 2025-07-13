<?php
require_once('../conexion.php');
include 'header.php';
?>

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Listas de Usuarios</h1>
            <a href="crearUsuarios.php" class="btn btn-primary">Crear Usuarios</a>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              <li class="breadcrumb-item active">Listas de Usuarios</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>N°</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Usuario</th>
                    <th>Contraseña</th>
                    <th>Fecha de Creación</th>
                    <th>Acción</th>
                  </tr>
                  </thead>
                  <tbody>
                                      <?php
                  $sql = "SELECT * FROM usuario";
                  $ejecutar = $con->query($sql);
                  while ($fila = mysqli_fetch_array($ejecutar)){ ;
?>
                  <tr>
                    <td><?php echo $fila['id']  ?></td>
                    <td><?php echo $fila['nombre']  ?></td>
                    <td><?php echo $fila['email']  ?></td>
                    <td><?php echo $fila['usuario']  ?></td>
                    <td><?php echo $fila['password']  ?></td>
                    <td><?php echo $fila['fechaRegistro']  ?></td>
                    <td><a href="editarUsuarios.php?id= <?php echo $fila['id']  ?>" name="id" class="btn btn-primary">EDITAR</a></td>
                  </tr>
                  <?php  }  ?>
                  </tbody> 
                  </tfoot>
                </table>
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
    <!-- /.content -->
  </div>

<?php

include 'footer.php';

?>
