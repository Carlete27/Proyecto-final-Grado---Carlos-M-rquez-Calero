<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Crear Grupo LDAP</title>

    <!-- Bootstrap 4 desde CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main role="main" class="container mt-5 mb-5">
        <div class="py-5 text-center">
            <h2>Crear Nuevo Grupo LDAP</h2>
            <p class="lead">Rellena los campos para registrar un nuevo grupo en el servidor LDAP.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Manejo de mensajes de éxito/error de sesión -->
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success text-center">
                        <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    </div>
                <?php elseif (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=GrupoController&action=crearGrupo" method="POST">

                    <div class="form-group">
                        <label for="cn">Nombre del grupo (CN)</label>
                        <input type="text" class="form-control" id="cn" name="cn" required
                               value="<?= isset($_POST['cn']) ? htmlspecialchars($_POST['cn']) : '' ?>">
                        <small class="form-text text-muted">Solo letras minúsculas y números, sin espacios.</small>
                    </div>

                    <div class="form-group">
                        <label for="ou">Unidad Organizativa (OU)</label>
                        <input type="text" class="form-control" id="ou" name="ou" 
                               value="<?= isset($_POST['ou']) ? htmlspecialchars($_POST['ou']) : 'Grupos' ?>" required>
                        <small class="form-text text-muted">Por defecto es "Grupos", cambia si usas otra OU.</small>
                    </div>

                    <div class="form-group">
                        <label for="miembros">Miembros del grupo</label>
                        <!-- Select múltiple para elegir usuarios -->
                        <select class="form-control" id="miembros" name="miembros[]" multiple size="6">
                            <?php if (!empty($usuarios) && is_array($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <?php if (is_array($usuario) && isset($usuario['uid']) && isset($usuario['cn'])): ?>
                                        <option value="<?= htmlspecialchars($usuario['uid']) ?>"
                                                <?= (isset($_POST['miembros']) && in_array($usuario['uid'], $_POST['miembros'])) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($usuario['cn']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay usuarios disponibles</option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Mantén pulsada Ctrl (o Cmd en Mac) para seleccionar varios usuarios.</small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Crear Grupo</button>
                        <a href="index.php" class="btn btn-secondary ml-2">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validación del campo CN: solo letras minúsculas y números
        document.getElementById('cn').addEventListener('input', function(e) {
            // Solo permitir letras minúsculas y números
            this.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '');
        });
    </script>
</body>
</html>