<?php
// Inicia la sesión del usuario
session_start();
// Verifica que exista una sesión activa
if (!isset($_SESSION['admin_usuario'])) {
    // Redirige al usuario al inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}
// Incluye la conexión a la base de datos
require_once 'conexion.php';
// Obtiene el código del alumno enviado por la URL
$codigo = isset($_GET['codigo']) ? (int)$_GET['codigo'] : 0;
// Verifica que el código sea válido
if ($codigo <= 0) {
    // Regresa a la lista de alumnos si el código es incorrecto
    header("Location: lista_alumnos.php");
    exit();
}
// Consulta los datos del alumno seleccionado
$stmt = $conexion->prepare("SELECT * FROM alumnos WHERE Codigo = ?");
// Asocia el parámetro a la consulta
$stmt->bind_param("i", $codigo);
// Ejecuta la consulta
$stmt->execute();
// Obtiene el resultado de la consulta
$resultado = $stmt->get_result();
// Verifica que el alumno exista
if ($resultado->num_rows == 0) {
    // Muestra un mensaje si el alumno no fue encontrado
    die("Alumno no encontrado");
}
$alumno = $resultado->fetch_assoc();
// Procesa los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene la matrícula actualizada
    $matricula = trim($_POST['matricula']);
    // Obtiene el nombre actualizado
    $nombre = trim($_POST['nombre']);
    // Obtiene el correo actualizado
    $correo = trim($_POST['correo']);
    // Obtiene el curso seleccionado
    $curso = (int)$_POST['curso'];
    // Consulta para actualizar los datos del alumno
    $stmt = $conexion->prepare("UPDATE alumnos SET Matricula=?, Nombre=?, Correo=?, Codigo_Curso=? WHERE Codigo=?");
    // Asocia los valores a actualizar
    $stmt->bind_param("sssii", $matricula, $nombre, $correo, $curso, $codigo);
    // Ejecuta la actualización del alumno
    if ($stmt->execute()) {
        // Regresa al listado de alumnos después de actualizar
        header("Location: lista_alumnos.php");
        exit();
    }
}
// Obtiene la lista de cursos disponibles
$cursos = $conexion->query("SELECT id, nombre_curso FROM cursos ORDER BY nombre_curso ASC");
?>
<!-- Configuración del encabezado de la página -->
<!-- Hoja de estilos principal -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar alumno | Sistema Escolar</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<!-- Contenido principal del sistema -->
<body>
<div class="app-shell">
    <!-- Menú lateral de navegación -->
    <?php require_once 'sidebar.php'; ?>
    <main class="main-content">
        <div class="main-topbar">
            <div class="breadcrumb">Sistema escolar / <strong>Modificar alumno</strong></div>
            <div class="welcome">Bienvenido, <?= htmlspecialchars($_SESSION["admin_usuario"], ENT_QUOTES, "UTF-8") ?></div>
        </div>
        <div class="contenedor">
            <!-- Información de la página actual -->
            <div class="encabezado-pagina">
                <div>
                    <h1>Modificar alumno</h1>
                    <p>Actualiza la información del alumno registrado.</p>
                </div>
            </div>
            <form method="POST">
                <!-- Campo de matrícula -->
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
                    <!-- Selección de curso -->
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
                    <!-- Botón para guardar los cambios realizados -->
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