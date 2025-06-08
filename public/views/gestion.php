<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Formulario de Gestión</title>
    <!-- Bootstrap 4 desde CDN para estilos -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main role="main">
        <!-- ====================================================================
             SECCIÓN DE ENCABEZADO
             ==================================================================== -->
        <section class="py-5 text-center container">
            <div class="row py-lg-3">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <h1 class="fw-light">Panel de Gestión</h1>
                    <p class="lead text-muted">
                        Gestiona usuarios, grupos y el monitoreo desde este panel.
                    </p>
                </div>
            </div>
        </section>

        <!-- ====================================================================
             SECCIÓN PRINCIPAL CON TARJETAS DE FUNCIONALIDADES
             ==================================================================== -->
        <div class="album py-4 bg-light mb-5">
            <div class="container">
                <!-- Botón para crear copia de seguridad (ubicado en la parte superior) -->
                <div class="text-right mb-3">
                    <a href="index.php?controller=BackupController&action=generar" 
                       class="btn btn-success">
                        Crear Copia de Seguridad
                    </a>
                </div>

                <!-- Grid de tarjetas con las funcionalidades principales -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 g-4 justify-content-center">

                    <!-- Tarjeta: Crear Usuarios individuales -->
                    <div class="col mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Crear Usuarios</h5>
                                <a href="index.php?controller=UsuarioController&action=formularioCrear" 
                                   class="btn btn-primary">Ir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Crear Usuarios masivamente desde CSV -->
                    <div class="col mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Crear Usuarios por CSV</h5>
                                <a href="index.php?controller=UsuarioController&action=mostrarFormularioCSV" 
                                   class="btn btn-primary">Ir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Crear Grupos -->
                    <div class="col mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Crear Grupos</h5>
                                <a href="index.php?controller=GrupoController&action=mostrarFormulario" 
                                   class="btn btn-primary">Ir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Listar todos los usuarios -->
                    <div class="col mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Listar Usuarios</h5>
                                <a href="index.php?controller=UsuarioController&action=listar" 
                                   class="btn btn-primary">Ir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Listar todos los grupos -->
                    <div class="col mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title">Listar Grupos</h5>
                                <a href="index.php?controller=GrupoController&action=listar" 
                                   class="btn btn-primary">Ir</a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ====================================================================
                     BOTÓN PARA VER LOGS DEL SERVIDOR
                     ==================================================================== -->
                <div class="text-center mt-4">
                    <a href="index.php?controller=LogController&action=mostrarLogs" 
                       class="btn btn-warning btn-lg">
                        Ver Logs del Servidor LDAP
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- Scripts de Bootstrap para funcionalidad JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>