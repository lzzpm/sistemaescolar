<?php
// Inicia la sesión actual
session_start();

// Elimina todos los datos guardados en la sesión
$_SESSION = array();

// Cierra la sesión del administrador
session_destroy();

// Regresa al usuario a la pantalla de inicio de sesión
header("Location: login.php");
exit();
?>
