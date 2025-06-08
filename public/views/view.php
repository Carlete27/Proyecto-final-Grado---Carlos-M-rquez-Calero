<?php 
/**
 * Clase View - Maneja la renderización de vistas en el patrón MVC
 */
class View {
    /**
     * Método estático para mostrar una vista con datos opcionales
     * @param string $viewName - Nombre del archivo de vista (sin extensión)
     * @param array|null $data - Array asociativo con datos para la vista
     */
    public static function show($viewName, $data = null) {
        // Convierte el array $data en variables individuales disponibles en la vista
        // Ejemplo: ['usuarios' => $lista] se convierte en $usuarios = $lista
        if (is_array($data)) {
            extract($data);
        }
        
        // Log de debug para verificar que los datos lleguen correctamente
        // (útil para depuración, se puede comentar en producción)
        if (isset($usuarios)) {
            error_log("View::show - Variable usuarios disponible, cantidad: " . count($usuarios));
        }
        
        // Estructura de la página: header + contenido + footer
        include_once("header.php");    // Cabecera común (navegación, CSS, etc.)
        include("$viewName");          // Contenido específico de la vista
        include_once("footer.php");    // Pie de página común
    }
}
?>