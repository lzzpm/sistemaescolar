<?php
session_start();
if (!isset($_SESSION['admin_usuario'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';

$codigo = filter_input(INPUT_GET, 'codigo', FILTER_VALIDATE_INT);
if (!$codigo || $codigo < 1) {
    header("Location: lista_alumnos.php");
    exit();
}

$stmt = $conexion->prepare("DELETE FROM alumnos WHERE Codigo = ?");
$stmt->bind_param("i", $codigo);
$stmt->execute();
$stmt->close();

header("Location: lista_alumnos.php?msg=eliminado");
exit();
?>
