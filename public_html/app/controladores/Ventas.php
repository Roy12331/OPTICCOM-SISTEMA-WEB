<?php
class Ventas extends Controlador {
    private $solicitudModelo;
    private $cotizacionModelo;
    private $clienteModelo;
    private $planModelo;

    public function __construct(){
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('location: ' . RUTA_URL . '/usuarios/login');
            exit();
        }
        try {
            $this->solicitudModelo = $this->modelo('Solicitud');
            $this->cotizacionModelo = $this->modelo('Cotizacion');
            $this->clienteModelo = $this->modelo('Cliente'); // Modelo Cliente limpio
            $this->planModelo = $this->modelo('Plan');
        } catch (Throwable $e) {
            error_log("Error CRÍTICO al cargar modelos en Ventas: " . $e->getMessage());
            die("Error fatal inicializando módulo ventas. Revise logs.");
        }
    }

    public function index(){
        $solicitudes = [];
        $cotizaciones = [];
        try {
            // Usa la función que sí existe en Solicitud.php
            $solicitudes = $this->solicitudModelo->obtenerSolicitudes() ?? [];
        } catch (Throwable $e) {
            error_log("Error obteniendo solicitudes: ".$e->getMessage());
            flash('mensaje_error', 'Error al cargar solicitudes.', 'alert alert-danger');
            $solicitudes = [];
        }
        try {
            $cotizaciones = $this->cotizacionModelo->obtenerCotizaciones() ?? [];
        } catch (Throwable $e) {
             error_log("Error obteniendo cotizaciones: ".$e->getMessage());
             flash('mensaje_error', 'Error al cargar cotizaciones.', 'alert alert-danger');
             $cotizaciones = [];
        }
        $datos = [
            'titulo' => 'Gestión de Ventas',
            'solicitudes' => $solicitudes,
            'cotizaciones' => $cotizaciones
        ];
        $this->vista('ventas/index', $datos);
    }

    public function cambiarEstadoSolicitud($id_solicitud){
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado_solicitud'])) {
            $nuevo_estado = filter_var($_POST['estado_solicitud'], FILTER_SANITIZE_STRING);
            $estados_validos = ['pendiente', 'contactado', 'con_cobertura', 'sin_cobertura'];
            if (in_array($nuevo_estado, $estados_validos)) {
                $actualizado = false;
                try { $actualizado = $this->solicitudModelo->actualizarEstado($id_solicitud, $nuevo_estado); } catch (Throwable $e) { /*...*/ }
                if ($actualizado) { flash('venta_mensaje', 'Estado actualizado.'); } else { /*...*/ }
            } else { /*...*/ }
        } else { /*...*/ }
        header('location: ' . RUTA_URL . '/ventas');
        exit();
    }

    public function cambiarEstadoCotizacion($id_cotizacion){
         if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado_cotizacion'])) {
             $nuevo_estado = filter_var($_POST['estado_cotizacion'], FILTER_SANITIZE_STRING);
             $estados_validos = ['pendiente', 'contactado', 'enviada', 'ganada', 'perdida'];
             if (in_array($nuevo_estado, $estados_validos)) {
                 $actualizado = false;
                 try { $actualizado = $this->cotizacionModelo->actualizarEstado($id_cotizacion, $nuevo_estado); } catch (Throwable $e) { /*...*/ }
                 if ($actualizado) { flash('venta_mensaje', 'Estado actualizado.'); } else { /*...*/ }
             } else { /*...*/ }
         } else { /*...*/ }
         header('location: ' . RUTA_URL . '/ventas');
         exit();
    }

    /**
     * Convierte solicitud a cliente. Muestra mensaje genérico si falla agregarCliente.
     * Redirige a /clientes en éxito.
     */
    public function convertir($id_solicitud){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $solicitud = false;
            try {
                // Asegúrate que Solicitud.php tenga obtenerSolicitudPorId()
                $solicitud = $this->solicitudModelo->obtenerSolicitudPorId($id_solicitud);
            } catch (Throwable $e) {
                 error_log("Error obteniendo solicitud $id_solicitud para convertir: ".$e->getMessage());
                 flash('mensaje_error', 'Error al buscar la solicitud.', 'alert alert-danger');
                 header('location: ' . RUTA_URL . '/ventas');
                 exit();
             }

            // Verifica si existe y está 'con_cobertura'
            if(!$solicitud || !isset($solicitud->estado_solicitud) || $solicitud->estado_solicitud != 'con_cobertura'){
                 flash('mensaje_error', 'Solicitud no encontrada o no en estado "Con Cobertura".', 'alert alert-warning');
                 header('location: ' . RUTA_URL . '/ventas');
                 exit();
            }

            // (Opcional) Obtener precio del plan
            $precio_plan = null;
            if (isset($solicitud->id_plan_interesado)) { /* ... obtener precio ... */ }

            // Prepara datos para agregarCliente
            $datos_cliente = [
                'nombre' => $solicitud->nombres ?? '',
                'apellido' => $solicitud->apellidos ?? '',
                'telefono' => $solicitud->telefono ?? '',
                'documento_identidad' => $solicitud->documento_identidad ?? '',
                'email' => $solicitud->email ?? null,
                'direccion' => $solicitud->direccion ?? '',
                // 'referencia' => $solicitud->referencia ?? '', // Si aplica
                'id_plan' => $solicitud->id_plan_interesado ?? null,
                // 'precio_plan' => $precio_plan, // Si aplica
                'fecha_instalacion' => date('Y-m-d'),
                'estado_servicio' => 'Activo',
                'detalles' => 'Cliente convertido desde solicitud #' . $id_solicitud
            ];

             // Validar datos mínimos (DNI, Plan)
            if (empty($datos_cliente['documento_identidad'])) {
                 flash('mensaje_error', 'La solicitud no tiene DNI/RUC. No se puede convertir.', 'alert alert-danger');
                 header('location: ' . RUTA_URL . '/ventas');
                 exit();
            }
             if (empty($datos_cliente['id_plan'])) {
                 flash('mensaje_error', 'La solicitud no tiene un plan asociado. No se puede convertir.', 'alert alert-danger');
                 header('location: ' . RUTA_URL . '/ventas');
                 exit();
            }

            $convertido_ok = false; // Almacena true o false
            try {
                // Llama a agregarCliente (que ahora devuelve true o false)
                $convertido_ok = $this->clienteModelo->agregarCliente($datos_cliente);
            } catch (Throwable $e) {
                // Error MUY grave si la llamada al método falla
                error_log("Error EXCEPCIÓN al llamar agregarCliente: " . $e->getMessage());
                flash('mensaje_error', 'Error fatal en el proceso de conversión. Contacte soporte.', 'alert alert-danger');
                 header('location: ' . RUTA_URL . '/ventas');
                 exit();
            }

            // --- LÓGICA ESTÁNDAR RESTAURADA ---
            // Si agregarCliente devolvió true (éxito)
            if($convertido_ok){
                // Actualizar estado solicitud a 'instalado'
                $estado_actualizado = false;
                try {
                    $estado_actualizado = $this->solicitudModelo->actualizarEstado($id_solicitud, 'instalado');
                } catch (Throwable $e) { /* ... log error no crítico ... */ }

                flash('cliente_mensaje', '¡Solicitud #' . $id_solicitud . ' convertida a Cliente!');
                if (!$estado_actualizado) { flash('mensaje_error', 'Nota: No se pudo actualizar estado solicitud original.', 'alert alert-warning'); }

                header('location: ' . RUTA_URL . '/clientes'); // Redirige a CLIENTES
                exit();
            } else {
                // Si agregarCliente devolvió false (fallo)
                // Muestra el mensaje GENÉRICO (ocultando detalles técnicos al usuario)
                if (!isset($_SESSION['mensaje_flash'])) { // Evita sobreescribir error fatal previo
                   // Mensaje más general para el usuario
                   flash('mensaje_error', 'No se pudo convertir la solicitud (¿DNI/RUC ya existe como cliente?). Verifique los logs del servidor para detalles.', 'alert alert-danger');
                }
                header('location: ' . RUTA_URL . '/ventas'); // Vuelve a ventas
                exit();
            }
             // --- FIN LÓGICA RESTAURADA ---

        } else {
             flash('mensaje_error', 'Acción no permitida.', 'alert alert-warning');
            header('location: ' . RUTA_URL . '/ventas');
            exit();
        }
    } // Fin convertir()

} // Fin Controlador Ventas