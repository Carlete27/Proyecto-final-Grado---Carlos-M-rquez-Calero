<?php
/**
 * UsuarioController.php
 * Controlador principal para la gestión de usuarios en el sistema LDAP
 * Incluye funcionalidades para listar, crear, eliminar usuarios y cargar desde CSV
 */

// Iniciamos una Sesión para mantener estado entre peticiones
session_start();

require_once "models/ModeloLDAP.php";
require_once 'views/view.php';

class UsuarioController {
    
    /**
     * Método para listar todos los usuarios del sistema
     * Obtiene todos los usuarios desde LDAP y los muestra en una vista
     */
    public function listar() {
        $ldap = new ModeloLDAP();
        $usuarios = $ldap->getAllUsers();
        
        // Pasar directamente el array de usuarios a la vista
        View::show("views/lista_usuarios.php", $usuarios);
    }

    /**
     * Método para mostrar el detalle de un usuario específico
     * Obtiene el usuario por su UID y muestra toda su información
     */
    public function detalle() {
        // Obtener UID desde los parámetros GET
        $uid = $_GET['uid'] ?? null;
        
        if ($uid) {
            $ldap = new ModeloLDAP();
            $usuario = $ldap->getUserByUid($uid);
            
            // Mostrar la vista de detalle con la información del usuario
            View::show("views/detalle_usuario.php", ["usuario" => $usuario]);
        } else {
            // Redirigir si no hay UID especificado
            header("Location: index.php?controller=UsuarioController&action=listar");
            exit;
        }
    }

    /**
     * Método para eliminar un usuario del sistema
     * Elimina el usuario especificado por su UID
     */
    public function eliminar() {
        // Obtener UID desde los parámetros GET
        $uid = $_GET['uid'] ?? null;
        
        if ($uid) {
            $ldap = new ModeloLDAP();
            $resultado = $ldap->deleteUser($uid);
            
            // Establecer mensaje de resultado en la sesión
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Usuario '$uid' eliminado correctamente.";
            } else {
                $_SESSION['mensaje_error'] = "Error al eliminar el usuario '$uid'.";
            }
        }
        
