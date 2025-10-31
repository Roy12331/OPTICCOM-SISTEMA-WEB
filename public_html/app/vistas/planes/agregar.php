<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>
<div class="row"><div class="col-md-6 mx-auto"><div class="card card-body bg-light mt-5">
    <a href="<?php echo RUTA_URL; ?>/planes" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Volver</a>
    <h2 class="mb-3"><?php echo $datos['titulo']; ?></h2>
    <p>Complete los datos para crear un nuevo plan de servicio.</p>
    <form action="<?php echo RUTA_URL; ?>/planes/agregar" method="POST">
        <div class="mb-3">
            <label for="nombre_plan" class="form-label">Nombre del Plan: <sup>*</sup></label>
            <input type="text" name="nombre_plan" class="form-control <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombre_plan']); ?>" required>
            <div class="invalid-feedback"><?php echo $datos['nombre_error']; ?></div>
        </div>
        <div class="mb-3">
            <label for="velocidad" class="form-label">Velocidad (Ej: 100 Mbps): <sup>*</sup></label>
            <input type="text" name="velocidad" class="form-control <?php echo (!empty($datos['velocidad_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['velocidad']); ?>" required>
            <div class="invalid-feedback"><?php echo $datos['velocidad_error']; ?></div>
        </div>
        <div class="mb-3">
            <label for="precio_mensual" class="form-label">Precio Mensual (S/): <sup>*</sup></label>
            <input type="text" name="precio_mensual" class="form-control <?php echo (!empty($datos['precio_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['precio_mensual']); ?>" required>
            <div class="invalid-feedback"><?php echo $datos['precio_error']; ?></div>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción (Opcional):</label>
            <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($datos['descripcion']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">Crear Plan</button>
    </form>
</div></div></div>
<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>