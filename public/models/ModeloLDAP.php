<?php
// ====================================================================
// MODELO ESPECIALIZADO PARA LA GESTIÓN DE USUARIOS EN LDAP
// ====================================================================
// Se encarga de todas las operaciones relacionadas con usuarios y grupos
// del sistema LDAP, incluyendo autenticación, creación, eliminación, etc.

class ModeloLDAP {
    // Variable privada que almacena nuestra conexión con el servidor LDAP
    private $ldapconn;

    // ====================================================================
    // CONSTRUCTOR Y CONFIGURACIÓN INICIAL
    // ====================================================================
    
    // Constructor: se ejecuta automáticamente al crear un objeto de esta clase
    public function __construct() {
        // Establecemos conexión con el servidor LDAP que está en Docker
        $this->ldapconn = ldap_connect("ldap://openldap");
        
        // Configuramos la versión 3 del protocolo LDAP (la más actual)
        ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    // Función auxiliar para autenticarse como administrador
    private function bindAdmin() {
        $adminDn = "cn=admin,dc=carlete,dc=sl";
        $adminPass = "admin270404";
        ldap_bind($this->ldapconn, $adminDn, $adminPass);
    }

    // ====================================================================
    // FUNCIONES DE AUTENTICACIÓN
    // ====================================================================

  // Función para verificar si las credenciales de un usuario son correctas
public function authenticate($user, $password) {
    // Definimos los dos tipos de DN posibles
    $dntipo1 = "cn=$user,ou=Usuarios,dc=carlete,dc=sl";
    $dntipo2 = "uid=$user,ou=Usuarios,dc=carlete,dc=sl";
    
    // Intentamos autenticar primero con el tipo 1 (cn)
    $auth1 = @ldap_bind($this->ldapconn, $dntipo1, $password);
    
    if ($auth1) {
        return true; // Autenticación exitosa con DN tipo 1
    }
    
    // Si falla con tipo 1, intentamos con tipo 2 (uid)
    $auth2 = @ldap_bind($this->ldapconn, $dntipo2, $password);
    
    return $auth2; // Retorna true si funciona con tipo 2, false si ambos fallan
}

// Función para verificar si un usuario tiene permisos de administrador
public function isAdmin($user) {
    // Nos autenticamos como admin para realizar la consulta
    $this->bindAdmin();
    
    // Definimos dónde buscar (en el grupo de administradores)
    $base = "cn=admins,ou=Grupos,dc=carlete,dc=sl";
    
    // Creamos filtros para buscar con ambos tipos de DN
    $filtro1 = "(member=cn=$user,ou=Usuarios,dc=carlete,dc=sl)";
    $filtro2 = "(member=uid=$user,ou=Usuarios,dc=carlete,dc=sl)";
    
    // Ejecutamos la búsqueda con el primer filtro (cn)
    $resultado1 = @ldap_search($this->ldapconn, $base, $filtro1);
    $entrada1 = @ldap_get_entries($this->ldapconn, $resultado1);
    
    // Si encontró resultados con el primer filtro, es admin
    if ($entrada1 && $entrada1["count"] > 0) {
        return true;
    }
    
    // Si no encontró con el primer filtro, probamos con el segundo (uid)
    $resultado2 = @ldap_search($this->ldapconn, $base, $filtro2);
    $entrada2 = @ldap_get_entries($this->ldapconn, $resultado2);
    
    // Retorna true si encontró resultados con cualquiera de los dos filtros
    return ($entrada2 && $entrada2["count"] > 0);
}

    // ====================================================================
    // FUNCIONES DE GESTIÓN DE USUARIOS
    // ====================================================================

    // Función auxiliar que obtiene los datos crudos de usuarios desde LDAP
    public function getAllUsersRaw() {
        $this->bindAdmin();
        $baseDn = "ou=Usuarios,dc=carlete,dc=sl";
        $filter = "(objectClass=inetOrgPerson)";
        $result = @ldap_search($this->ldapconn, $baseDn, $filter);
        return @ldap_get_entries($this->ldapconn, $result);
    }

    // Función para obtener todos los usuarios del sistema de forma limpia
    public function getAllUsers() {
        // Obtenemos los datos crudos de LDAP
        $rawUsers = $this->getAllUsersRaw();
        $usuarios = [];
        
        // Procesamos cada usuario para mostrar solo la información relevante
        for ($i = 0; $i < $rawUsers['count']; $i++) {
            $user = $rawUsers[$i];
            
            // Obtenemos los grupos a los que pertenece este usuario
            $grupos = $this->getUserGroups($user['uid'][0] ?? '');
            
            // Creamos un array limpio con la información del usuario
            $usuarios[] = [
                'uid' => $user['uid'][0] ?? 'N/A',               // ID de usuario
                'cn' => $user['cn'][0] ?? 'N/A',                 // Nombre común
                'sn' => $user['sn'][0] ?? 'N/A',                 // Apellido
                'mail' => $user['mail'][0] ?? 'N/A',             // Email
                'grupo' => !empty($grupos) ? implode(', ', $grupos) : 'Sin grupo',
                'dn' => $user['dn'] ?? ''                        // Distinguished Name
            ];
        }
        return $usuarios;
    }

    // Función para crear un nuevo usuario en el sistema
    public function crearUsuario($uid, $cn, $sn, $password, $email, $ou = "Usuarios") {
        $this->bindAdmin();
        
        // Construimos el DN del nuevo usuario
        $dn = "uid=$uid,ou=$ou,dc=carlete,dc=sl";
        
        // Definimos los atributos del usuario
        $entry = [
            "objectClass" => ["inetOrgPerson"],  // Tipo de objeto LDAP
            "uid" => $uid,                       // ID único del usuario
            "cn" => $cn,                         // Nombre común
            "sn" => $sn,                         // Apellido
            "userPassword" => $password,         // Contraseña del usuario
            "mail" => $email                     // Dirección de email
        ];
        
        // Creamos el usuario en LDAP
        return @ldap_add($this->ldapconn, $dn, $entry);
    }

    // Función para eliminar un usuario del sistema
    public function deleteUser($uid) {
        $this->bindAdmin();
        $dn = "uid=$uid,ou=Usuarios,dc=carlete,dc=sl";
        return @ldap_delete($this->ldapconn, $dn);
    }

    // Función para obtener información de un usuario específico
    public function getUserByUid($uid) {
        $this->bindAdmin();
        $baseDn = "ou=Usuarios,dc=carlete,dc=sl";
        $filter = "(uid=$uid)";
        $result = @ldap_search($this->ldapconn, $baseDn, $filter);
        $entries = @ldap_get_entries($this->ldapconn, $result);
        
        // Si encontramos el usuario, devolvemos su información
        if ($entries['count'] > 0) {
            $user = $entries[0];
            return [
                'uid' => $user['uid'][0] ?? '',
                'cn' => $user['cn'][0] ?? '',
                'sn' => $user['sn'][0] ?? '',
                'mail' => $user['mail'][0] ?? '',
                'dn' => $user['dn'] ?? '',
                'grupos' => $this->getUserGroups($uid)
            ];
        }
        
        return null; // Usuario no encontrado
    }

    // Función auxiliar para obtener los grupos de un usuario específico
    public function getUserGroups($uid) {
        if (empty($uid)) return [];
        
        $this->bindAdmin();
        $baseDn = "ou=Grupos,dc=carlete,dc=sl";
        $filter = "(&(objectClass=groupOfNames)(member=uid=$uid,ou=Usuarios,dc=carlete,dc=sl))";
        
        $result = ldap_search($this->ldapconn, $baseDn, $filter);
        if (!$result) return [];
        
        $entries = ldap_get_entries($this->ldapconn, $result);
        $grupos = [];
        
        // Extraemos los nombres de los grupos
        for ($i = 0; $i < $entries['count']; $i++) {
            $grupos[] = $entries[$i]['cn'][0] ?? '';
        }
        
        return array_filter($grupos); // Eliminamos valores vacíos
    }

    // Función para verificar si un usuario existe en el sistema
    public function userExists($uid) {
        return $this->getUserByUid($uid) !== null;
    }

    // ====================================================================
    // FUNCIONES DE GESTIÓN DE GRUPOS
    // ====================================================================

    // Función auxiliar que obtiene los datos crudos de grupos desde LDAP
    public function getAllGroupsRaw() {
        $this->bindAdmin();
        $baseDn = "ou=Grupos,dc=carlete,dc=sl";
        $filter = "(objectClass=groupOfNames)";
        $result = @ldap_search($this->ldapconn, $baseDn, $filter);
        return ldap_get_entries($this->ldapconn, $result);
    }

    // Función para obtener todos los grupos del sistema de forma limpia
    public function getAllGroups() {
        // Obtenemos los datos crudos de LDAP
        $rawGroups = $this->getAllGroupsRaw();
        $grupos = [];
        
        // Procesamos cada grupo para mostrar solo la información relevante
        for ($i = 0; $i < $rawGroups['count']; $i++) {
            $group = $rawGroups[$i];
            $grupos[] = [
                'cn' => $group['cn'][0] ?? 'N/A',                    // Nombre del grupo
                'description' => $group['description'][0] ?? '',     // Descripción
                'dn' => $group['dn'] ?? '',                          // Distinguished Name
                'members' => $this->getGroupMembers($group['cn'][0] ?? '') // Miembros
            ];
        }
        return $grupos;
    }

    // Función para crear un nuevo grupo en el sistema
    public function crearGrupo($cn, $description = '', $miembros = [], $ou = "Grupos") {
        $this->bindAdmin();
        
        // Construimos el DN del nuevo grupo
        $dn = "cn=$cn,ou=$ou,dc=carlete,dc=sl";
        
        // Definimos los atributos básicos del grupo
        $entry = [
            "objectClass" => ["groupOfNames"],  // Tipo de objeto LDAP
            "cn" => $cn                         // Nombre común del grupo
        ];
        
        // Agregamos descripción si se proporciona
        if (!empty($description)) {
            $entry["description"] = $description;
        }
        
        // Agregamos miembros al grupo
        if (!empty($miembros)) {
            $memberDns = [];
            foreach ($miembros as $uid) {
                $memberDns[] = "uid=$uid,ou=Usuarios,dc=carlete,dc=sl";
            }
            $entry["member"] = $memberDns;
        } else {
            // Los grupos necesitan al menos un miembro - usando un DN válido
            $entry["member"] = ["cn=admin,dc=carlete,dc=sl"];
        }
        
        return @ldap_add($this->ldapconn, $dn, $entry);
    }

    // Función para eliminar un grupo del sistema
    public function deleteGroup($cn) {
        $this->bindAdmin();
        $dn = "cn=$cn,ou=Grupos,dc=carlete,dc=sl";
        
        // Verificamos que el grupo existe antes de eliminarlo
        if (!$this->getGroupByName($cn)) {
            return false;
        }
        
        return @ldap_delete($this->ldapconn, $dn);
    }

    // Función para obtener información completa de un grupo
    public function getGroupByName($cn) {
        $this->bindAdmin();
        $baseDn = "cn=$cn,ou=Grupos,dc=carlete,dc=sl";
        $filter = "(objectClass=groupOfNames)";
        
        $result = @ldap_search($this->ldapconn, $baseDn, $filter);
        if (!$result) return null;
        
        $entries = ldap_get_entries($this->ldapconn, $result);
        if ($entries['count'] == 0) return null;
        
        $group = $entries[0];
        return [
            'cn' => $group['cn'][0] ?? '',
            'description' => $group['description'][0] ?? '',
            'dn' => $group['dn'] ?? '',
            'members' => $this->getGroupMembers($cn)
        ];
    }

    // Función para obtener los miembros de un grupo específico
    public function getGroupMembers($groupCn) {
        $this->bindAdmin();
        $baseDn = "cn=$groupCn,ou=Grupos,dc=carlete,dc=sl";
        $filter = "(objectClass=groupOfNames)";
        
        $result = ldap_search($this->ldapconn, $baseDn, $filter);
        if (!$result) return [];
        
        $entries = ldap_get_entries($this->ldapconn, $result);
        if ($entries['count'] == 0) return [];
        
        $group = $entries[0];
        $miembros = [];
        
        // Extraemos los UIDs de los DNs de los miembros
        if (isset($group['member'])) {
            for ($i = 0; $i < $group['member']['count']; $i++) {
                $memberDn = $group['member'][$i];
                // Extraemos el uid del DN completo usando expresión regular
                if (preg_match('/uid=([^,]+)/', $memberDn, $matches)) {
                    $miembros[] = $matches[1];
                }
            }
        }
        
        return $miembros;
    }

    // Función para verificar si un grupo existe
    public function groupExists($cn) {
        return $this->getGroupByName($cn) !== null;
    }

    // Función para agregar un usuario a un grupo
    public function addUserToGroup($uid, $groupCn) {
        $this->bindAdmin();
        
        $userDn = "uid=$uid,ou=Usuarios,dc=carlete,dc=sl";
        $groupDn = "cn=$groupCn,ou=Grupos,dc=carlete,dc=sl";
        
        // Obtenemos los miembros actuales del grupo
        $result = @ldap_search($this->ldapconn, $groupDn, "(objectClass=groupOfNames)");
        if (!$result) return false;
        
        $entries = @ldap_get_entries($this->ldapconn, $result);
        if ($entries['count'] == 0) return false;
        
        $group = $entries[0];
        $members = [];
        
        // Recopilamos miembros existentes
        if (isset($group['member'])) {
            for ($i = 0; $i < $group['member']['count']; $i++) {
                $members[] = $group['member'][$i];
            }
        }
        
        // Agregamos el nuevo usuario si no está ya en el grupo
        if (!in_array($userDn, $members)) {
            $members[] = $userDn;
        }
        
        // Actualizamos el grupo con la nueva lista de miembros
        $entry = ["member" => $members];
        return @ldap_modify($this->ldapconn, $groupDn, $entry);
    }



    // ====================================================================
    // FUNCIONES DE BACKUP Y SEGURIDAD
    // ====================================================================

    // Función para generar una copia de seguridad del directorio LDAP
    public function generarCopiaSeguridad() {
        $timestamp = date('Ymd_His');                    // Timestamp para el nombre del archivo
        $ruta = "/ldap/Seguridad/backup-$timestamp.ldif"; // Ruta donde se guardará el backup
        $comando = "docker exec openldap slapcat -v -l $ruta";  // Comando para crear el backup
        shell_exec($comando);                            // Ejecutamos el comando
        return $ruta;                                    // Devolvemos la ruta del archivo creado
    }



    // ====================================================================
    // FUNCIONES DE LOGS Y MONITOREO
    // ====================================================================

    // Función para obtener los logs del contenedor Docker de LDAP
    public function obtenerLogsDesdeContenedor() {
        // Ejecutamos comando para obtener logs del contenedor openldap
        $salida = shell_exec("docker logs --timestamps openldap 2>&1");

        // Si no se pudieron obtener los logs, devolvemos mensaje de error
        if ($salida === null) {
            return ["Error al obtener logs del contenedor."];
        }

        // Separamos cada línea de log y eliminamos líneas vacías
        $lineas = explode(PHP_EOL, $salida);
        return array_filter($lineas);
    }

}

?>