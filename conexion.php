<?php  
$host = "localhost";
$user = "root";
$password = "root";
$db = "plataformacurso";

$con = mysqli_connect($host, $user, $password, $db);

if (!$con) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}
?>
