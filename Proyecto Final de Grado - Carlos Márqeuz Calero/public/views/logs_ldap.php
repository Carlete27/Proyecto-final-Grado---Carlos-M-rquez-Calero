<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Logs OpenLDAP</title>
    <!-- Usa Bootstrap 4 (diferente versión que los otros archivos) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h1 class="text-center mb-4">Logs de OpenLDAP</h1>

    <?php
    // Array que traduce los tipos de logs a nombres descriptivos
    $nombresTipos = [
        'bind' => 'Operaciones de BIND (autenticación)',
        'search' => 'Operaciones de SEARCH (búsqueda)',
        'add' => 'Operaciones de ADD (alta)',
        'delete' => 'Operaciones de DELETE (baja)',
        'error' => 'Errores',
        'otros' => 'Otros Logs'
    ];

    // Recorre cada tipo de log y muestra una tabla por cada uno
    foreach ($logs as $tipo => $lineas):
        if (empty($lineas)) continue; // Omite tipos sin logs
    ?>
        <div class="card mb-5 shadow">
            <div class="card-header bg-primary text-white">
                <?= $nombresTipos[$tipo] ?>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Contenido</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lineas as $log): ?>
                        <tr>
                            <?php
                            // Extrae la fecha usando expresión regular (formato ISO)
                            preg_match('/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+)/', $log, $match);
                            $fecha = $match[1] ?? 'N/A';
                            $contenido = $log;
                            ?>
                            <!-- Columna de fecha con texto sin saltos de línea -->
                            <td style="white-space: nowrap;"><?= htmlspecialchars($fecha) ?></td>
                            <!-- Columna con el contenido completo del log -->
                            <td><?= htmlspecialchars($contenido) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Botón para recargar/actualizar los logs -->
    <div class="text-center mt-5">
        <a href="index.php?controller=LogController&action=mostrarLogs" class="btn btn-secondary">Actualizar Logs</a>
    </div>
</div>

</body>
</html>