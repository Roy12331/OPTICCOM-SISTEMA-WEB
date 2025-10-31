<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Volver al Listado</a>
            <h2 class="mb-3"><?php echo $datos['titulo']; ?></h2>
            <p>Modifique los campos que desea actualizar del cliente.</p>
            
            <form action="<?php echo RUTA_URL; ?>/clientes/editar/<?php echo $datos['id_cliente']; ?>" method="POST" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombres: <sup>*</sup></label>
                        <input type="text" name="nombre" class="form-control <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['nombre_error']; ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellidos: <sup>*</sup></label>
                        <input type="text" name="apellido" class="form-control <?php echo (!empty($datos['apellido_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['apellido']); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['apellido_error']; ?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="documento_identidad" class="form-label">Documento (DNI/RUC/Pasaporte):</label>
                        <input type="text" name="documento_identidad" class="form-control <?php echo (!empty($datos['documento_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['documento_identidad']); ?>">
                        <div class="invalid-feedback"><?php echo $datos['documento_error']; ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Celular: <sup>*</sup></label>
                        <input type="text" name="telefono" class="form-control <?php echo (!empty($datos['telefono_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['telefono']); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['telefono_error']; ?></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($datos['direccion']); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($datos['email']); ?>">
                </div>

                <div class="mb-3">
                    <label for="id_plan" class="form-label">Plan Contratado: <sup>*</sup></label>
                    <select name="id_plan" class="form-select <?php echo (!empty($datos['plan_error'])) ? 'is-invalid' : ''; ?>" required>
                        <option value="">-- Seleccione un Plan --</option>
                        <?php foreach($datos['planes'] as $plan): ?>
                            <option value="<?php echo $plan->id_plan; ?>" <?php echo ($datos['id_plan'] == $plan->id_plan) ? 'selected' : ''; ?>>
                                <?php echo $plan->nombre_plan; ?> (<?php echo $plan->velocidad; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"><?php echo $datos['plan_error']; ?></div>
                </div>

                <div class="mb-3">
                    <label for="estado_servicio" class="form-label">Estado del Servicio: <sup>*</sup></label>
                    <select name="estado_servicio" class="form-select">
                        <option value="Activo" <?php echo ($datos['estado_servicio'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="Suspendido" <?php echo ($datos['estado_servicio'] == 'Suspendido') ? 'selected' : ''; ?>>Suspendido</option>
                        <option value="Cancelado" <?php echo ($datos['estado_servicio'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fecha_instalacion" class="form-label">Fecha de Instalación:</label>
                    <input type="date" name="fecha_instalacion" class="form-control <?php echo (!empty($datos['fecha_instalacion_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['fecha_instalacion']); ?>">
                    <div class="invalid-feedback"><?php echo $datos['fecha_instalacion_error']; ?></div>
                </div>

                <div class="mb-3">
                    <label for="detalles" class="form-label">Detalles (Opcional):</label>
                    <textarea name="detalles" class="form-control" rows="3"><?php echo htmlspecialchars($datos['detalles']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Actualizar Datos del Cliente</button>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>