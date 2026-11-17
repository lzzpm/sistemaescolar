<?php
/// Cargar el archivo .env de forma manual y sencilla si existe localmente
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
    }
}

// Obtener las variables (priorizando el .env local o el entorno de Docker/Clever Cloud)
$host = getenv('DB_HOST') ?: getenv('MYSQL_ADDON_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: getenv('MYSQL_ADDON_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: getenv('MYSQL_ADDON_DB') ?: 'sistemaescolar';
$user = getenv('DB_USER') ?: getenv('MYSQL_ADDON_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: getenv('MYSQL_ADDON_PASSWORD') ?: '';

// Crear la conexión con la base de datos
$conexion = mysqli_init();

// Verifica que la conexión pueda iniciarse
if (!$conexion) {
    die("Error al inicializar MySQLi");
}

// Permite la conexión segura con Clever Cloud
$conexion->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

// Intenta conectarse a la base de datos
$conectado = $conexion->real_connect(
    $host,
    $user,
    $pass,
    $db,
    (int)$port,
    NULL,
    MYSQLI_CLIENT_SSL
);

// Comprueba si la conexión fue exitosa
if (!$conectado) {
    die("Error de conexión a Clever Cloud (" . mysqli_connect_errno() . "): " . mysqli_connect_error());
}

// Configura la base de datos para aceptar caracteres especiales y acentos
$conexion->set_charset("utf8mb4");
?>