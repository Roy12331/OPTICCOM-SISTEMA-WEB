<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-3 mb-5 shadow-sm">
            <h2 class="mb-3"><?php echo htmlspecialchars($datos['titulo'] ?? 'Agregar Cliente'); ?></h2>
            <p>Complete el formulario para registrar un nuevo cliente.</p>
            <?php flash('mensaje_error'); ?>

            <form action="<?php echo RUTA_URL; ?>/clientes/agregar" method="POST" novalidate>
                <h5 class="mt-4">Datos Personales</h5> <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombres: <sup>*</sup></label>
                        <input type="text" name="nombre" id="nombre" class="form-control <?php echo (!empty($datos['nombre_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['nombre_error'] ?? ''; ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="apellido" class="form-label">Apellidos: <sup>*</sup></label>
                        <input type="text" name="apellido" id="apellido" class="form-control <?php echo (!empty($datos['apellido_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['apellido'] ?? ''); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['apellido_error'] ?? ''; ?></div>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="documento_identidad" class="form-label">DNI / RUC: <sup>*</sup></label>
                        <input type="text" name="documento_identidad" id="documento_identidad" class="form-control <?php echo (!empty($datos['documento_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['documento_identidad'] ?? ''); ?>" required>
                         <div class="invalid-feedback"><?php echo $datos['documento_error'] ?? ''; ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono / Celular: <sup>*</sup></label>
                        <input type="tel" name="telefono" id="telefono" class="form-control <?php echo (!empty($datos['telefono_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['telefono_error'] ?? ''; ?></div>
                    </div>
                </div>
                 <div class="mb-3">
                    <label for="email" class="form-label">Email (Opcional):</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>">
                </div>

                <h5 class="mt-4">Datos del Servicio</h5> <hr>
                 <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección: <sup>*</sup></label>
                    <input type="text" name="direccion" id="direccion" class="form-control <?php echo (!empty($datos['direccion_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['direccion'] ?? ''); ?>" required>
                    <div class="invalid-feedback"><?php echo $datos['direccion_error'] ?? ''; ?></div>
                </div>
                <div class="mb-3">
                    <label for="referencia" class="form-label">Referencia (Opcional):</label>
                    <textarea name="referencia" id="referencia" class="form-control" rows="2"><?php echo htmlspecialchars($datos['referencia'] ?? ''); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_plan" class="form-label">Plan Contratado: <sup>*</sup></label>
                         <select name="id_plan" id="id_plan" class="form-select <?php echo (!empty($datos['plan_error'] ?? '')) ? 'is-invalid' : ''; ?>" required>
                            <option value="">-- Seleccione --</option>
                            <?php if (!empty($datos['planes']) && is_array($datos['planes'])):
                                foreach($datos['planes'] as $plan):
                                    if (is_object($plan) && isset($plan->id_plan)): ?>
                                        <option value="<?php echo $plan->id_plan; ?>" <?php echo (($datos['id_plan'] ?? '') == $plan->id_plan) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($plan->nombre_plan ?? '?'); ?>
                                            (<?php echo htmlspecialchars($plan->velocidad ?? '-'); ?> -
                                             S/ <?php echo number_format($plan->precio_mensual ?? 0, 2); ?>)
                                        </option>
                            <?php   endif;
                                endforeach;
                            else: ?>
                                <option value="" disabled>No hay planes disponibles</option>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback"><?php echo $datos['plan_error'] ?? ''; ?></div>
                    </div>
                     <div class="col-md-6 mb-3">
                         <label for="fecha_instalacion" class="form-label">Fecha Instalación: <sup>*</sup></label>
                         <input type="date" name="fecha_instalacion" id="fecha_instalacion" class="form-control <?php echo (!empty($datos['fecha_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['fecha_instalacion'] ?? date('Y-m-d')); ?>" required>
                          <div class="invalid-feedback"><?php echo $datos['fecha_error'] ?? ''; ?></div>
                    </div>
                </div>
                 <div class="mb-3">
                     <label for="estado_servicio" class="form-label">Estado Servicio:</label>
                     <select name="estado_servicio" id="estado_servicio" class="form-select">
                         <option value="Activo" <?php echo (($datos['estado_servicio'] ?? 'Activo') == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                         <option value="Suspendido" <?php echo (($datos['estado_servicio'] ?? '') == 'Suspendido') ? 'selected' : ''; ?>>Suspendido</option>
                         <option value="Cancelado" <?php echo (($datos['estado_servicio'] ?? '') == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                     </select>
                 </div>
                 <div class="mb-3">
                    <label for="detalles" class="form-label">Detalles (Opcional):</label>
                    <textarea name="detalles" id="detalles" class="form-control" rows="3"><?php echo htmlspecialchars($datos['detalles'] ?? ''); ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                     <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-secondary me-md-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>