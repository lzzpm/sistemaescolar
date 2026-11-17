<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión del administrador
session_destroy();

// Regresar a la pantalla de inicio de sesión
header("Location: login.php");
exit();
?>
