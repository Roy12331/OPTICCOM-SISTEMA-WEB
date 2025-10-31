<?php require APP_ROOT . '/app/vistas/includes/header.php'; // Carga la cabecera ?>

<h1 class="mb-4"><?php echo htmlspecialchars($datos['titulo'] ?? 'Gestión de Ventas'); ?></h1>

<?php /* Muestra mensajes de éxito o error pasados desde el controlador */ ?>
<?php flash('venta_mensaje'); ?>
<?php flash('cliente_mensaje'); // El mensaje puede venir después de convertir y redirigir ?>
<?php flash('mensaje_error'); ?>

<?php /* Pestañas para B2C y B2B */ ?>
<ul class="nav nav-tabs" id="ventasTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="solicitudes-tab" data-bs-toggle="tab" data-bs-target="#solicitudes" type="button" role="tab" aria-controls="solicitudes" aria-selected="true">
            Solicitudes Residenciales (B2C) <span class="badge bg-primary"><?php echo isset($datos['solicitudes']) && is_array($datos['solicitudes']) ? count($datos['solicitudes']) : 0; ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="cotizaciones-tab" data-bs-toggle="tab" data-bs-target="#cotizaciones" type="button" role="tab" aria-controls="cotizaciones" aria-selected="false">
            Cotizaciones de Empresas (B2B) <span class="badge bg-primary"><?php echo isset($datos['cotizaciones']) && is_array($datos['cotizaciones']) ? count($datos['cotizaciones']) : 0; ?></span>
        </button>
    </li>
</ul>

