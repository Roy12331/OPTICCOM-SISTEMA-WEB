<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container-fluid bg-dark text-white text-center p-5">
    <div class="container">
        <h1 class="display-4">Internet de Fibra Óptica para tu Hogar y Negocio</h1>
        <p class="lead">Conexión estable y de alta velocidad con el mejor soporte técnico de la región.</p>
        <a href="#planes" class="btn btn-primary btn-lg mt-3">Ver Planes Residenciales</a>
    </div>
</div>

<div class="container mt-5" id="planes">
    <h2 class="text-center mb-4">Planes Residenciales</h2>
    <div class="row">
        
        <?php if (empty($datos['planes'])): ?>
            <div class="col-12">
                <p class="text-center text-muted">No hay planes de servicio residenciales disponibles en este momento.</p>
            </div>
        <?php else: ?>
            <?php foreach($datos['planes'] as $plan): ?>
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100 shadow-sm <?php echo ($plan->id_plan == 2) ? 'border-primary shadow-lg' : ''; // Destaca un plan si quieres ?>">
                    <div class="card-header <?php echo ($plan->id_plan == 2) ? 'bg-primary text-white' : 'bg-dark text-white'; ?>">
                        <h4><?php echo htmlspecialchars($plan->nombre_plan); ?></h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($plan->velocidad); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($plan->descripcion); ?></p>
                        <h3 class="card-text">S/ <?php echo number_format($plan->precio_mensual, 2); ?> <small class="text-muted">/ mes</small></h3>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="<?php echo RUTA_URL; ?>/paginas/solicitud/<?php echo $plan->id_plan; ?>" class="btn btn-primary w-100">¡Lo Quiero!</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<div class="container-fluid bg-light mt-5 p-5">
    <div class="container text-center">
        <h2 class="text-dark">¿Eres una Empresa o Entidad Pública?</h2>
        <p class="lead text-muted">Contamos con soluciones corporativas, internet dedicado y planes simétricos diseñados para la alta demanda de tu negocio.</p>
        <a href="<?php echo RUTA_URL; ?>/paginas/cotizacion" class="btn btn-outline-dark btn-lg">
            <i class="fas fa-briefcase"></i> Solicitar Cotización
        </a>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>