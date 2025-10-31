<?php require APP_ROOT . '/app/vistas/includes/header.php'; // Asumiendo que tienes un header público ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Solicitud de Servicio de Internet Residencial</h2>
                </div>
                <div class="card-body">
                    <p class="card-text">Por favor, complete todos los campos para verificar cobertura y registrar su solicitud.</p>
                    <?php flash('mensaje_error'); ?>

                    <form action="<?php echo RUTA_URL; ?>/paginas/solicitud" method="POST" novalidate>

                        <h5 class="mt-4">Datos Personales</h5><hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label">Nombres: <sup>*</sup></label>
                                <input type="text" name="nombres" id="nombres" class="form-control <?php echo (!empty($datos['nombres_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombres'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['nombres_error'] ?? ''; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label">Apellidos: <sup>*</sup></label>
                                <input type="text" name="apellidos" id="apellidos" class="form-control <?php echo (!empty($datos['apellidos_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['apellidos'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['apellidos_error'] ?? ''; ?></div>
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
                            <label for="email" class="form-label">Email: <sup>*</sup></label>
                            <input type="email" name="email" id="email" class="form-control <?php echo (!empty($datos['email_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" required>
                             <div class="invalid-feedback"><?php echo $datos['email_error'] ?? ''; ?></div>
                        </div>

                        <h5 class="mt-4">Datos de Instalación</h5><hr>
                         <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección Exacta: <sup>*</sup></label>
                            <input type="text" name="direccion" id="direccion" class="form-control <?php echo (!empty($datos['direccion_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['direccion'] ?? ''); ?>" required>
                            <div class="invalid-feedback"><?php echo $datos['direccion_error'] ?? ''; ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="referencia" class="form-label">Referencia (Ej: Cerca al parque, casa color azul): <sup>*</sup></label>
                            <textarea name="referencia" id="referencia" class="form-control <?php echo (!empty($datos['referencia_error'] ?? '')) ? 'is-invalid' : ''; ?>" rows="2" required><?php echo htmlspecialchars($datos['referencia'] ?? ''); ?></textarea>
                             <div class="invalid-feedback"><?php echo $datos['referencia_error'] ?? ''; ?></div>
                        </div>
                        <div class="mb-3">
                             <label for="id_plan_interesado" class="form-label">Plan de Interés: <sup>*</sup></label>
                             <select name="id_plan_interesado" id="id_plan_interesado" class="form-select <?php echo (!empty($datos['plan_error'] ?? '')) ? 'is-invalid' : ''; ?>" required>
                                <option value="">-- Seleccione un Plan --</option>
                                <?php if (!empty($datos['planes']) && is_array($datos['planes'])):
                                    foreach($datos['planes'] as $plan):
                                        if (is_object($plan) && isset($plan->id_plan)): ?>
                                            <option value="<?php echo $plan->id_plan; ?>" <?php echo (($datos['plan_seleccionado'] ?? '') == $plan->id_plan) ? 'selected' : ''; ?>>
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

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Enviar Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; // Asumiendo que tienes un footer público ?>