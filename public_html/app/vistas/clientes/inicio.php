<?php require APP_ROOT . '/app/vistas/includes/header.php'; // Carga la cabecera ?>

<div class="row mb-3 align-items-center">
    <div class="col-md-6">
        <h1><?php echo htmlspecialchars($datos['titulo'] ?? 'Gestión de Clientes'); ?></h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo RUTA_URL; ?>/clientes/agregar" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Agregar Cliente
        </a>
    </div>
</div>

<?php flash('cliente_mensaje'); // Muestra mensajes de éxito (verde) ?>
<?php flash('mensaje_error'); // Muestra mensajes de error (rojo) ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Lista de Clientes Registrados</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Cód. Cliente (DNI)</th>
                        <th>Nombre Completo</th>
                        <th>Plan Contratado</th>
                        <th>Estado Servicio</th>
                        <th>Estado Pago</th> <?php /* <-- NUEVA COLUMNA */ ?>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Verifica si $datos['clientes'] es un array válido antes de iterar
                    if (isset($datos['clientes']) && is_array($datos['clientes']) && !empty($datos['clientes'])):
                        $contador = 1; // Inicializa contador
                        foreach($datos['clientes'] as $cliente):
                            // Asegura que $cliente sea un objeto antes de acceder a sus propiedades
                            if (!is_object($cliente)) continue;
                    ?>
                    <tr>
                        <td><?php echo $contador++; ?></td>
                        <td><?php echo htmlspecialchars($cliente->dni ?? 'N/A'); // Muestra DNI (tu nombre de columna) ?></td>
                        <td><?php echo htmlspecialchars(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')); ?></td>
                        <td>
                            <?php echo htmlspecialchars($cliente->nombre_plan ?? 'Sin Plan'); ?>
                            <?php
                                // Intenta obtener el precio del plan si no viene con el cliente
                                // Esto asume que el controlador $datos['planes'] también está disponible
                                // o que el modelo obtenerClientes ya trae el precio_mensual del plan
                                // Si tu modelo obtenerClientes ya trae $cliente->precio_mensual, úsalo directamente.
                                // Si no, necesitaríamos pasar $datos['planes'] a esta vista o ajustar el JOIN.
                                // Ejemplo si ya viene en $cliente:
                                // if (isset($cliente->precio_mensual)) {
                                //    echo ' (S/ ' . number_format($cliente->precio_mensual, 2) . ')';
                                // }
                            ?>
                        </td>
                        <td>
                            <?php /* Muestra badge para Estado Servicio */ ?>
                            <?php $estado_servicio = $cliente->estado_servicio ?? 'Desconocido'; ?>
                            <span class="badge fs-6 <?php
                                if ($estado_servicio == 'Activo') echo 'bg-success';
                                elseif ($estado_servicio == 'Suspendido') echo 'bg-warning text-dark';
                                elseif ($estado_servicio == 'Cancelado') echo 'bg-danger';
                                else echo 'bg-secondary';
                            ?>"><?php echo htmlspecialchars($estado_servicio); ?></span>
                        </td>
                        <td>
                            <?php /* <-- NUEVO: Muestra badge para Estado Pago */ ?>
                            <?php $estado_pago = $cliente->estado_pago ?? 'N/A'; // Obtiene el estado_pago ?>
                            <span class="badge fs-6 <?php
                                if ($estado_pago == 'Al día') echo 'bg-success';
                                elseif ($estado_pago == 'Pendiente') echo 'bg-warning text-dark';
                                elseif ($estado_pago == 'Vencido') echo 'bg-danger';
                                else echo 'bg-secondary'; // Para 'N/A' u otros casos
                            ?>"><?php echo htmlspecialchars($estado_pago); ?></span>
                        </td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Acciones Cliente">
                                <?php if (isset($cliente->id_cliente)): // Asegura que el ID exista ?>
                                    
                                    <?php /* Botón Editar (como antes) */ ?>
                                    <a href="<?php echo RUTA_URL; ?>/clientes/editar/<?php echo $cliente->id_cliente; ?>"
                                       class="btn btn-warning btn-sm" title="Editar Cliente">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <?php /* <-- NUEVO: Botón Registrar Pago (abre modal) */ ?>
                                    <button type="button" class="btn btn-success btn-sm" title="Registrar Pago"
                                            onclick="abrirModalPago(<?php echo $cliente->id_cliente; ?>, '<?php echo htmlspecialchars(addslashes(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))); ?>')">
                                        <i class="fas fa-dollar-sign"></i> <?php /* Icono Dólar */ ?>
                                    </button>

                                    <?php /* <-- NUEVO: Botón Ver Historial */ ?>
                                    <a href="<?php echo RUTA_URL; ?>/clientes/historialPagos/<?php echo $cliente->id_cliente; ?>"
                                       class="btn btn-info btn-sm" title="Ver Historial de Pagos">
                                        <i class="fas fa-history"></i> <?php /* Icono Historial */ ?>
                                    </a>

                                    <?php /* <-- NUEVO (Opcional): Botones Marcar Pendiente/Vencido */ ?>
                                    <?php // Solo muestra marcar Pendiente si está Al día ?>
                                    <?php if ($estado_pago == 'Al día'): ?>
                                    <form action="<?php echo RUTA_URL; ?>/clientes/marcarEstadoPago/<?php echo $cliente->id_cliente; ?>/Pendiente" method="POST" class="d-inline" onsubmit="return confirm('¿Marcar como Pendiente?')">
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Marcar como Pendiente">
                                             <i class="fas fa-exclamation-triangle"></i> <?php /* Icono Advertencia */ ?>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <?php // Solo muestra marcar Vencido si está Al día o Pendiente ?>
                                     <?php if ($estado_pago == 'Al día' || $estado_pago == 'Pendiente'): ?>
                                    <form action="<?php echo RUTA_URL; ?>/clientes/marcarEstadoPago/<?php echo $cliente->id_cliente; ?>/Vencido" method="POST" class="d-inline" onsubmit="return confirm('¿Marcar como VENCIDO?')">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Marcar como Vencido">
                                             <i class="fas fa-times-circle"></i> <?php /* Icono Error */ ?>
                                        </button>
                                    </form>
                                     <?php endif; ?>

                                <?php endif; // Fin if isset id_cliente ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                        endforeach; // Fin foreach
                    elseif ($datos['clientes'] === false): // Si hubo error al cargar
                    ?>
                        <tr><td colspan="7" class="text-center text-danger py-4"><strong>Error al cargar la lista de clientes.</strong><br>Revise los logs del servidor para más detalles.</td></tr>
                    <?php
                    else: // Si el array está vacío (no hay clientes)
                    ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay clientes registrados todavía. <a href="<?php echo RUTA_URL; ?>/clientes/agregar">Agregue el primero</a>.</td></tr>
                    <?php
                    endif; // Fin if/elseif/else
                    ?>
                </tbody>
            </table>
        </div></div></div><div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-labelledby="modalRegistrarPagoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalRegistrarPagoLabel">Registrar Nuevo Pago para: <strong id="nombreClienteModal"></strong></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <?php /* El 'action' se llenará dinámicamente con JavaScript */ ?>
      <form id="formRegistrarPago" action="" method="POST">
        <div class="modal-body">
            <?php /* Campo oculto para pasar el ID (aunque ya va en la URL, puede ser útil) */ ?>
            <input type="hidden" name="id_cliente_modal" id="id_cliente_modal">

            <div class="mb-3">
                <label for="fecha_pago" class="form-label">Fecha del Pago: <sup class="text-danger">*</sup></label>
                <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" required>
                 <div class="form-text">Fecha en que el cliente realizó el pago.</div>
            </div>
            <div class="mb-3">
                <label for="monto_pagado" class="form-label">Monto Pagado (S/): <sup class="text-danger">*</sup></label>
                <input type="number" step="0.01" min="0.01" class="form-control" id="monto_pagado" name="monto_pagado" required placeholder="Ej: 60.00">
                 <div class="form-text">Ingrese el monto exacto pagado. Use punto (.) para decimales.</div>
            </div>
            <div class="mb-3">
                <label for="mes_correspondiente" class="form-label">Mes/Periodo Correspondiente: <sup class="text-danger">*</sup></label>
                <input type="text" class="form-control" id="mes_correspondiente" name="mes_correspondiente" required placeholder="Ej: Octubre 2025, Adelanto Nov, Instalación">
                <div class="form-text">Identifique claramente a qué mes o concepto corresponde el pago.</div>
            </div>
            <div class="mb-3">
                <label for="metodo_pago" class="form-label">Método de Pago (Opcional):</label>
                <input type="text" class="form-control" id="metodo_pago" name="metodo_pago" placeholder="Ej: Yape, Plin, Efectivo, Banco BCP">
                <div class="form-text">Puede especificar cómo se realizó el pago.</div>
            </div>
            <p><small><sup class="text-danger">*</sup> Campos obligatorios.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Pago</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php /* Es mejor poner este script al final, justo antes de cerrar el </body> o en footer.php */ ?>
<script>
function abrirModalPago(idCliente, nombreCliente) {
    // Llenar datos del modal con la información del cliente
    // document.getElementById('id_cliente_modal').value = idCliente; // Opcional
    document.getElementById('nombreClienteModal').innerText = nombreCliente;

    // Poner la URL correcta en la propiedad 'action' del formulario del modal
    // Apunta a la función 'registrarPago' del controlador 'Clientes', pasando el ID del cliente
    const form = document.getElementById('formRegistrarPago');
    form.action = '<?php echo RUTA_URL; ?>/clientes/registrarPago/' + idCliente;

    // Limpiar campos del formulario por si se usó antes (opcional)
    form.reset();
    document.getElementById('fecha_pago').value = new Date().toISOString().split('T')[0]; // Pone fecha actual

    // Obtener la instancia del modal de Bootstrap y mostrarlo
    const modalPagoElement = document.getElementById('modalRegistrarPago');
    if (modalPagoElement) {
        const modalPago = new bootstrap.Modal(modalPagoElement);
        modalPago.show();
    } else {
        console.error("El elemento del modal #modalRegistrarPago no fue encontrado.");
        alert("Error al intentar abrir el formulario de pago.");
    }
}
</script>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; // Carga el pie de página ?>