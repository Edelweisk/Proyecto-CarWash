<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body>
    
<?php 
    include_once "header.php"; 
?>

<div class="container"> 
   <h1>Lista de Cursos</h1>
<?php 
 include_once "conexion.php";
 $sql = "SELECT * FROM cursos";
 $ejecutar = $con->query($sql);
 while($filas = mysqli_fetch_array($ejecutar)){

?>

<div class="card" style="width: 18rem;">
   <img src="data:image/jpg;base64, <?php echo base64_encode($filas['img']); ?> " class="card-img-top" alt="...">
   <div class="card-body">
    <h5 class="card-title"><?php echo $filas['titulo']?></h5>
    <p class="card-text"><?php echo $filas['descripción']?></p>
    <a href="#" class="btn btn-success">Ver curso</a>
  </div>
</div>

<?php } ?>
</div>



    
</body>
</html>