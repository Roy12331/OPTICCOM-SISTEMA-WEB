<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container">
    <h2>Crear una Cuenta</h2>
    <p>Por favor, llena este formulario para registrarte.</p>
    
    <form action="<?php echo RUTA_URL; ?>/usuarios/registrar" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre: <sup>*</sup></label>
            <input type="text" name="nombre" value="<?php echo $datos['nombre']; ?>">
            <span class="error-text"><?php echo $datos['nombre_error']; ?></span>
        </div>
        <div class="form-group">
            <label for="email">Email: <sup>*</sup></label>
            <input type="email" name="email" value="<?php echo $datos['email']; ?>">
            <span class="error-text"><?php echo $datos['email_error']; ?></span>
        </div>
        <div class="form-group">
            <label for="password">Contraseña: <sup>*</sup></label>
            <input type="password" name="password" value="<?php echo $datos['password']; ?>">
            <span class="error-text"><?php echo $datos['password_error']; ?></span>
        </div>
        <div class="form-group">
            <label for="confirmar_password">Confirmar Contraseña: <sup>*</sup></label>
            <input type="password" name="confirmar_password" value="<?php echo $datos['confirmar_password']; ?>">
            <span class="error-text"><?php echo $datos['confirmar_password_error']; ?></span>
        </div>
        
        <input type="submit" value="Registrar" class="btn-submit">
        <a href="<?php echo RUTA_URL; ?>/usuarios/login">¿Ya tienes una cuenta? Inicia Sesión</a>
    </form>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>