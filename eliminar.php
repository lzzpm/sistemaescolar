<?php
//Inicia la sesión del administrador
session_start();
//Verifica que el usuario haya iniciado sesión
if (!isset($_SESSION['admin_usuario'])) {
    // Si no ha iniciado sesión, lo envía al acceso
    header("Location: login.php");
    exit();
}
//Conecta con la base de datos
require_once 'conexion.php';

// Obtiene el código del alumno que se desea eliminar
$codigo = filter_input(INPUT_GET, 'codigo', FILTER_VALIDATE_INT);
//Comprueba que el código sea válido
if (!$codigo || $codigo < 1) {
    //Regresa a la lista de alumnos si el código no es correcto
    header("Location: lista_alumnos.php");
    exit();
}
//Elimina el alumno seleccionado de la base de datos
$stmt = $conexion->prepare("DELETE FROM alumnos WHERE Codigo = ?");
$stmt->bind_param("i", $codigo);
$stmt->execute();
$stmt->close();

// Regresa a la lista mostrando un mensaje de confirmación
header("Location: lista_alumnos.php?msg=eliminado");
exit();
?>
