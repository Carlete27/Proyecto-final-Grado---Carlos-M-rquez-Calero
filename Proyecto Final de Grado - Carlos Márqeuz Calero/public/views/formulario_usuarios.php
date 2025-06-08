<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Crear Usuario LDAP</title>

    <!-- Bootstrap 4 desde CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main role="main" class="container mt-5 mb-5">
        <div class="py-5 text-center">
            <h2>Crear Nuevo Usuario LDAP</h2>
            <p class="lead">Rellena los campos para registrar un nuevo usuario en el servidor LDAP.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Mensajes de éxito/error específicos para usuarios -->
                <?php if (!empty($_SESSION['mensaje_exito'])): ?>
                    <div class="alert alert-success text-center">
                        <?= htmlspecialchars($_SESSION['mensaje_exito']); unset($_SESSION['mensaje_exito']); ?>
                    </div>
                <?php elseif (!empty($_SESSION['mensaje_error'])): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($_SESSION['mensaje_error']); unset($_SESSION['mensaje_error']); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=UsuarioController&action=crear" method="POST">

                    <div class="form-group">
                        <label for="uid">Nombre de usuario (UID)</label>
                        <input type="text" class="form-control" id="uid" name="uid" required>
                        <small class="form-text text-muted">Solo letras minúsculas y números, sin espacios.</small>
                    </div>

                    <div class="form-group">
                        <label for="cn">Nombre completo (CN)</label>
                        <input type="text" class="form-control" id="cn" name="cn" required>
                    </div>

                    <div class="form-group">
                        <label for="sn">Apellidos (SN)</label>
                        <input type="text" class="form-control" id="sn" name="sn" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <small class="form-text text-muted">Ingresa una dirección de correo válida.</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">Mínimo 6 caracteres.</small>
                    </div>

                    <div class="form-group">
                        <label for="grupo">Grupo</label>
                        <!-- Dropdown con grupos disponibles cargados desde LDAP -->
                        <select class="form-control" id="grupo" name="grupo">
                            <option value="">Seleccionar grupo (opcional)</option>
                            <?php if (isset($data) && !empty($data)): ?>
                                <?php foreach ($data as $grupo): ?>
                                    <option value="<?= htmlspecialchars($grupo['cn']) ?>">
                                        <?= htmlspecialchars($grupo['cn']) ?>
                                        <!-- Muestra descripción del grupo si existe -->
                                        <?php if (!empty($grupo['description'])): ?>
                                            - <?= htmlspecialchars($grupo['description']) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay grupos disponibles</option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Selecciona el grupo al que pertenecerá el usuario.</small>
                    </div>

                    <div class="form-group">
                        <label for="ou">Unidad Organizativa (OU)</label>
                        <input type="text" class="form-control" id="ou" name="ou" value="Usuarios" required>
                        <small class="form-text text-muted">Por defecto es "Usuarios", cambia si usas otra OU.</small>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
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
        // Validación en tiempo real del campo UID
        document.getElementById('uid').addEventListener('input', function(e) {
            // Solo permitir letras minúsculas y números
            this.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '');
        });
        
        // Validación de longitud mínima de contraseña
        document.getElementById('password').addEventListener('input', function(e) {
            if (this.value.length < 6) {
                this.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>