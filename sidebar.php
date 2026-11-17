<?php
// Obtiene el nombre de la página actual para saber cuál menú está abierto
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!-- Barra lateral del sistema -->
<aside class="sidebar">
    <!-- Logotipo y título del sistema -->
    <div class="sidebar-brand">
        <div class="sidebar-logo">🎓</div>
        <div>
            <strong>Sistema Escolar</strong>
            <small>Panel administrativo</small>
        </div>
    </div>
    <!-- Título del menú -->
    <div class="sidebar-label">Menú principal</div>
    <!-- Opciones de navegación -->
    <nav class="sidebar-nav" aria-label="Navegación principal">
        <!-- Acceso al panel principal -->
        <a class="<?= ($pagina_actual === 'index.php') ? 'active' : '' ?>" href="index.php">
            <span class="sidebar-icon">⌂</span>
            <span class="nav-text">Panel principal</span>
        </a>
        <!-- Acceso al apartado de alumnos -->
        <a class="<?= ($pagina_actual === 'lista_alumnos.php' || $pagina_actual === 'Modificar.php') ? 'active' : '' ?>" href="lista_alumnos.php">
            <span class="sidebar-icon">♙</span>
            <span class="nav-text">Alumnos</span>
        </a>
        <!-- Acceso al apartado de cursos -->
        <a class="<?= ($pagina_actual === 'cursos.php') ? 'active' : '' ?>" href="cursos.php">
            <span class="sidebar-icon">▤</span>
            <span class="nav-text">Cursos y horarios</span>
        </a>
    </nav>
    <!-- Parte inferior de la barra lateral -->
    <div class="sidebar-bottom">
        <!-- Botón para cerrar sesión -->
        <nav class="sidebar-nav">
            <a href="logout.php" class="cerrar-sesion">
                <span class="sidebar-icon">⇥</span>
                <span class="nav-text">Cerrar sesión</span>
            </a>
        </nav>
        <!-- Información del usuario que inició sesión -->
        <div class="sidebar-user">
            <!-- Muestra la primera letra del nombre del administrador -->
            <div class="sidebar-avatar">
                <?= htmlspecialchars(mb_substr($_SESSION["admin_usuario"] ?? 'A', 0, 1, "UTF-8"), ENT_QUOTES, "UTF-8") ?>
            </div>
            <!-- Nombre y rol del usuario -->
            <div class="user-info">
                <strong><?= htmlspecialchars($_SESSION["admin_usuario"] ?? 'Admin', ENT_QUOTES, "UTF-8") ?></strong>
                <small>Administrador</small>
            </div>
        </div>
    </div>
</aside>