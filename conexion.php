<?php  
$host = "localhost";
$user = "root";
$password = "ROOT";
$db = "plataformacurso";

$con = mysqli_connect($host, $user, $password, $db);

if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
