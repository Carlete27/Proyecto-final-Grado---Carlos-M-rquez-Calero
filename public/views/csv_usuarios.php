<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Crear Usuarios desde CSV</title>

    <!-- Bootstrap 4 desde CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main role="main" class="container mt-5">
        <div class="py-5 text-center">
            <h2>Crear Usuarios desde Archivo CSV</h2>
            <p class="lead">Sube un archivo CSV con los datos de los usuarios que deseas crear en el servidor LDAP.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Formulario con enctype para subida de archivos -->
                <form action="index.php?controller=UsuarioController&action=procesarCSV" method="POST" enctype="multipart/form-data" class="border p-4 bg-light rounded shadow-sm">

                    <div class="form-group">
                        <label for="csv">Selecciona archivo CSV</label>
                        <!-- Input de tipo file restringido a archivos .csv -->
                        <input type="file" class="form-control-file" id="csv" name="csv" accept=".csv" required>
                        <small class="form-text text-muted">
                            El archivo debe tener las columnas: <strong>uid, cn, sn, password, email, UO</strong>
                        </small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Subir y Crear</button>
                        <a href="index.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>