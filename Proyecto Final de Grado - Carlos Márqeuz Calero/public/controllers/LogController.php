<?php
/**
 * LogController.php
 * Controlador para visualizar y gestionar los logs del sistema LDAP
 */
require_once 'models/ModeloLDAP.php';

class LogController {
    /**
     * Método para mostrar los logs clasificados por tipo de operación
     * Obtiene todos los logs del contenedor y los clasifica para mejor visualización
     */
    public function mostrarLogs() {
        // Obtener todos los logs desde el contenedor con el modelo
        $logModel = new ModeloLDAP();
        $todosLosLogs = $logModel->obtenerLogsDesdeContenedor();

        // Clasificar los logs por tipo de operación LDAP
        $logsClasificados = [
            'bind' => [],       // Operaciones de autenticación
            'search' => [],     // Operaciones de búsqueda
            'add' => [],        // Operaciones de adición
            'delete' => [],     // Operaciones de eliminación
            'error' => [],      // Errores del sistema
            'otros' => []       // Otros tipos de logs
        ];

        // Clasificamos cada línea de log según su contenido
        foreach ($todosLosLogs as $linea) {
            if (strpos($linea, 'BIND') !== false) {
                $logsClasificados['bind'][] = $linea;
            } elseif (strpos($linea, 'SEARCH') !== false) {
                $logsClasificados['search'][] = $linea;
            } elseif (strpos($linea, 'ADD') !== false) {
                $logsClasificados['add'][] = $linea;
            } elseif (strpos($linea, 'DELETE') !== false) {
                $logsClasificados['delete'][] = $linea;
            } elseif (stripos($linea, 'error') !== false || stripos($linea, 'failed') !== false) {
                $logsClasificados['error'][] = $linea;
            } else {
                $logsClasificados['otros'][] = $linea;
            }
        }

        // Mostramos la vista con los logs clasificados
        View::show("views/logs_ldap.php", ['logs' => $logsClasificados]);
    }
}
?>
