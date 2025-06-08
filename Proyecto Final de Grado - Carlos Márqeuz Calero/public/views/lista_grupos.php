<div class="container py-5">
    <h2 class="text-center mb-4">Listado de Grupos</h2>
    <div class="table-responsive">
        <!-- Tabla para mostrar información de grupos LDAP -->
        <table class="table table-bordered table-hover text-center">
            <thead class="thead-dark bg-primary text-white">
                <tr>
                    <th>Nombre del Grupo</th>
                    <th>Miembros</th>
                    <th>Cantidad de Miembros</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($grupos) && !empty($grupos)): ?>
                    <?php foreach ($grupos as $grupo): ?>
                        <tr>
                            <!-- Nombre del grupo -->
                            <td><?= htmlspecialchars($grupo['cn'] ?? 'N/A') ?></td>
                            <td>
                                <!-- Lista de miembros del grupo separados por comas -->
                                <?php if (!empty($grupo['members'])): ?>
                                    <?= htmlspecialchars(implode(', ', $grupo['members'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin miembros</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Contador de miembros -->
                                <?= count($grupo['members'] ?? []) ?>
                            </td>
                            <td>
                                <!-- Botón para eliminar grupo con confirmación JavaScript -->
                                <a href="index.php?controller=GrupoController&action=eliminarGrupo&cn=<?= urlencode($grupo['cn']) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Estás seguro de que quieres eliminar el grupo \'<?= htmlspecialchars($grupo['cn']) ?>\'? Esta acción no se puede deshacer.')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Mensaje cuando no hay grupos -->
                    <tr>
                        <td colspan="4" class="text-muted">No se encontraron grupos</td>
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