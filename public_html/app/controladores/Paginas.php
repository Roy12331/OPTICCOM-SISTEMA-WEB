<?php
class Paginas extends Controlador {
    private $planModelo;
    private $solicitudModelo;
    private $cotizacionModelo;

    public function __construct(){
        try {
            $this->planModelo = $this->modelo('Plan');
            $this->solicitudModelo = $this->modelo('Solicitud');
            $this->cotizacionModelo = $this->modelo('Cotizacion');
        } catch (Throwable $e) {
            error_log("Error CRÍTICO cargando modelos en Paginas: " . $e->getMessage());
            die("Error fatal inicializando módulo Paginas. Revise logs.");
        }
    }

    // --- Página de Inicio Pública ---
    public function index(){
         $planes = [];
         try { $planes = $this->planModelo->obtenerPlanes() ?? []; } catch (Throwable $e) { /* log */ }
         $datos = ['titulo' => 'Bienvenido a OPTICCOM', 'planes' => $planes];
         $this->vista('paginas/inicio', $datos);
    }

    // --- Formulario de Solicitud B2C (¡TODO OBLIGATORIO!) ---
    public function solicitud($id_plan = 0){
        $planes = [];
        try { $planes = $this->planModelo->obtenerPlanes() ?? []; } catch (Throwable $e) { /* log */ }

        $plan_seleccionado = null;
        if ($id_plan > 0 && !empty($planes)){ /* ... buscar plan preseleccionado ... */ }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Datos del formulario y errores
            $datos = [
                'planes' => $planes,
                'plan_seleccionado' => trim($_POST['id_plan_interesado'] ?? ''),
                'nombres' => trim($_POST['nombres'] ?? ''),
                'apellidos' => trim($_POST['apellidos'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
                'email' => trim($_POST['email'] ?? ''), // Mantenemos email obligatorio
                'direccion' => trim($_POST['direccion'] ?? ''),
                'referencia' => trim($_POST['referencia'] ?? ''), // Hacemos referencia obligatoria
                // Errores
                'nombres_error' => '', 'apellidos_error' => '', 'telefono_error' => '',
                'documento_error' => '', 'email_error' => '', // Añadimos error email
                'direccion_error' => '', 'referencia_error' => '', // Añadimos error referencia
                'plan_error' => ''
            ];

            // --- VALIDACIONES (¡TODO OBLIGATORIO!) ---
            if (empty($datos['nombres'])) { $datos['nombres_error'] = 'Nombres obligatorios.'; }
            if (empty($datos['apellidos'])) { $datos['apellidos_error'] = 'Apellidos obligatorios.'; }
            if (empty($datos['telefono'])) { $datos['telefono_error'] = 'Teléfono obligatorio.'; }
             elseif (!is_numeric($datos['telefono']) || strlen($datos['telefono']) != 9) { $datos['telefono_error'] = 'Debe ser 9 dígitos.';}

            if (empty($datos['documento_identidad'])) { $datos['documento_error'] = 'DNI/RUC obligatorio.'; }
             elseif (strlen($datos['documento_identidad']) < 8) { $datos['documento_error'] = 'Mínimo 8 dígitos.'; }

            if (empty($datos['email'])) { $datos['email_error'] = 'Email obligatorio.'; }
             elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) { $datos['email_error'] = 'Formato de email inválido.'; } // Validación formato

            if (empty($datos['direccion'])) { $datos['direccion_error'] = 'Dirección obligatoria.'; }
            if (empty($datos['referencia'])) { $datos['referencia_error'] = 'Referencia obligatoria.'; } // Referencia ahora obligatoria
            if (empty($datos['plan_seleccionado'])) { $datos['plan_error'] = 'Seleccione un plan.'; }
            // --- FIN VALIDACIONES ---

            // Verificamos si hay algún error
            $sin_errores_validacion = empty($datos['nombres_error']) && empty($datos['apellidos_error'])
                                   && empty($datos['telefono_error']) && empty($datos['documento_error'])
                                   && empty($datos['email_error']) && empty($datos['direccion_error'])
                                   && empty($datos['referencia_error']) && empty($datos['plan_error']);

            if ($sin_errores_validacion) {
                // Preparamos datos para guardar (TODOS)
                $datos_guardar = [
                    'nombres' => $datos['nombres'],
                    'apellidos' => $datos['apellidos'],
                    'telefono' => $datos['telefono'],
                    'documento_identidad' => $datos['documento_identidad'],
                    'email' => $datos['email'],
                    'direccion' => $datos['direccion'],
                    'referencia' => $datos['referencia'],
                    'id_plan_interesado' => $datos['plan_seleccionado']
                ];

                $guardado_ok = false;
                try { $guardado_ok = $this->solicitudModelo->agregarSolicitud($datos_guardar); } catch (Throwable $e) { /* log */ }

                if ($guardado_ok) {
                    header('location: ' . RUTA_URL . '/paginas/exito'); exit();
                } else {
                    flash('mensaje_error', 'Error al guardar la solicitud. Intente de nuevo.', 'alert alert-danger');
                    $this->vista('paginas/solicitud', $datos);
                }
            } else {
                flash('mensaje_error', 'Por favor complete todos los campos requeridos.', 'alert alert-warning');
                $this->vista('paginas/solicitud', $datos);
            }
        } else {
            // Carga inicial (GET)
            $datos = [
                'planes' => $planes,
                'plan_seleccionado' => $plan_seleccionado,
                'nombres' => '', 'apellidos' => '', 'telefono' => '', 'documento_identidad' => '',
                'email' => '', 'direccion' => '', 'referencia' => '',
                'nombres_error' => '', 'apellidos_error' => '', 'telefono_error' => '',
                'documento_error' => '', 'email_error' => '', 'direccion_error' => '', 'referencia_error' => '',
                'plan_error' => ''
            ];
            $this->vista('paginas/solicitud', $datos);
        }
    } // Fin solicitud()

    // --- Formulario de Cotización B2B ---
    // (Asegúrate de que aquí también las validaciones sean como tú quieres)
    public function cotizacion(){
         /* ... código de cotización ... */
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             // ... VALIDACIONES B2B ...
             if ($sin_errores_validacion_b2b) {
                 /* ... guardar cotización ... */
             } else {
                  flash('mensaje_error', 'Complete todos los campos B2B requeridos.', 'alert alert-warning');
                  $this->vista('paginas/cotizacion', $datos);
             }
         } else {
             $this->vista('paginas/cotizacion', $datos);
         }
    }

    // --- Página de Éxito ---
    public function exito(){
        $this->vista('paginas/exito', ['titulo' => 'Solicitud Enviada']);
    }
}