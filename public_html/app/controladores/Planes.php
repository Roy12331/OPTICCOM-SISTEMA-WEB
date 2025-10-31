<?php
class Planes extends Controlador {
    private $planModelo;

    public function __construct(){
        if (!isLoggedIn()) {
            header('location: ' . RUTA_URL . '/usuarios/login');
            exit();
        }
        // Solo los administradores pueden acceder a este módulo
        if ($_SESSION['rol_usuario'] != 'admin') {
            flash('mensaje_error', 'No tienes permiso para gestionar los planes de servicio.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/dashboard');
            exit();
        }
        $this->planModelo = $this->modelo('Plan');
    }

    public function index(){
        $planes = $this->planModelo->obtenerPlanes();
        $datos = ['titulo' => 'Gestión de Planes de Servicio', 'planes' => $planes];
        $this->vista('planes/inicio', $datos);
    }

    public function agregar(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'titulo' => 'Crear Nuevo Plan',
                'nombre_plan' => trim($_POST['nombre_plan']),
                'velocidad' => trim($_POST['velocidad']),
                'precio_mensual' => trim($_POST['precio_mensual']),
                'descripcion' => trim($_POST['descripcion']),
                'nombre_error' => '', 'velocidad_error' => '', 'precio_error' => ''
            ];

            if (empty($datos['nombre_plan'])) { $datos['nombre_error'] = 'El nombre del plan es obligatorio.'; }
            if (empty($datos['velocidad'])) { $datos['velocidad_error'] = 'La velocidad es obligatoria.'; }
            if (empty($datos['precio_mensual'])) { $datos['precio_error'] = 'El precio es obligatorio.'; }
            elseif (!is_numeric($datos['precio_mensual'])) { $datos['precio_error'] = 'El precio debe ser un número.'; }

            if (empty($datos['nombre_error']) && empty($datos['velocidad_error']) && empty($datos['precio_error'])) {
                if ($this->planModelo->agregarPlan($datos)) {
                    flash('plan_mensaje', 'Plan creado exitosamente.');
                    header('location: ' . RUTA_URL . '/planes');
                    exit();
                } else { die('Algo salió mal.'); }
            } else {
                $this->vista('planes/agregar', $datos);
            }
        } else {
            $datos = [
                'titulo' => 'Crear Nuevo Plan', 'nombre_plan' => '', 'velocidad' => '', 'precio_mensual' => '', 'descripcion' => '',
                'nombre_error' => '', 'velocidad_error' => '', 'precio_error' => ''
            ];
            $this->vista('planes/agregar', $datos);
        }
    }
}
?>