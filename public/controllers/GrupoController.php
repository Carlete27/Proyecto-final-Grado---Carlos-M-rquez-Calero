<?php
/**
 * GrupoController.php
 * Controlador para la gestión de grupos en el sistema LDAP
 * Incluye funcionalidades para listar, crear y eliminar grupos
 */

require_once "models/ModeloLDAP.php";
require_once 'views/view.php';

class GrupoController {
    
    /**
     * Función para mostrar la lista de todos los grupos del sistema
     * Obtiene todos los grupos desde LDAP y los muestra en una vista
     */
    public function listar() {
        // Creamos una conexión con LDAP
        $ldap = new ModeloLDAP();
        
        // Obtenemos todos los grupos del directorio LDAP
        $grupos = $ldap->getAllGroups();
        
        // Mostramos la vista con la lista de grupos
        View::show("views/lista_grupos.php", ["grupos" => $grupos]);
    }

    /**
     * Función para mostrar el formulario de creación de grupos
     * Carga todos los usuarios disponibles para poder asignarlos al grupo
     */
    public function mostrarFormulario() {
        $ldap = new ModeloLDAP();
        
        // Obtenemos todos los usuarios para mostrarlos en el formulario
        $usuarios = $ldap->getAllUsers();
        
        // Debug: agregar esta línea temporalmente para verificar datos
        error_log("Usuarios obtenidos: " . print_r($usuarios, true));
        
        // Mostramos el formulario con la lista de usuarios disponibles
        View::show("views/formulario_grupos.php", ["usuarios" => $usuarios]);
    }

    /**
     * Función para crear un nuevo grupo en el sistema
     * Procesa el formulario de creación y valida los datos
     */
    public function crearGrupo() {
        // Solo procesamos si es una petición POST (desde un formulario)
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: index.php?controller=GrupoController&action=listar");
            exit;
        }

        // Recogemos los datos del formulario
        $cn = trim($_POST['cn']);                    // Nombre común del grupo
        $ou = trim($_POST['ou'] ?? 'Grupos');       // Unidad organizacional (por defecto 'Grupos')
        $miembro = $_POST['miembro'] ?? '';         // Un solo miembro inicial

        // Validación básica: el nombre del grupo es obligatorio
        if (empty($cn)) {
            $_SESSION['error'] = "El nombre del grupo es obligatorio.";
            header("Location: index.php?controller=GrupoController&action=mostrarFormulario");
            exit;
        }

        $ldap = new ModeloLDAP();

        // Verificamos que no exista ya un grupo con ese nombre
        if ($ldap->groupExists($cn)) {
            $_SESSION['error'] = "Ya existe un grupo con el nombre '$cn'.";
            header("Location: index.php?controller=GrupoController&action=mostrarFormulario");
            exit;
        }

        // Procesamos los miembros seleccionados del formulario
        $miembrosSeleccionados = $_POST['miembros'] ?? [];
        $miembrosValidos = [];

        // Validamos que cada miembro seleccionado existe en LDAP
        if (!empty($miembrosSeleccionados) && is_array($miembrosSeleccionados)) {
            foreach ($miembrosSeleccionados as $uid) {
                if ($ldap->getUserByUid($uid)) {
                    $miembrosValidos[] = $uid;
                }
            }
        }

        // Crear el grupo con los miembros válidos
        if ($ldap->crearGrupo($cn, '', $miembrosValidos, $ou)) {
            $_SESSION['success'] = "Grupo '$cn' creado exitosamente.";
            if (!empty($miembrosValidos)) {
                $_SESSION['success'] .= " Se agregó el usuario '$miembro' como miembro inicial.";
            }
        } else {
            $_SESSION['error'] = "Error al crear el grupo '$cn'. Verifique los datos e intente nuevamente.";
        }

        // Redirigimos al formulario de grupos
        header("Location: index.php?controller=GrupoController&action=mostrarFormulario");
        exit;
    }

    /**
     * Función para eliminar un grupo del sistema
     * Elimina el grupo especificado y todos sus miembros
     */
    public function eliminarGrupo() {
        // Obtenemos el nombre del grupo desde la URL
        $cn = $_GET['cn'] ?? '';
        
        // Validamos que se haya especificado un grupo
        if (empty($cn)) {
            $_SESSION['error'] = "Grupo no especificado para eliminar.";
            header("Location: index.php?controller=GrupoController&action=listar");
            exit;
        }

        $ldap = new ModeloLDAP();

        // Verificamos que el grupo existe antes de eliminarlo
        if (!$ldap->groupExists($cn)) {
            $_SESSION['error'] = "El grupo '$cn' no existe.";
            header("Location: index.php?controller=GrupoController&action=listar");
            exit;
        }

        // Obtenemos información del grupo antes de eliminarlo (para mostrar estadísticas)
        $grupo = $ldap->getGroupByName($cn);
        $cantidadMiembros = count($grupo['members'] ?? []);

        // Intentamos eliminar el grupo
        if ($ldap->deleteGroup($cn)) {
            $_SESSION['success'] = "Grupo '$cn' eliminado exitosamente.";
            if ($cantidadMiembros > 0) {
                $_SESSION['success'] .= " Se removieron $cantidadMiembros miembro(s).";
            }
        } else {
            $_SESSION['error'] = "Error al eliminar el grupo '$cn'. Intente nuevamente.";
        }

        // Redirigimos a la lista de grupos
        header("Location: index.php?controller=GrupoController&action=listar");
        exit;
    }
}
?>
