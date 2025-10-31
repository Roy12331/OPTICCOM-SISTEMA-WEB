<?php require APP_ROOT . '/app/vistas/includes/header.php'; // Carga la cabecera del panel de administración ?>

<div class="row mb-3 align-items-center">
    <div class="col-md-9">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo RUTA_URL; ?>/clientes">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($datos['titulo'] ?? 'Historial de Pagos'); ?></li>
            </ol>
        </nav>
        <h1 class="mb-0"><?php echo htmlspecialchars($datos['titulo'] ?? 'Historial de Pagos'); ?></h1>
        <?php if (isset($datos['cliente']) && is_object($datos['cliente'])): ?>
            <p class="lead text-muted">Cliente: <?php echo htmlspecialchars($datos['cliente']->nombre ?? ''); ?> <?php echo htmlspecialchars($datos['cliente']->apellido ?? ''); ?> (DNI/RUC: <?php echo htmlspecialchars($datos['cliente']->dni ?? 'N/A'); ?>)</p>
        <?php endif; ?>
    </div>
    <div class="col-md-3 text-end">
        <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a Clientes
        </a>
    </div>
</div>

<?php flash('pago_mensaje'); ?>
<?php flash('mensaje_error'); ?>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Registros de Pagos</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Fecha de Pago</th>
                        <th>Monto Pagado (S/)</th>
                        <th>Mes Correspondiente</th>
                        <th>Método Pago</th>
                        <th>Registrado Por</th>
                        <th>Fecha Registro</th> <?php /* <-- Título actualizado */ ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($datos['pagos']) && is_array($datos['pagos']) && !empty($datos['pagos'])):
                        $contador_pagos = 1;
                        foreach ($datos['pagos'] as $pago):
                            if (!is_object($pago)) continue;
                    ?>
                    <tr>
                        <td><?php echo $contador_pagos++; ?></td>
                        <td><?php echo isset($pago->fecha_pago) ? date('d/m/Y', strtotime($pago->fecha_pago)) : 'N/A'; ?></td>
                        <td><?php echo isset($pago->monto_pagado) ? number_format($pago->monto_pagado, 2) : '0.00'; ?></td>
                        <td><?php echo htmlspecialchars($pago->mes_correspondiente ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($pago->metodo_pago ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($pago->nombre_usuario_registro ?? 'Sistema'); ?></td>
                        <td>
                            <?php /* <-- BLOQUE CORREGIDO PARA MOSTRAR HORA DE LIMA */ ?>
                            <?php
                            if (isset($pago->fecha_registro)) {
                                try {
                                    // 1. Crea objeto DateTime desde el string guardado (asumiendo UTC)
                                    $fechaUTC = new DateTime($pago->fecha_registro, new DateTimeZone('UTC'));
                                    // 2. Establece la zona horaria a la de Lima
                                    $fechaUTC->setTimezone(new DateTimeZone('America/Lima'));
                                    // 3. Formatea en la zona horaria correcta
                                    echo $fechaUTC->format('d/m/Y H:i');
                                } catch (Exception $e) {
                                    error_log("Error formateando fecha_registro: " . $e->getMessage());
                                    echo 'N/A'; // Muestra N/A si la fecha es inválida
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                        endforeach; // Fin foreach
                    else: // Si no hay pagos registrados
                    ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i> No hay pagos registrados para este cliente todavía.
                            </td>
                        </tr>
                    <?php
                    endif; // Fin if/else
                    ?>
                </tbody>
            </table>
        </div></div></div><?php require APP_ROOT . '/app/vistas/includes/footer.php'; // Carga el pie de página ?>