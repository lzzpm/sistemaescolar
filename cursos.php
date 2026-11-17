<?php
// Inicia la sesión para identificar al usuario que ingresó al sistema
session_start();
// Verifica si el administrador ha iniciado sesión
if (!isset($_SESSION['admin_usuario'])) {
    // Si no ha iniciado sesión, lo envía al formulario de acceso
    header("Location: login.php");
    exit();
}
// Conexión con la base de datos
require_once 'conexion.php';

// Variables que se utilizarán durante el programa
$curso_editar = null;
$error = '';

//Agregar cuso

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_curso'])) {
    // Obtiene los datos escritos en el formulario
    $nombre = trim($_POST['nombre_curso'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $horario = trim($_POST['horario'] ?? '');
    // Verifica que no existan campos vacíos
    if ($nombre !== '' && $instructor !== '' && $horario !== '') {
        // Guarda el nuevo curso en la base de datos
        $stmt = $conexion->prepare("INSERT INTO cursos (nombre_curso, instructor, horario) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $instructor, $horario);
        $stmt->execute();
        $stmt->close();
        // Regresa a la página mostrando mensaje de éxito
        header("Location: cursos.php?msg=creado");
        exit();
    }
    // Mensaje en caso de faltar información
    $error = 'Completa todos los campos para agregar el curso.';
}
// ACTUALIZAR UN CURSO EXISTENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_curso'])) {
    // Obtiene los datos modificados
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = trim($_POST['nombre_curso'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $horario = trim($_POST['horario'] ?? '');

    // Valida que la información sea correcta
    if ($id && $id > 0 && $nombre !== '' && $instructor !== '' && $horario !== '') {
        // Actualiza los datos del curso seleccionado
        $stmt = $conexion->prepare("UPDATE cursos SET nombre_curso = ?, instructor = ?, horario = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $instructor, $horario, $id);
        $stmt->execute();
        $stmt->close();
        // Muestra mensaje de actualización exitosa
        header("Location: cursos.php?msg=actualizado");
        exit();
    }
    // Mensaje si existe algún error en los datos
    $error = 'Revisa los datos del curso e inténtalo de nuevo.';
}
//ELIMINAR CURSO
if (isset($_GET['eliminar'])) {
    $id = filter_input(INPUT_GET, 'eliminar', FILTER_VALIDATE_INT);
    if ($id && $id > 0) {
        // Borra el curso de la base de datos
        $stmt = $conexion->prepare("DELETE FROM cursos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    // Regresa a la página con mensaje de confirmación
    header("Location: cursos.php?msg=eliminado");
    exit();
}
//Modificar
if (isset($_GET['editar'])) {
    $id_edit = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
    if ($id_edit && $id_edit > 0) {
        // Busca la información del curso seleccionado
        $stmt = $conexion->prepare("SELECT * FROM cursos WHERE id = ?");
        $stmt->bind_param("i", $id_edit);
        $stmt->execute();
        $resultado = $stmt->get_result();
        // Guarda los datos para mostrarlos en el formulario
        $curso_editar = $resultado->fetch_assoc();
        $stmt->close();
    }
}
// Listar cursos
$cursos = $conexion->query("SELECT * FROM cursos ORDER BY id DESC");
//Mensajes del sistema
$mensajes = [
    'creado' => '¡Curso agregado con éxito!',
    'actualizado' => '¡Curso actualizado con éxito!',
    'eliminado' => '¡Curso eliminado con éxito!'
];
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de cursos | Sistema Escolar</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="app-shell">
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <div class="main-topbar">
            <div class="breadcrumb">Sistema escolar / <strong>Cursos y horarios</strong></div>
            <div class="welcome">Bienvenido, <?= htmlspecialchars($_SESSION["admin_usuario"], ENT_QUOTES, "UTF-8") ?></div>
        </div>

        <div class="contenedor">
            <div class="encabezado-pagina">
                <div>
                    <h1>Gestión de cursos</h1>
                    <p>Administra los cursos, docentes y horarios.</p>
                </div>
            </div>

            <?php if (isset($mensajes[$msg])): ?>
                <div class="msg-exito"><?= htmlspecialchars($mensajes[$msg], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="msg-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <section>
                <h2><?= $curso_editar ? 'Modificar curso' : 'Agregar un curso' ?></h2>
                <form method="POST">
                    <?php if ($curso_editar): ?>
                        <input type="hidden" name="id" value="<?= (int)$curso_editar['id'] ?>">
                    <?php endif; ?>
                    <div class="formulario-campos">
                        <div class="campo">
                            <label for="nombre_curso">Nombre del curso</label>
                            <input id="nombre_curso" type="text" name="nombre_curso" placeholder="Ej. Matemáticas" value="<?= htmlspecialchars($curso_editar['nombre_curso'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="campo">
                            <label for="instructor">Profesor o docente</label>
                            <input id="instructor" type="text" name="instructor" placeholder="Nombre del docente" value="<?= htmlspecialchars($curso_editar['instructor'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="campo">
                            <label for="horario">Horario</label>
                            <input id="horario" type="text" name="horario" placeholder="Ej. Lunes 8:00 a 10:00" value="<?= htmlspecialchars($curso_editar['horario'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="enlaces-pie" style="justify-content:flex-start">
                        <?php if ($curso_editar): ?>
                            <input type="submit" name="actualizar_curso" value="Guardar cambios" class="btn-update">
                            <a href="cursos.php" class="boton btn-del">Cancelar</a>
                        <?php else: ?>
                            <input type="submit" name="guardar_curso" value="Agregar curso" class="btn-add">
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <h2>Cursos disponibles</h2>
            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Nombre del curso</th><th>Docente</th><th>Horario</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if ($cursos->num_rows > 0): ?>
                        <?php while ($c = $cursos->fetch_assoc()): ?>
                            <tr>
                                <td><?= (int)$c['id'] ?></td>
                                <td><strong><?= htmlspecialchars($c['nombre_curso'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($c['instructor'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($c['horario'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a class="boton btn-mod" href="cursos.php?editar=<?= (int)$c['id'] ?>">Modificar</a>
                                    <a class="boton btn-del" href="cursos.php?eliminar=<?= (int)$c['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este curso?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="tabla-vacia">Todavía no hay cursos registrados.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>