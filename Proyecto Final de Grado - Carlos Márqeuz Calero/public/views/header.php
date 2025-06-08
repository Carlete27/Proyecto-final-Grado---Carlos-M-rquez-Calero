<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Incluye Bootstrap 5 para el diseño responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Barra de navegación superior con branding y sesión de usuario -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid justify-content-between">
        <!-- Logo/nombre de la empresa que funciona como enlace al inicio -->
        <a class="navbar-brand" href="index.php">Carlete SL</a>

        <div class="d-flex align-items-center">
            <!-- Muestra saludo personalizado y botón de logout si hay sesión activa -->
            <?php if (isset($_SESSION['usuario'])): ?>
                <span class="text-white me-3">Hola, <?= htmlspecialchars($_SESSION['usuario']) ?></span>
                <a href="index.php?controller=UsuarioController&action=logout" class="btn btn-danger">Cerrar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</nav>