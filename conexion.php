<?php
// Carga la configuración guardada en el archivo .env si existe
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignora las líneas que son comentarios
        if (strpos(trim($line), '#') === 0) continue;
        // Separa el nombre y valor de cada variable
        list($name, $value) = explode('=', $line, 2);
        // Guarda las variables para poder utilizarlas en el sistema
        putenv(trim($name) . '=' . trim($value));
    }
}

// Obtiene los datos necesarios para conectarse a la base de datos
$host = getenv('DB_HOST') ?: getenv('MYSQL_ADDON_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: getenv('MYSQL_ADDON_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: getenv('MYSQL_ADDON_DB') ?: 'sistemaescolar';
$user = getenv('DB_USER') ?: getenv('MYSQL_ADDON_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: getenv('MYSQL_ADDON_PASSWORD') ?: '';

// Inicia la conexión con MySQL
$conexion = mysqli_init();

// Verifica que la conexión pueda crearse correctamente
if (!$conexion) {
    die("Error al inicializar MySQLi");
}

// Habilita la conexión segura con el servidor
$conexion->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

// Intenta establecer la conexión con la base de datos
$conectado = $conexion->real_connect(
    $host,
    $user,
    $pass,
    $db,
    (int)$port,
    NULL,
    MYSQLI_CLIENT_SSL
);

// Comprueba si la conexión se realizó correctamente
if (!$conectado) {
    die("Error de conexión a Clever Cloud (" . mysqli_connect_errno() . "): " . mysqli_connect_error());
}

// Permite utilizar caracteres especiales como acentos y la letra ñ
$conexion->set_charset("utf8mb4");
?>