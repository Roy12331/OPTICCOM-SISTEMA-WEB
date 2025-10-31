<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Iniciar Sesión</h2>
            <p>Por favor, ingrese sus credenciales para acceder.</p>
            <form action="<?php echo RUTA_URL; ?>/usuarios/login" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($datos['email_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['email']; ?>">
                    <div class="invalid-feedback"><?php echo $datos['email_error']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña: <sup>*</sup></label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['password']; ?>">
                    <div class="invalid-feedback"><?php echo $datos['password_error']; ?></div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>