<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="mb-4">Dashboard Principal</h1>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $datos['total_clientes']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Clientes Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $datos['total_activos']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Clientes Suspendidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $datos['total_suspendidos']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Planes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $datos['total_planes']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wifi fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <h3>Acciones Rápidas</h3>
            <hr>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-grid gap-2">
                <a href="<?php echo RUTA_URL; ?>/clientes/agregar" class="btn btn-primary btn-lg p-4">
                    <i class="fas fa-user-plus fa-2x"></i><br>Agregar Nuevo Cliente
                </a>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-grid gap-2">
                <a href="<?php echo RUTA_URL; ?>/planes/agregar" class="btn btn-info btn-lg p-4 text-white">
                    <i class="fas fa-signal fa-2x"></i><br>Crear Nuevo Plan
                </a>
            </div>
        </div>
        <?php if ($_SESSION['rol_usuario'] == 'admin'): // Solo los administradores ven este botón ?>
        <div class="col-md-4 mb-3">
            <div class="d-grid gap-2">
                <a href="<?php echo RUTA_URL; ?>/usuarios/agregar" class="btn btn-secondary btn-lg p-4">
                    <i class="fas fa-users-cog fa-2x"></i><br>Gestionar Usuarios
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>