<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $datos['titulo']; ?> - OPTICCOM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
    <link rel="stylesheet" href="<?php echo RUTA_URL; ?>/css/style.css?v=1.3">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?php echo RUTA_URL; ?>">OPTICCOM S.A.C.</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      
      <?php if(isLoggedIn()): ?>
        
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo RUTA_URL; ?>/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo RUTA_URL; ?>/ventas"><i class="fas fa-handshake"></i> Gestión de Ventas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo RUTA_URL; ?>/clientes"><i class="fas fa-users"></i> Clientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo RUTA_URL; ?>/planes"><i class="fas fa-wifi"></i> Planes</a>
            </li>
            <?php if ($_SESSION['rol_usuario'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo RUTA_URL; ?>/usuarios"><i class="fas fa-users-cog"></i> Usuarios</a>
            </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <span class="navbar-text me-3">Hola, <?php echo $_SESSION['nombre_usuario']; ?></span>
            </li>
            <li class="nav-item">
                <a class="btn btn-outline-light" href="<?php echo RUTA_URL; ?>/usuarios/logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            </li>
        </ul>
      
      <?php else: ?>

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo RUTA_URL; ?>">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#planes">Nuestros Servicios</a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="btn btn-primary" href="<?php echo RUTA_URL; ?>/usuarios/login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
            </li>
        </ul>

      <?php endif; ?>

    </div>
  </div>
</nav>

<div class="container mt-4">
    <?php 
    flash('usuario_mensaje');
    flash('plan_mensaje');
    flash('cliente_mensaje');
    flash('venta_mensaje'); // Mensaje para el panel de ventas
    flash('mensaje_error'); 
    ?>