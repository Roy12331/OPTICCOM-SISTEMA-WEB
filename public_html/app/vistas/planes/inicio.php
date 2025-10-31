<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>
<div class="container-fluid">
    <?php flash('plan_mensaje'); ?>
    <div class="row mb-3">
        <div class="col-md-6"><h1><?php echo $datos['titulo']; ?></h1></div>
        <div class="col-md-6 text-end"><a href="<?php echo RUTA_URL; ?>/planes/agregar" class="btn btn-primary"><i class="fas fa-plus"></i> Crear Nuevo Plan</a></div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead><tr><th>Nombre del Plan</th><th>Velocidad</th><th>Precio Mensual</th><th>Descripción</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach($datos['planes'] as $plan): ?>
                <tr>
                    <td><?php echo htmlspecialchars($plan->nombre_plan); ?></td>
                    <td><?php echo htmlspecialchars($plan->velocidad); ?></td>
                    <td>S/ <?php echo number_format($plan->precio_mensual, 2); ?></td>
                    <td><?php echo htmlspecialchars($plan->descripcion); ?></td>
                    <td>
                        <a href="<?php echo RUTA_URL; ?>/planes/editar/<?php echo $plan->id_plan; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>