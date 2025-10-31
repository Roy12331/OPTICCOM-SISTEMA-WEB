<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <a href="<?php echo RUTA_URL; ?>/usuarios" class="btn btn-light mb-3"><i class="fas fa-backward me-1"></i> Volver</a>
        <div class="card card-body shadow-sm">
            <h2><?php echo htmlspecialchars($datos['titulo'] ?? 'Editar Usuario Admin'); ?></h2>
            <p>Modifique los datos del usuario.</p>
            <form action="<?php echo RUTA_URL; ?>/usuarios/editar/<?php echo $datos['id_usuario']; ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre: <sup class="text-danger">*</sup></label>
                    <input type="text" class="form-control <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" 
                           id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>" required>
                    <span class="invalid-feedback"><?php echo $datos['nombre_error'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (Usuario): <sup class="text-danger">*</sup></label>
                    <input type="email" class="form-control <?php echo (!empty($datos['email_error'])) ? 'is-invalid' : ''; ?>"
                           id="email" name="email" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" required>
                    <span class="invalid-feedback"><?php echo $datos['email_error'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol: <sup class="text-danger">*</sup></label>
                    <select class="form-select <?php echo (!empty($datos['rol_error'])) ? 'is-invalid' : ''; ?>" id="rol" name="rol" required>
                        <option value="" <?php echo (empty($datos['rol'])) ? 'selected' : ''; ?>>Seleccione un rol...</option>
                        <option value="Administrador" <?php echo (($datos['rol'] ?? '') == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="Ventas" <?php echo (($datos['rol'] ?? '') == 'Ventas') ? 'selected' : ''; ?>>Ventas</option>
                        <option value="Pagos" <?php echo (($datos['rol'] ?? '') == 'Pagos') ? 'selected' : ''; ?>>Pagos</option>
                        <option value="Soporte" <?php echo (($datos['rol'] ?? '') == 'Soporte') ? 'selected' : ''; ?>>Soporte</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $datos['rol_error'] ?? ''; ?></span>
                </div>
                <hr>
                <p class="text-muted">Deje los campos de contraseña en blanco si no desea cambiarla.</p>
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña:</label>
                    <input type="password" class="form-control <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>"
                           id="password" name="password" minlength="6">
                    <span class="invalid-feedback"><?php echo $datos['password_error'] ?? ''; ?></span>
                </div>
                <div class="mb-3">
                    <label for="confirmar_password" class="form-label">Confirmar Nueva Contraseña:</label>
                    <input type="password" class="form-control <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>"
                           id="confirmar_password" name="confirmar_password" minlength="6">
                    <span class="invalid-feedback"><?php echo $datos['password_error'] ?? ''; ?></span>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>