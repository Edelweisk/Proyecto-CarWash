<?php
// Iniciar la sesión actual para poder manipularla
session_start();

// Eliminar todas las variables de sesión actuales
session_unset();

// Destruir la sesión completamente, eliminando todos los datos asociados
session_destroy();

// Redirigir al usuario a la página de login después de cerrar sesión
header("Location: login.php");
exit(); // Asegura que no se ejecute código adicional después de la redirección
?>
