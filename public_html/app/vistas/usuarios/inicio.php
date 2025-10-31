<?php require APP_ROOT . '/app/vistas/includes/header.php'; // Carga la cabecera ?>

<h1 class="mb-4"><?php echo htmlspecialchars($datos['titulo'] ?? 'Gestión de Accesos'); ?></h1>

<?php flash('usuario_mensaje'); // Mensajes de éxito o error ?>
<?php flash('mensaje_error'); ?>


<ul class="nav nav-tabs" id="accesosTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="true">
            <i class="fas fa-user-shield me-1"></i> Usuarios Administrativos
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button" role="tab" aria-controls="clientes" aria-selected="false">
            <i class="fas fa-users me-1"></i> Accesos de Clientes
        </button>
    </li>
</ul>

<div class="tab-content" id="accesosTabContent">

    <div class="tab-pane fade show active" id="admin" role="tabpanel" aria-labelledby="admin-tab">
        <div class="card card-body shadow-sm mt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                 <h5 class="card-title mb-0">Personal de la Empresa (Administradores, Asesores)</h5>
                 <a href="<?php echo RUTA_URL; ?>/usuarios/agregar" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Agregar Usuario Admin
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($datos['usuarios_admin']) && is_array($datos['usuarios_admin']) && !empty($datos['usuarios_admin'])): ?>
                            <?php foreach($datos['usuarios_admin'] as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario->nombre ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($usuario->email ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($usuario->rol ?? ''); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if (isset($usuario->id_usuario)): ?>
                                            
                                            <a href="<?php echo RUTA_URL; ?>/usuarios/editar/<?php echo $usuario->id_usuario; ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <?php // No permitir que el admin se borre a sí mismo ?>
                                            <?php if ($usuario->id_usuario != $_SESSION['id_usuario']): ?>
                                                <form action="<?php echo RUTA_URL; ?>/usuarios/borrar/<?php echo $usuario->id_usuario; ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Borrar"
                                                            onclick="return confirm('¿Estás seguro de borrar a <?php echo htmlspecialchars($usuario->nombre ?? ''); ?>? Esta acción no se puede deshacer.');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <tr><td colspan="4" class="text-center text-muted py-3">No hay usuarios administrativos.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="clientes" role="tabpanel" aria-labelledby="clientes-tab">
        <div class="card card-body shadow-sm mt-3">
            <h5 class="card-title mb-3">Accesos para Clientes (Portal Cliente)</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>DNI / RUC (Usuario)</th>
                            <th>Plan</th>
                            <th>Estado Acceso</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($datos['clientes']) && is_array($datos['clientes']) && !empty($datos['clientes'])): ?>
                            <?php foreach($datos['clientes'] as $cliente):
                                if (!is_object($cliente)) continue;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($cliente->dni ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cliente->nombre_plan ?? 'N/A'); ?></td>
                                <td>
                                    <?php // Verifica si la columna password está VACÍA (NULL) o no ?>
                                    <?php if (empty($cliente->password)): ?>
                                        <span class="badge bg-warning text-dark">Pendiente de Creación</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($cliente->id_cliente)): ?>
                                        <?php if (empty($cliente->password)): ?>
                                            <button type="button" class="btn btn-info btn-sm" title="Crear Acceso Cliente"
                                                    onclick="abrirModalAcceso(<?php echo $cliente->id_cliente; ?>, '<?php echo htmlspecialchars(addslashes(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))); ?>', 'Crear Acceso para')">
                                                <i class="fas fa-key me-1"></i> Crear
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary btn-sm" title="Resetear Contraseña Cliente"
                                                    onclick="abrirModalAcceso(<?php echo $cliente->id_cliente; ?>, '<?php echo htmlspecialchars(addslashes(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))); ?>', 'Resetear Contraseña para')">
                                                <i class="fas fa-redo-alt me-1"></i> Resetear
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <tr><td colspan="5" class="text-center text-muted py-3">No hay clientes registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div> <div class="modal fade" id="modalCrearAcceso" tabindex="-1" aria-labelledby="modalCrearAccesoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearAccesoLabel"><strong id="accionClienteModal"></strong> <span id="nombreClienteAccesoModal"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formCrearAcceso" action="" method="POST">
        <div class="modal-body">
            <p>El cliente usará su **DNI/RUC** como usuario para iniciar sesión.</p>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña: <sup class="text-danger">*</sup></label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
            </div>
            <div class="mb-3">
                <label for="confirmar_password" class="form-label">Confirmar Contraseña: <sup class="text-danger">*</sup></label>
                <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required minlength="6" placeholder="Repetir contraseña">
            </div>
            <p><small><sup class="text-danger">*</sup> Campos obligatorios.</small></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Contraseña</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function abrirModalAcceso(idCliente, nombreCliente, accionTitulo) {
    document.getElementById('accionClienteModal').innerText = accionTitulo;
    document.getElementById('nombreClienteAccesoModal').innerText = nombreCliente;
    const formAcceso = document.getElementById('formCrearAcceso');
    
    // ESTA ES LA RUTA NUEVA Y CORRECTA
    formAcceso.action = '<?php echo RUTA_URL; ?>/usuarios/crearAccesoCliente/' + idCliente;
    
    formAcceso.reset();
    const modalAccesoElement = document.getElementById('modalCrearAcceso');
    if (modalAccesoElement) {
        const modalAcceso = new bootstrap.Modal(modalAccesoElement);
        modalAcceso.show();
    }
}

// Script para activar la pestaña correcta si venimos de una redirección con #clientes
document.addEventListener('DOMContentLoaded', function() {
    if(window.location.hash) {
        var tabTrigger = document.querySelector('button[data-bs-target="' + window.location.hash + '"]');
        if (tabTrigger) {
            var tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }
});
</script>


<?php require APP_ROOT . '/app/vistas/includes/footer.php'; // Carga el pie de página ?>