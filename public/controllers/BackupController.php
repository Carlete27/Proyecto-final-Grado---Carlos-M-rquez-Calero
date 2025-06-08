<?php
/**
 * BackupController.php
 * Controlador para gestionar las copias de seguridad del sistema LDAP
 */
require_once 'models/ModeloLDAP.php';

class BackupController {
    private $modelo;

    /**
     * Constructor de la clase
     * Inicializa el modelo LDAP necesario para las operaciones de backup
     */
    public function __construct() {
        $this->modelo = new ModeloLDAP();
    }

    /**
     * Método para generar una copia de seguridad
     * Llama al modelo para crear el backup y redirige al index
     */
    public function generar() {
        // Genera la copia de seguridad a través del modelo LDAP
        $this->modelo->generarCopiaSeguridad();
        
        // Redirige al usuario de vuelta al index principal
        header("Location: index.php"); // o "gestion.php" si va aparte
        exit();
    }
}
?>