<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Editar Plan de Servicio</h2>
            <p>Ajuste los campos necesarios para actualizar el plan.</p>
            <form action="<?php echo RUTA_URL; ?>/planes/editar/<?php echo $datos['id_plan']; ?>" method="POST">
                <div class="mb-3">
                    <label for="nombre_plan" class="form-label">Nombre del Plan: <sup>*</sup></label>
                    <input type="text" name="nombre_plan" class="form-control <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['nombre_plan']; ?>">
                    <div class="invalid-feedback"><?php echo $datos['nombre_error']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="velocidad" class="form-label">Velocidad: <sup>*</sup></label>
                    <input type="text" name="velocidad" class="form-control <?php echo (!empty($datos['velocidad_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['velocidad']; ?>">
                    <div class="invalid-feedback"><?php echo $datos['velocidad_error']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="precio_mensual" class="form-label">Precio Mensual (S/): <sup>*</sup></label>
                    <input type="text" name="precio_mensual" class="form-control <?php echo (!empty($datos['precio_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['precio_mensual']; ?>">
                    <div class="invalid-feedback"><?php echo $datos['precio_error']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <textarea name="descripcion" class="form-control"><?php echo $datos['descripcion']; ?></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Actualizar Plan</button>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>