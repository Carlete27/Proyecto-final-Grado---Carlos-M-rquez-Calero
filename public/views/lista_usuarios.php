<div class="container py-5">
    <h2 class="text-center mb-4">Listado de Usuarios</h2>
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
    </div>            
    <div class="table-responsive">
        <!-- Tabla para mostrar información de usuarios LDAP -->
        <table class="table table-bordered table-hover text-center">
            <thead class="thead-dark bg-primary text-white">
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre</th>
                    <th>Grupo</th>
                    <th>Correo Electrónico</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($data) && !empty($data)): ?>
                    <?php foreach ($data as $usuario): ?>
                        <tr>
                            <!-- uid = identificador único del usuario -->
                            <td><?= htmlspecialchars($usuario['uid'] ?? 'N/A') ?></td>
                            <!-- cn = nombre completo del usuario -->
                            <td><?= htmlspecialchars($usuario['cn'] ?? 'N/A') ?></td>
                            <!-- Grupo al que pertenece el usuario -->
                            <td><?= htmlspecialchars($usuario['grupo'] ?? 'N/A') ?></td>
                            <!-- Dirección de correo electrónico -->
                            <td><?= htmlspecialchars($usuario['mail'] ?? 'N/A') ?></td>
                            <td>
                                <!-- Botón para eliminar usuario con confirmación JavaScript -->
                                <a href="index.php?controller=UsuarioController&action=eliminar&uid=<?= urlencode($usuario['uid']) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Mensaje cuando no hay usuarios -->
                    <tr>
                        <td colspan="5" class="text-muted">No se encontraron usuarios</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Botón de navegación para volver al panel principal -->
    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>