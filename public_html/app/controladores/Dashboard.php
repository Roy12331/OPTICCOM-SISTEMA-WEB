<?php
class Dashboard extends Controlador {
    private $clienteModelo;
    private $planModelo;

    public function __construct(){
        if (!isLoggedIn()) {
            header('location: '. RUTA_URL . '/usuarios/login');
            exit();
        }
        // Carga los modelos que necesita para obtener los datos
        $this->clienteModelo = $this->modelo('Cliente');
        $this->planModelo = $this->modelo('Plan');
    }

    public function index(){
        // --- ESTAS LLAMADAS ERAN LAS QUE ESTABAN CAUSANDO EL ERROR ---
        $totalClientes = $this->clienteModelo->contarClientes();
        $totalPlanes = $this->planModelo->contarPlanes();
        $totalActivos = $this->clienteModelo->contarClientesPorEstado('Activo');
        $totalSuspendidos = $this->clienteModelo->contarClientesPorEstado('Suspendido');
        
        // Obtener la lista de los últimos 5 clientes
        $ultimosClientes = $this->clienteModelo->obtenerUltimosClientes(5);
        // --- FIN DE LAS LLAMADAS ---

        // Prepara los datos para enviar a la vista
        $datos = [
            'titulo' => 'Dashboard',
            'total_clientes' => $totalClientes,
            'total_planes' => $totalPlanes,
            'total_activos' => $totalActivos,
            'total_suspendidos' => $totalSuspendidos,
            'ultimos_clientes' => $ultimosClientes
        ];
        
        // Carga la vista del dashboard con todos los datos
        $this->vista('dashboard/index', $datos);
    }
}
?>