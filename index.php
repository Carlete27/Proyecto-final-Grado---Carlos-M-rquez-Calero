<?php
// ====================================================================
// ARCHIVO PRINCIPAL DE LA APLICACIÓN DE GESTIÓN LDAP
// ====================================================================
// Este archivo maneja el login de usuarios y redirige a la gestión
// una vez autenticados correctamente

// Cargamos todas las clases necesarias para el funcionamiento
require_once 'models/ModeloLDAP.php';      // Modelo para operaciones LDAP
require_once 'views/view.php';             // Vista principal
require_once 'controllers/UsuarioController.php';  // Controlador de usuarios
require_once 'controllers/GrupoController.php';    // Controlador de grupos
require_once 'controllers/BackupController.php';   // Controlador de backups
require_once 'controllers/LogController.php';      // Controlador de logs

// Cargamos configuración desde el archivo .env
$env = parse_ini_file('.env');
$organizacion = $env['LDAP_ORGANISATION'];  // Nombre de la organización
$dominio = $env['LDAP_DOMAIN'];            // Dominio LDAP

// Variable para almacenar mensajes de error
$error = '';

// ====================================================================
// PROCESAMIENTO DEL FORMULARIO DE LOGIN
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["usuario"], $_POST["contrasena"])) {
    $user = $_POST["usuario"];
    $password = $_POST["contrasena"];

    // Creamos una instancia del modelo LDAP
    $ldap = new ModeloLDAP();

    // Intentamos autenticar al usuario
    if ($ldap->authenticate($user, $password)) {
        // Si la autenticación es exitosa, iniciamos sesión
        $_SESSION["usuario"] = $user;

        // Verificamos si el usuario tiene permisos de administrador
        if ($ldap->isAdmin($user)) {
            $_SESSION["es_admin"] = true;
        }

        // Redirigimos a la página de gestión
        View::show("views/gestion.php");
        exit;
    } else {
        // Si falla la autenticación, mostramos error
        $error = "Usuario o contraseña incorrectos.";
    }
}

// ====================================================================
// MANEJO DE USUARIOS YA AUTENTICADOS
// ====================================================================
if (isset($_SESSION["usuario"])) {
    // Obtenemos los parámetros de controlador y acción desde la URL
    $controller = $_GET['controller'] ?? null;
    $action = $_GET['action'] ?? null;

    // Si hay controlador y acción específicos, los ejecutamos
    if ($controller && $action) {
        require_once "controllers/$controller.php";
        $obj = new $controller();
        $obj->$action();
        exit;
    }

    // Si no hay acción específica, mostramos la vista de gestión
    View::show("views/gestion.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - <?= htmlspecialchars($organizacion) ?></title>
    <!-- Bootstrap CSS para el diseño responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <!-- Tarjeta del formulario de login -->
                <div class="card shadow-lg">
                    <!-- Cabecera de la tarjeta -->
                    <div class="card-header text-center bg-primary text-white">
                        <h3><?= htmlspecialchars($organizacion) ?> - Acceso</h3>
                    </div>
                    
                    <!-- Cuerpo de la tarjeta con el formulario -->
                    <div class="card-body">
                        <!-- Mostramos mensaje de error si existe -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Formulario de login -->
                        <form method="POST" action="index.php">
                            <!-- Campo de usuario -->
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" name="usuario" id="usuario" class="form-control" required>
                            </div>
                            
                            <!-- Campo de contraseña -->
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" name="contrasena" id="contrasena" class="form-control" required>
                            </div>
                            
                            <!-- Botón de envío -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Pie de la tarjeta -->
                    <div class="card-footer text-muted text-center">
                        © <?= date("Y") ?> <?= htmlspecialchars($organizacion) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>