<div class="tab-content mt-3" id="ventasTabContent">

    <?php /* Pestaña Solicitudes B2C */ ?>
    <div class="tab-pane fade show active" id="solicitudes" role="tabpanel" aria-labelledby="solicitudes-tab">
        <div class="card card-body shadow-sm">
            <h5 class="card-title">Nuevas Solicitudes de Clientes Residenciales</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Plan</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php /* Verifica si el array solicitudes existe y no está vacío */ ?>
                        <?php if (isset($datos['solicitudes']) && is_array($datos['solicitudes']) && !empty($datos['solicitudes'])): ?>
                            <?php foreach($datos['solicitudes'] as $solicitud):
                                // Validación básica para cada ítem
                                if (!is_object($solicitud)) continue;
                                $estado_sol = $solicitud->estado_solicitud ?? 'desconocido'; // Obtiene estado de forma segura
                            ?>
                            <tr>
                                <td><?php echo isset($solicitud->fecha_solicitud) ? date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud)) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars(($solicitud->nombres ?? '') . ' ' . ($solicitud->apellidos ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($solicitud->telefono ?? 'N/A'); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($solicitud->nombre_plan ?? 'N/A'); ?>
                                    <?php if (isset($solicitud->precio_mensual)): ?>
                                        (S/ <?php echo number_format($solicitud->precio_mensual, 2); ?>)
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($solicitud->direccion ?? 'N/A'); ?></td>
                                <td> <?php /* Badge de Estado */ ?>
                                    <span class="badge <?php
                                        if($estado_sol == 'pendiente') echo 'bg-warning text-dark';
                                        elseif($estado_sol == 'contactado') echo 'bg-info text-dark';
                                        elseif($estado_sol == 'con_cobertura') echo 'bg-success';
                                        elseif($estado_sol == 'sin_cobertura') echo 'bg-danger';
                                        elseif($estado_sol == 'instalado') echo 'bg-secondary'; // Estado después de convertir
                                        else echo 'bg-dark'; // Estado desconocido
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $estado_sol)); ?>
                                    </span>
                                </td>
                                <td> <?php /* Celda de Acciones */ ?>
                                    <?php if (isset($solicitud->id_solicitud)): // Verifica si existe ID ?>

                                        <?php /* Formulario para cambiar estado (sin opción "Instalado") */ ?>
                                        <form action="<?php echo RUTA_URL; ?>/ventas/cambiarEstadoSolicitud/<?php echo $solicitud->id_solicitud; ?>" method="POST" class="d-flex mb-1">
                                            <select name="estado_solicitud" class="form-select form-select-sm me-2" aria-label="Cambiar estado solicitud">
                                                <option value="pendiente" <?php echo ($estado_sol == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="contactado" <?php echo ($estado_sol == 'contactado') ? 'selected' : ''; ?>>Contactado</option>
                                                <option value="con_cobertura" <?php echo ($estado_sol == 'con_cobertura') ? 'selected' : ''; ?>>Con Cobertura</option>
                                                <option value="sin_cobertura" <?php echo ($estado_sol == 'sin_cobertura') ? 'selected' : ''; ?>>Sin Cobertura</option>
                                                <?php /* Opción "Instalado" eliminada */ ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">OK</button>
                                        </form>

                                        <?php /* Botón para convertir a cliente (solo si estado es 'con_cobertura') */ ?>
                                        <?php if ($estado_sol == 'con_cobertura'): ?>
                                            <form action="<?php echo RUTA_URL; ?>/ventas/convertir/<?php echo $solicitud->id_solicitud; ?>" method="POST" class="d-inline w-100">
                                                <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('¿Convertir esta solicitud en cliente? Se creará un registro en Clientes.');">
                                                    <i class="fas fa-user-plus me-1"></i> Convertir a Cliente
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                    <?php endif; // Fin verificación id_solicitud ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: // Si $datos['solicitudes'] está vacío o no es válido ?>
                            <tr><td colspan="7" class="text-center text-muted py-3">No hay solicitudes residenciales pendientes.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php /* Pestaña Cotizaciones B2B (No necesita cambios basados en el controlador anterior) */ ?>
    <div class="tab-pane fade" id="cotizaciones" role="tabpanel" aria-labelledby="cotizaciones-tab">
        <div class="card card-body shadow-sm">
            <h5 class="card-title">Nuevas Solicitudes de Cotización de Empresas</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Razón Social</th>
                            <th>RUC</th>
                            <th>Contacto</th>
                            <th>Teléfono / Email</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if (isset($datos['cotizaciones']) && is_array($datos['cotizaciones']) && !empty($datos['cotizaciones'])): ?>
                            <?php foreach($datos['cotizaciones'] as $cotizacion):
                                if(!is_object($cotizacion)) continue;
                                $estado_cot = $cotizacion->estado_cotizacion ?? 'desconocido';
                            ?>
                            <tr>
                                <td><?php echo isset($cotizacion->fecha_solicitud) ? date('d/m/Y H:i', strtotime($cotizacion->fecha_solicitud)) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($cotizacion->razon_social ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cotizacion->ruc ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cotizacion->persona_contacto ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cotizacion->telefono_contacto ?? ''); ?> / <?php echo htmlspecialchars($cotizacion->email_contacto ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cotizacion->direccion_proyecto ?? 'N/A'); ?></td>
                                <td> <?php /* Badge de Estado */ ?>
                                    <span class="badge <?php
                                        if($estado_cot == 'pendiente') echo 'bg-warning text-dark';
                                        elseif($estado_cot == 'contactado') echo 'bg-info text-dark';
                                        elseif($estado_cot == 'enviada') echo 'bg-primary';
                                        elseif($estado_cot == 'ganada') echo 'bg-success';
                                        elseif($estado_cot == 'perdida') echo 'bg-danger';
                                        else echo 'bg-dark';
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $estado_cot)); ?>
                                    </span>
                                </td>
                                <td> <?php /* Celda de Acciones */ ?>
                                    <?php if (isset($cotizacion->id_cotizacion)): ?>
                                        <?php /* Formulario para cambiar estado */ ?>
                                        <form action="<?php echo RUTA_URL; ?>/ventas/cambiarEstadoCotizacion/<?php echo $cotizacion->id_cotizacion; ?>" method="POST" class="d-flex">
                                            <select name="estado_cotizacion" class="form-select form-select-sm me-2" aria-label="Cambiar estado cotización">
                                                <option value="pendiente" <?php echo ($estado_cot == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                                <option value="contactado" <?php echo ($estado_cot == 'contactado') ? 'selected' : ''; ?>>Contactado</option>
                                                <option value="enviada" <?php echo ($estado_cot == 'enviada') ? 'selected' : ''; ?>>Cotización Enviada</option>
                                                <option value="ganada" <?php echo ($estado_cot == 'ganada') ? 'selected' : ''; ?>>Ganada</option>
                                                <option value="perdida" <?php echo ($estado_cot == 'perdida') ? 'selected' : ''; ?>>Perdida</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">OK</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <tr><td colspan="8" class="text-center text-muted py-3">No hay cotizaciones de empresas pendientes.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div> <?php /* Fin Tab Content */ ?>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; // Carga el pie de página ?>