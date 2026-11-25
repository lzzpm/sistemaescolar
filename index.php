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

/*REGISTRO DE ALUMNOS*/

// Verifica si se enviaron datos desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    // Obtiene la información capturada
    $nombre = trim($_POST['nombre']);
    $mail = trim($_POST['mail']);
    $codigo_curso = (int)$_POST['codigocurso'];

    // Comprueba que los datos estén completos
    if (!empty($nombre) && !empty($mail) && $codigo_curso > 0) {
        
        // Revisa cuántos alumnos hay registrados en el curso
        $stmt_val = $conexion->prepare("SELECT COUNT(*) as total FROM alumnos WHERE Codigo_Curso = ?");
        $stmt_val->bind_param("i", $codigo_curso);
        $stmt_val->execute();
        $res_val = $stmt_val->get_result()->fetch_assoc();
        $stmt_val->close();

        // Si el curso llegó al límite de alumnos, muestra un aviso
        if ($res_val['total'] >= 6) {
            header("Location: index.php?error=cupo_lleno");
            exit();
        }

        // Comprueba si el correo ya existe en el sistema
        $stmt_mail = $conexion->prepare("SELECT Codigo FROM alumnos WHERE Correo = ?");
        $stmt_mail->bind_param("s", $mail);
        $stmt_mail->execute();
        $stmt_mail->store_result();
        // Comprueba si el correo ya existe en el sistema
        if ($stmt_mail->num_rows > 0) {
            $stmt_mail->close();
            header("Location: index.php?error=ya_registrado");
            exit();
        }
        $stmt_mail->close();

        // Guarda al nuevo alumno en la base de datos
        try {
            // Genera una matrícula para el alumno
            $matricula = 'A' . rand(1000, 9999);
            $stmt = $conexion->prepare("INSERT INTO alumnos (Matricula, Nombre, Correo, Codigo_Curso) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $matricula, $nombre, $mail, $codigo_curso);
            // Comprueba si el registro fue exitoso
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?exito=1");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            // Muestra aviso si existe un correo repetido
            if ($e->getCode() === 1062) {
                header("Location: index.php?error=ya_registrado");
                exit();
            }
            // Muestra cualquier otro error encontrado
            die("Error inesperado en la base de datos: " . $e->getMessage());
        }
    }
}

/*CONSULTA DE CURSOS*/

// Obtiene la lista de cursos disponibles
$cursos_bd = $conexion->query("SELECT id, nombre_curso FROM cursos ORDER BY nombre_curso ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar alumno | Sistema Escolar</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<div class="app-shell">
    <!-- Menú lateral del sistema -->
    <?php require_once 'sidebar.php'; ?>

    <main class="main-content">
        <!-- Barra superior del navegación -->
        <div class="main-topbar">
            <div class="breadcrumb">Sistema escolar / <strong>Registro de alumnos</strong></div>
            <div class="welcome">Bienvenido, <?= htmlspecialchars($_SESSION["admin_usuario"], ENT_QUOTES, "UTF-8") ?></div>
        </div>

        <div class="contenedor">
            <!-- Encabezado de la página -->
            <div class="encabezado-pagina">
                <div>
                    <h1>Registro de alumnos</h1>
                    <p>Completa los datos para dar de alta a un alumno en el sistema.</p>
                </div>
            </div>

            <!-- Mensaje de registro exitoso -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="msg-exito">¡Alumno registrado con éxito!</div>
            <?php endif; ?>

            <!-- Mensajes de error -->
            <?php if (($_GET['error'] ?? '') === 'cupo_lleno'): ?>
                <div class="msg-error">Lo sentimos, este curso ya alcanzó el límite máximo de 6 alumnos.</div>
            <?php endif; ?>

            <?php if (($_GET['error'] ?? '') === 'ya_registrado'): ?>
                <div class="msg-error">Este correo electrónico ya se encuentra registrado en un curso</div>
            <?php endif; ?>

            <!-- Formulario para registrar alumnos -->
            <form action="index.php" method="POST">
                <div class="campo">
                    <label for="nombre">Nombre completo</label>
                    <!-- Campo para capturar el nombre -->
                    <input id="nombre" type="text" name="nombre" placeholder="Escribe el nombre del alumno" autocomplete="name" required>
                </div>

                <div class="campo" style="margin-top:18px">
                    <label for="mail">Correo electrónico</label>
                    <!-- Campo para capturar el correo -->
                    <input id="mail" type="email" name="mail" placeholder="alumno@ejemplo.com" autocomplete="email" required>
                </div>

                <div class="campo" style="margin-top:18px">
                    <label for="codigocurso">Curso</label>
                    <!-- Lista de cursos disponibles -->
                    <select id="codigocurso" name="codigocurso" required>
                        <option value="">Selecciona un curso</option>
                        <?php while ($c = $cursos_bd->fetch_assoc()): ?>
                            <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre_curso'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="margin-top:24px">
                    <!-- Botón para registrar al alumno -->
                    <input type="submit" value="Registrar alumno" style="width:100%">
                </div>
            </form>
            <!-- Enlaces de acceso rápido -->
            <div class="enlaces-pie">
                <a class="boton" href="lista_alumnos.php" style="background:#eaf1ff;color:#1d4ed8">Ver alumnos registrados</a>
                <a class="boton" href="cursos.php" style="background:#dcfce7;color:#166534">Administrar cursos</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>