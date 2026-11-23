<?php
session_start();
require_once 'conexion.php';

$error = '';
$mensaje = '';
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'login';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // INICIAR SESIÓN
    if (isset($_POST['accion_login'])) {

        $correo = mysqli_real_escape_string(
            $conexion,
            $_POST['correo']
        );
        $password = $_POST['password'];
        $res = mysqli_query(
            $conexion,
            "SELECT * FROM usuarios WHERE correo = '$correo'"
        );
        if ($usr = mysqli_fetch_assoc($res)) {
            if (password_verify($password, $usr['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_usuario'] = $usr['nombre'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "El correo no está registrado";
        }
    }
    // REGISTRAR UNA CUENTA
    if (isset($_POST['accion_registro'])) {
        $nombre = mysqli_real_escape_string(
            $conexion,
            $_POST['nombre']
        );
        $correo = mysqli_real_escape_string(
            $conexion,
            $_POST['correo']
        );
        $pass_hash = password_hash(
            $_POST['password'],
            PASSWORD_BCRYPT
        );
        $query = "INSERT INTO usuarios
                  (nombre, correo, password)
                  VALUES ('$nombre', '$correo', '$pass_hash')";
        if (mysqli_query($conexion, $query)) {
            $mensaje = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
            $vista = 'login';
        } else {
            $error = "El correo ya se encuentra registrado.";
            $vista = 'registro';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Acceso | Sistema Escolar</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px;

            /* FONDO CON IMAGEN ESCOLAR */
            background:
                linear-gradient(
                    rgba(3, 18, 65, 0.55),
                    rgba(3, 18, 65, 0.65)
                ),
                url('fondo-escolar.jpg');
            background-color: #06165f;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        /* CONTENEDOR SIN MARCOS EXTERIORES */
        .marco {
            width: 100%;
            max-width: 535px;
            padding: 0;
            background: transparent;
            border: none;
            box-shadow: none;
        }
        /* PANEL AZUL ELIMINADO */
        .panel {
            width: 100%;
            background: transparent;
            padding: 0;
            min-height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* ÚNICAMENTE LA VENTANA BLANCA */
        .contenedor {
            width: 100%;
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25);
        }
        /* ICONO */
        .icono {
            width: 58px;
            height: 58px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #edf2ff;
            color: #233fa5;
            font-size: 27px;
        }
        /* TÍTULOS */
        h1 {
            text-align: center;
            color: #10236d;
            font-size: 25px;
            margin-bottom: 24px;
        }
        .subtitulo {
            text-align: center;
            color: #74809a;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 28px;
        }
        /* ETIQUETAS */
        label {
            display: block;
            margin-bottom: 7px;
            color: #263f86;
            font-size: 13px;
            font-weight: bold;
        }
        /* CAMPOS */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 14px;
            margin-bottom: 20px;
            border: 1px solid #c7d4ef;
            border-radius: 7px;
            background: #ffffff;
            color: #263247;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #3853b5;
            box-shadow: 0 0 0 3px rgba(56, 83, 181, 0.12);
        }
        /* BOTÓN PRINCIPAL */
        input[type="submit"] {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #3853b5;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.25s, transform 0.25s;
        }
        input[type="submit"]:hover {
            background: #233d9a;
            transform: translateY(-1px);
        }
        /* BOTÓN DE REGISTRO */
        .btn-verde {
            background: #218838 !important;
        }
        .btn-verde:hover {
            background: #176b2b !important;
        }
        /* ENLACES */
        .enlace {
            display: block;
            text-align: center;
            color: #2947a3;
            text-decoration: none;
            font-size: 12px;
            margin-top: 20px;
            font-weight: bold;
        }
        .enlace:hover {
            color: #07146d;
            text-decoration: underline;
        }
        /* MENSAJES */
        .msg-error {
            background: #fff0f0;
            color: #c62828;
            border: 1px solid #ffcaca;
            padding: 11px;
            border-radius: 6px;
            text-align: center;
            font-size: 13px;
            margin-bottom: 18px;
        }
        .msg-exito {
            background: #edfff2;
            color: #19713a;
            border: 1px solid #b9e8c7;
            padding: 11px;
            border-radius: 6px;
            text-align: center;
            font-size: 13px;
            margin-bottom: 18px;
        }
        /* TEXTO INFERIOR */
        .pie {
            text-align: center;
            color: #74809a;
            font-size: 12px;
            margin-top: 22px;
        }

        /* ADAPTACIÓN A CELULARES */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .contenedor {
                padding: 25px 20px;
            }

            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<div class="marco">
    <div class="panel">
        <div style="width: 100%;">
            <div class="contenedor">
                <?php if ($vista == 'login'): ?>
                    <div class="icono">🎓</div>
                    <h1>Iniciar Sesión</h1>
                    <p class="subtitulo">
                        Accede al sistema escolar
                    </p>
                    <?php if ($error): ?>
                        <div class="msg-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($mensaje): ?>
                        <div class="msg-exito">
                            <?php echo htmlspecialchars($mensaje); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input
                            type="hidden"
                            name="accion_login"
                            value="1"
                        >
                        <label for="correo">Correo:</label>
                        <input
                            type="email"
                            id="correo"
                            name="correo"
                            placeholder="Ingresa tu correo"
                            autocomplete="email"
                            required
                        >
                        <label for="password">Contraseña:</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            required
                        >
                        <input type="submit" value="Entrar">
                    </form>
                    <a
                        href="login.php?vista=registro"
                        class="enlace"
                    >
                        ¿No tienes cuenta? Regístrate aquí
                    </a>
                <?php else: ?>
                    <div class="icono">👤</div>
                    <h1>Crear Cuenta</h1>
                    <p class="subtitulo">
                        Regístrate en el sistema escolar
                    </p>
                    <?php if ($error): ?>
                        <div class="msg-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input
                            type="hidden"
                            name="accion_registro"
                            value="1"
                        >
                        <label for="nombre">Nombre:</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            placeholder="Tu nombre completo"
                            autocomplete="name"
                            required
                        >
                        <label for="correo_registro">Correo:</label>
                        <input
                            type="email"
                            id="correo_registro"
                            name="correo"
                            placeholder="Tu correo electrónico"
                            autocomplete="email"
                            required
                        >
                        <label for="password_registro">Contraseña:</label>
                        <input
                            type="password"
                            id="password_registro"
                            name="password"
                            placeholder="Crea una contraseña"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <input
                            type="submit"
                            value="Crear mi cuenta"
                            class="btn-verde"
                        >
                    </form>
                    <a
                        href="login.php?vista=login"
                        class="enlace"
                    >
                        ¿Ya tienes cuenta? Inicia sesión
                    </a>
                <?php endif; ?>
            </div>
            <div class="pie">
                Sistema de Administración Escolar
            </div>
        </div>
    </div>
</div>
</body>
</html>