        // Redirigir a la lista de usuarios
        header("Location: index.php?controller=UsuarioController&action=listar");
        exit;
    }

    /**
     * Método para crear un nuevo usuario
     * Procesa el formulario de creación y opcionalmente asigna el usuario a un grupo
     */
    public function crear() {
        // Solo procesar si es una petición POST
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Recoger datos del formulario
            $uid = $_POST['uid'];           // Identificador único del usuario
            $cn = $_POST['cn'];             // Nombre común
            $sn = $_POST['sn'];             // Apellido
            $password = $_POST['password']; // Contraseña
            $email = $_POST['email'];       // Correo electrónico
            $grupo = $_POST['grupo'] ?? null; // Grupo opcional
            $ou = $_POST['ou'] ?? 'Usuarios'; // Unidad organizacional

            $ldap = new ModeloLDAP();
            
            // Intentar crear el usuario
            $exito = $ldap->crearUsuario($uid, $cn, $sn, $password, $email, $ou);

            if ($exito) {
                // Si se seleccionó un grupo, agregar el usuario al grupo
                if ($grupo && $grupo !== '') {
                    $grupoExito = $ldap->addUserToGroup($uid, $grupo);
                    if ($grupoExito) {
                        $_SESSION['mensaje_exito'] = "Usuario '$uid' creado correctamente y agregado al grupo '$grupo'.";
                    } else {
                        $_SESSION['mensaje_exito'] = "Usuario '$uid' creado correctamente, pero hubo un error al agregarlo al grupo.";
                    }
                } else {
                    $_SESSION['mensaje_exito'] = "Usuario '$uid' creado correctamente.";
                }
            } else {
                $_SESSION['mensaje_error'] = "Error al crear el usuario '$uid', el usuario ya existe.";
            }

            // Redirigir a la misma página para evitar reenvío de formulario
            header("Location: index.php?controller=UsuarioController&action=formularioCrear");
            exit;
        }
    }

    /**
     * Método para mostrar el formulario de creación de usuarios
     * Carga todos los grupos disponibles para el desplegable
     */
    public function formularioCrear() {
        // Obtener todos los grupos para el desplegable del formulario
        $ldap = new ModeloLDAP();
        $grupos = $ldap->getAllGroups();
        
        // Mostrar el formulario con los grupos disponibles
        View::show("views/formulario_usuarios.php", $grupos);
    }

    /**
     * Método para mostrar el formulario de carga masiva de usuarios desde CSV
     */
    public function mostrarFormularioCSV() {
        View::show("views/csv_usuarios.php");
    }

    /**
     * Método para procesar un archivo CSV con usuarios
     * Lee el archivo CSV y crea múltiples usuarios de forma masiva
     */
    public function procesarCSV() {
        // Validar que se haya subido un archivo correctamente
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            die("Error al subir el archivo CSV.");
        }

        $modelo = new ModeloLDAP();

        // Abrir el archivo CSV temporal
        $archivo = $_FILES['csv']['tmp_name'];
        $handle = fopen($archivo, 'r');
        if (!$handle) {
            die("No se pudo abrir el archivo.");
        }

        // Leer la primera fila (cabecera) para identificar columnas
        $cabecera = fgetcsv($handle);

        // Verificar que existan las columnas necesarias
        $requeridas = ['uid', 'cn', 'sn', 'password', 'mail'];
        foreach ($requeridas as $campo) {
            if (!in_array($campo, $cabecera)) {
                die("Faltan columnas obligatorias: uid, cn, sn, password, mail");
            }
        }

        // Crear índice para acceder a columnas por nombre
        $indice = array_flip($cabecera);

        // Inicializar contadores para el reporte
        $exitos = 0;
        $errores = [];

        // Procesar cada fila del CSV
        while (($datos = fgetcsv($handle)) !== false) {
            // Extraer datos de cada columna
            $uid = $datos[$indice['uid']] ?? '';
            $cn = $datos[$indice['cn']] ?? '';
            $sn = $datos[$indice['sn']] ?? '';
            $password = $datos[$indice['password']] ?? '';
            $mail = $datos[$indice['mail']] ?? '';

            // Validar que todos los campos requeridos tengan datos
            if ($uid && $cn && $sn && $password && $mail) {
                // Intentar crear el usuario
                $ok = $modelo->crearUsuario($uid, $cn, $sn, $password, $mail);
                if ($ok) {
                    $exitos++;
                } else {
                    $errores[] = "Error al crear usuario: $uid";
                }
            } else {
                $errores[] = "Datos incompletos en una fila.";
            }
        }

        // Cerrar el archivo
        fclose($handle);

        // Mostrar reporte de resultados
        echo "<div class='container mt-5'>";
        echo "<h2>Resultado del proceso:</h2>";
        echo "<p>✅ Usuarios creados correctamente: <strong>$exitos</strong></p>";

        // Mostrar errores si los hay
        if (!empty($errores)) {
            echo "<div class='alert alert-warning'><h5>⚠️ Errores:</h5><ul>";
            foreach ($errores as $e) {
                echo "<li>$e</li>";
            }
            echo "Ya existen todos estos Usuarios, pruebe a crear otros nuevos.";
            echo "</ul></div>";
        }

        // Enlace para volver al formulario
        echo '<a href="index.php?controller=UsuarioController&action=mostrarFormularioCSV" class="btn btn-primary mt-3">Volver</a>';
        echo "</div>";
    }

    /**
     * Método para cerrar sesión del usuario
     * Destruye la sesión y redirige al index
     */
    public function logout() {
        // Limpiar todas las variables de sesión
        session_unset();
        
        // Destruir la sesión completamente
        session_destroy();
        
        // Redirigir al índice principal
        header("Location: index.php");
        exit;
    }
}
?>