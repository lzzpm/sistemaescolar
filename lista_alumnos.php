<?php
// Inicia la sesión del administrador
session_start();
// Comprueba que el usuario haya iniciado sesión
if (!isset($_SESSION['admin_usuario'])) {
    header("Location: login.php");
    exit();
}
// Conecta con la base de datos
require_once 'conexion.php';

/*FILTROS Y BÚSQUEDA DE ALUMNOS*/

// Obtiene los datos de búsqueda enviados desde el formulario
$filtro_curso = trim($_GET['filtro_curso'] ?? '');
$buscar = trim($_GET['buscar'] ?? '');

/*CONSULTA DE ALUMNOS*/

// Consulta que obtiene los alumnos y el curso al que pertenecen
$sql = "SELECT a.Codigo, a.Matricula, a.Nombre, a.Correo, c.nombre_curso
        FROM alumnos a
        LEFT JOIN cursos c ON a.Codigo_Curso = c.id
        WHERE 1=1";
$tipos = '';
$parametros = [];

// Aplica el filtro por curso seleccionado
if ($filtro_curso !== '' && ctype_digit($filtro_curso)) {
    $sql .= " AND a.Codigo_Curso = ?";
    $tipos .= 'i';
    $parametros[] = (int)$filtro_curso;
}
// Aplicar la búsqueda por nombre, matrícula o código
if ($buscar !== '') {
    $sql .= " AND (a.Nombre LIKE ? OR a.Matricula LIKE ? OR CAST(a.Codigo AS CHAR) LIKE ?)";
    $tipos .= 'sss';
    $termino = '%' . $buscar . '%';
    $parametros[] = $termino;
    $parametros[] = $termino;
    $parametros[] = $termino;
}
// Ordena los registros del más reciente al más antiguo
$sql .= " ORDER BY a.Codigo DESC";

/*EJECUTAR CONSULTA*/

// Prepara y ejecuta la consulta
$stmt = $conexion->prepare($sql);
if ($tipos !== '') {
    $stmt->bind_param($tipos, ...$parametros);
}
$stmt->execute();
// Guarda los resultados encontrados
$registros = $stmt->get_result();
/*LISTA DE CURSOS*/

// Obtiene todos los cursos para el filtro
$cursos_lista = $conexion->query("SELECT id, nombre_curso FROM cursos ORDER BY nombre_curso ASC");
// Cuenta el total de resultados encontrados
$total = $registros->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumnos registrados | Sistema Escolar</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="app-shell">
    <!-- Menú lateral del sistema -->
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <!-- Barra superior de navegación -->
        <div class="main-topbar">
            <div class="breadcrumb">Sistema escolar / <strong>Alumnos registrados</strong></div>
            <div class="welcome">Bienvenido, <?= htmlspecialchars($_SESSION["admin_usuario"], ENT_QUOTES, "UTF-8") ?></div>
        </div>

        <div class="contenedor">
            <!-- Encabezado de la página -->
            <div class="encabezado-pagina">
                <div>
                    <h1>Alumnos registrados</h1>
                    <p>Consulta, busca y administra los registros de alumnos.</p>
                </div>
            </div>

            <?php if (($_GET['msg'] ?? '') === 'eliminado'): ?>
                <!-- Mensaje cuando un alumno es eliminado -->
                <div class="msg-exito">El alumno fue eliminado correctamente.</div>
            <?php endif; ?>
            <!-- Formulario para filtrar y buscar alumnos -->
            <form method="GET" class="filtros">
                <div class="campo">
                    <label for="filtro_curso">Filtrar por curso</label>
                    <!-- Lista de cursos disponibles -->
                    <select id="filtro_curso" name="filtro_curso">
                        <option value="">Todos los cursos</option>
                        <?php while ($c = $cursos_lista->fetch_assoc()): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= ($filtro_curso == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre_curso'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="campo">
                    <label for="buscar">Buscar alumno</label>
                    <!-- Campo para buscar alumnos -->
                    <input id="buscar" type="text" name="buscar" placeholder="Nombre, matrícula o código" value="<?= htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="acciones-filtro">
                    <button type="submit">Buscar</button>
                    <a class="limpiar" href="lista_alumnos.php">Limpiar</a>
                </div>
            </form>
            <!-- Resumen de resultados encontrados -->
            <div class="resumen">
                <span>Resultados de la consulta</span>
                <strong><?= $total ?> alumno<?= $total === 1 ? '' : 's' ?></strong>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Curso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($total > 0): ?>
                        <?php while ($reg = $registros->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars((string)$reg['Matricula'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($reg['Nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($reg['Correo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($reg['nombre_curso'] ?: 'Sin curso', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a class="boton btn-mod" href="Modificar.php?codigo=<?= (int)$reg['Codigo'] ?>">Modificar</a>
                                    <a class="boton btn-del" href="eliminar.php?codigo=<?= (int)$reg['Codigo'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este alumno?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="tabla-vacia">No se encontraron alumnos con esos criterios.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="enlaces-pie">
                <a class="boton" href="index.php" style="background:#eaf1ff;color:#1d4ed8">Registrar alumno</a>
                <a class="boton" href="cursos.php" style="background:#dcfce7;color:#166534">Gestionar cursos</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>