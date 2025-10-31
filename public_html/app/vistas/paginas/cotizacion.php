<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5 shadow-sm">
            <h2 class="mb-3"><?php echo $datos['titulo']; ?></h2>
            <p>Por favor, complete los datos de su empresa. Un asesor comercial especializado se pondrá en contacto con usted para diseñar una solución a su medida.</p>
            
            <form action="<?php echo RUTA_URL; ?>/paginas/cotizacion" method="POST" novalidate>
                <div class="mb-3">
                    <label for="razon_social" class="form-label">Razón Social: <sup>*</sup></label>
                    <input type="text" name="razon_social" class="form-control <?php echo (!empty($datos['razon_social_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['razon_social']); ?>" required>
                    <div class="invalid-feedback"><?php echo $datos['razon_social_error']; ?></div>
                </div>
                
                <div class="mb-3">
                    <label for="ruc" class="form-label">RUC: <sup>*</sup></label>
                    <input type="text" name="ruc" class="form-control <?php echo (!empty($datos['ruc_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['ruc']); ?>" required minlength="11" maxlength="11">
                    <div class="invalid-feedback"><?php echo $datos['ruc_error']; ?></div>
                </div>

                <hr class="my-4">
                <p class="text-muted">Datos de la Persona de Contacto</p>

                <div class="mb-3">
                    <label for="persona_contacto" class="form-label">Nombre del Contacto: <sup>*</sup></label>
                    <input type="text" name="persona_contacto" class="form-control <?php echo (!empty($datos['contacto_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['persona_contacto']); ?>" required>
                    <div class="invalid-feedback"><?php echo $datos['contacto_error']; ?></div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telefono_contacto" class="form-label">Teléfono: <sup>*</sup></label>
                        <input type="tel" name="telefono_contacto" class="form-control <?php echo (!empty($datos['telefono_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['telefono_contacto']); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['telefono_error']; ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email_contacto" class="form-label">Email: <sup>*</sup></label>
                        <input type="email" name="email_contacto" class="form-control <?php echo (!empty($datos['email_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['email_contacto']); ?>" required>
                        <div class="invalid-feedback"><?php echo $datos['email_error']; ?></div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label for="direccion_proyecto" class="form-label">Dirección de Instalación / Proyecto: <sup>*</sup></label>
                    <input type="text" name="direccion_proyecto" class="form-control <?php echo (!empty($datos['direccion_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['direccion_proyecto']); ?>" required>
                    <div class="invalid-feedback"><?php echo $datos['direccion_error']; ?></div>
                </div>
                
                <div class="mb-3">
                    <label for="mensaje" class="form-label">Detalles del Requerimiento (Opcional):</label>
                    <textarea name="mensaje" class="form-control" rows="3" placeholder="Ej: Requerimos internet dedicado, 10 puntos de red, servicio simétrico, etc."><?php echo htmlspecialchars($datos['mensaje']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">Solicitar Cotización Formal</button>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>