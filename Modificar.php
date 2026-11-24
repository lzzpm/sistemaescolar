<?php
session_start();
if (!isset($_SESSION['admin_usuario'])) {
    header("Location: login.php");
    exit();
}
require_once 'conexion.php';
$codigo = isset($_GET['codigo']) ? (int)$_GET['codigo'] : 0;
if ($codigo <= 0) {
    header("Location: lista_alumnos.php");
    exit();
}
$stmt = $conexion->prepare("SELECT * FROM alumnos WHERE Codigo = ?");
$stmt->bind_param("i", $codigo);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows == 0) {
    die("Alumno no encontrado");
}
$alumno = $resultado->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $curso = (int)$_POST['curso'];
    $stmt = $conexion->prepare("UPDATE alumnos SET Matricula=?, Nombre=?, Correo=?, Codigo_Curso=? WHERE Codigo=?");
    $stmt->bind_param("sssii", $matricula, $nombre, $correo, $curso, $codigo);

    if ($stmt->execute()) {
        header("Location: lista_alumnos.php");
        exit();
    }
}
$cursos = $conexion->query("SELECT id, nombre_curso FROM cursos ORDER BY nombre_curso ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar alumno | Sistema Escolar</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="app-shell">
    <?php require_once 'sidebar.php'; ?>
    <main class="main-content">
        <div class="main-topbar">
            <div class="breadcrumb">Sistema escolar / <strong>Modificar alumno</strong></div>
            <div class="welcome">Bienvenido, <?= htmlspecialchars($_SESSION["admin_usuario"], ENT_QUOTES, "UTF-8") ?></div>
        </div>
        <div class="contenedor">
            <div class="encabezado-pagina">
                <div>
                    <h1>Modificar alumno</h1>
                    <p>Actualiza la información del alumno registrado.</p>
                </div>
            </div>
            <form method="POST">
                <div class="campo">
                    <label>Matrícula</label>
                    <input type="text" name="matricula" value="<?= htmlspecialchars($alumno['Matricula'], ENT_QUOTES, "UTF-8") ?>" required>
                </div>
                <div class="campo" style="margin-top:18px">
                    <label>Nombre completo</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($alumno['Nombre'], ENT_QUOTES, "UTF-8") ?>" required>
                </div>
                <div class="campo" style="margin-top:18px">
                    <label>Correo electrónico</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($alumno['Correo'], ENT_QUOTES, "UTF-8") ?>" required>
                </div>
                <div class="campo" style="margin-top:18px">
                    <label>Curso</label>
                    <select name="curso" required>
                        <?php while($c = $cursos->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" <?= ($c['id'] == $alumno['Codigo_Curso']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre_curso'], ENT_QUOTES, "UTF-8") ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="margin-top:24px">
                    <input type="submit" value="Guardar cambios" style="width:100%;">
                </div>
            </form>
            <div class="enlaces-pie">
                <a class="boton" href="lista_alumnos.php" style="background:#eaf1ff;color:#1d4ed8">Ver alumnos registrados</a>
                <a class="boton" href="cursos.php" style="background:#dcfce7;color:#166534">Administrar cursos</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>