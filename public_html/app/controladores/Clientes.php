<?php
class Clientes extends Controlador {
    private $clienteModelo;
    private $planModelo;

    public function __construct(){
        // Verifica si el usuario está logueado al crear el controlador
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning'); // Mensaje opcional
            header('location: ' . RUTA_URL . '/usuarios/login');
            exit();
        }
        // Carga los modelos necesarios (Cliente ya tiene las funciones de pago)
        try {
            $this->clienteModelo = $this->modelo('Cliente');
            $this->planModelo = $this->modelo('Plan');
        } catch (Throwable $e) {
             error_log("Error CRÍTICO al cargar modelos en Clientes: " . $e->getMessage());
             // Es un error grave si los modelos no cargan, detenemos ejecución
             die("Error fatal inicializando módulo clientes. Revise logs.");
        }
    }

    /**
     * Muestra la lista principal de clientes.
     * Pasa los datos de clientes (incluyendo estado_pago) a la vista.
     */
    public function index(){
        $clientes = false; // Inicializa como false para detectar errores
        try {
            // Obtiene la lista de clientes desde el modelo
            $clientes = $this->clienteModelo->obtenerClientes();
        } catch (Throwable $e) {
             // Registra el error si falla la obtención de clientes
             error_log("Error en Clientes/index al obtener clientes: " . $e->getMessage());
             flash('mensaje_error', 'Error al cargar la lista de clientes. Intente más tarde.', 'alert alert-danger');
             $clientes = []; // Envía un array vacío para evitar errores en la vista
        }
        // Prepara los datos para la vista
        $datos = [
            'titulo' => 'Gestión de Clientes',
            'clientes' => $clientes // Pasa el array de clientes (o array vacío si hubo error)
        ];
        // Carga la vista que muestra la tabla de clientes
        $this->vista('clientes/inicio', $datos);
    }

    /**
     * Muestra el formulario para agregar un nuevo cliente (GET)
     * o procesa los datos enviados para crear un nuevo cliente (POST).
     */
    public function agregar(){
        $planes = []; // Inicializa el array de planes
        try {
            // Intenta obtener la lista de planes
            if ($this->planModelo && method_exists($this->planModelo, 'obtenerPlanes')) {
                $planes_temp = $this->planModelo->obtenerPlanes();
                $planes = is_array($planes_temp) ? $planes_temp : []; // Asegura que sea un array
            } else { throw new Exception("Modelo Plan o método obtenerPlanes no disponible."); }
        } catch (Throwable $e) {
             error_log("Error obteniendo planes en Clientes/agregar: " . $e->getMessage());
             flash('mensaje_error', 'Error al cargar lista de planes. No se puede continuar.', 'alert alert-danger');
             // Si cargar planes es crítico, redirige a la lista
             // header('location: ' . RUTA_URL . '/clientes'); exit();
        }

        // Si se envió el formulario (método POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING); // Limpia los datos POST

             // Prepara el array $datos con la información del formulario y para errores
             $datos = [
                 'planes' => $planes,
                 'titulo' => 'Agregar Nuevo Cliente',
                 'nombre' => trim($_POST['nombre'] ?? ''),
                 'apellido' => trim($_POST['apellido'] ?? ''),
                 'telefono' => trim($_POST['telefono'] ?? ''),
                 'documento_identidad' => trim($_POST['documento_identidad'] ?? ''), // Se mapeará a DNI en el modelo
                 'email' => trim($_POST['email'] ?? ''),
                 'direccion' => trim($_POST['direccion'] ?? ''),
                 // 'referencia' => trim($_POST['referencia'] ?? ''), // Si tienes esta columna
                 'id_plan' => trim($_POST['id_plan'] ?? ''), // Se mapeará a id_plan_fk en el modelo
                 'fecha_instalacion' => trim($_POST['fecha_instalacion'] ?? date('Y-m-d')),
                 'estado_servicio' => trim($_POST['estado_servicio'] ?? 'Activo'),
                 'detalles' => trim($_POST['detalles'] ?? ''),
                 // Campos para mensajes de error de validación
                 'nombre_error' => '', 'apellido_error' => '', 'telefono_error' => '',
                 'documento_error' => '', 'direccion_error' => '', 'plan_error' => '', 'fecha_error' => ''
                 // Si tienes referencia: 'referencia_error' => ''
             ];

             // --- Realiza las VALIDACIONES ---
             if (empty($datos['nombre'])) { $datos['nombre_error'] = 'Nombre obligatorio.'; }
             if (empty($datos['apellido'])) { $datos['apellido_error'] = 'Apellido obligatorio.'; }
             if (empty($datos['documento_identidad'])) { $datos['documento_error'] = 'DNI/RUC obligatorio.'; }
              elseif (strlen($datos['documento_identidad']) < 8) { $datos['documento_error'] = 'Mínimo 8 dígitos.';}
             if (empty($datos['telefono'])) { $datos['telefono_error'] = 'Teléfono obligatorio.'; }
              elseif (!is_numeric($datos['telefono']) || strlen($datos['telefono']) != 9) { $datos['telefono_error'] = 'Debe ser 9 dígitos.';}
             if (empty($datos['direccion'])) { $datos['direccion_error'] = 'Dirección obligatoria.'; }
             if (empty($datos['id_plan'])) { $datos['plan_error'] = 'Plan obligatorio.'; }
             if (empty($datos['fecha_instalacion'])) { $datos['fecha_error'] = 'Fecha obligatoria.'; }
             // if (empty($datos['referencia'])) { $datos['referencia_error'] = 'Referencia obligatoria.'; } // Si es obligatoria

             // Verifica si hubo algún error de validación
             $sin_errores_validacion = empty($datos['nombre_error']) && empty($datos['apellido_error']) && empty($datos['documento_error']) && empty($datos['telefono_error']) && empty($datos['direccion_error']) && empty($datos['plan_error']) && empty($datos['fecha_error']) /* && empty($datos['referencia_error']) */;

             // Si no hay errores de validación, intenta guardar
             if ($sin_errores_validacion) {
                 $guardado_ok = false;
                 try {
                     // Llama a agregarCliente (que ahora inserta estado_pago='Al día')
                     $guardado_ok = $this->clienteModelo->agregarCliente($datos);
                 } catch (Throwable $e) {
                     error_log("Error EXCEPCIÓN al llamar agregarCliente: " . $e->getMessage());
                     flash('mensaje_error', 'Error fatal al guardar. Contacte soporte.', 'alert alert-danger');
                 }

                 // Si se guardó correctamente, redirige a la lista
                 if ($guardado_ok) {
                     flash('cliente_mensaje', 'Cliente agregado exitosamente.');
                     header('location: ' . RUTA_URL . '/clientes');
                     exit();
                 } else {
                     // Si falló el guardado (ej: DNI duplicado), muestra mensaje si no hay uno fatal
                      if (!isset($_SESSION['mensaje_flash'])) {
                         flash('mensaje_error', 'No se pudo guardar (¿DNI/RUC duplicado?). Revise los logs.', 'alert alert-danger');
                      }
                      // Vuelve a mostrar el formulario con los datos y el error
                      $this->vista('clientes/agregar', $datos);
                 }
             } else {
                  // Si hubo errores de validación, muestra mensaje y el formulario con errores
                  flash('mensaje_error', 'Por favor corrija los errores indicados.', 'alert alert-warning');
                  $this->vista('clientes/agregar', $datos);
             }

        } else {
            // Carga inicial (método GET): Prepara datos vacíos para el formulario
            $datos = [
                'planes' => $planes, // Pasa los planes cargados (o array vacío)
                'titulo' => 'Agregar Nuevo Cliente',
                'nombre' => '', 'apellido' => '', 'telefono' => '', 'documento_identidad' => '',
                'email' => '', 'direccion' => '', 'referencia' => '', 'id_plan' => '',
                'fecha_instalacion' => date('Y-m-d'), 'estado_servicio' => 'Activo', 'detalles' => '',
                'nombre_error' => '', 'apellido_error' => '', 'telefono_error' => '', 'documento_error' => '',
                'direccion_error' => '', 'plan_error' => '', 'fecha_error' => ''
                // 'referencia_error' => ''
            ];
            // Muestra la vista del formulario
            try {
                $this->vista('clientes/agregar', $datos);
            } catch (Throwable $e) {
                 error_log("Error FATAL al cargar la VISTA clientes/agregar: " . $e->getMessage());
                 echo "<h1>Error Crítico</h1><p>No se pudo cargar el formulario. Contacte al administrador.</p>";
            }
        }
    }

    /**
     * Muestra el formulario para editar un cliente existente (GET)
     * o procesa los datos enviados para actualizarlo (POST).
     */
    public function editar($id){
        $planes = [];
        try { $planes = $this->planModelo->obtenerPlanes() ?? []; } catch (Throwable $e) { /* log */ }
        $cliente = false;
        try { $cliente = $this->clienteModelo->obtenerClientePorId($id); } catch (Throwable $e) { /* log */ }

        // Si el cliente no existe, redirige a la lista
        if($cliente === false){
             flash('mensaje_error', 'Cliente no encontrado.', 'alert alert-danger');
             header('location: ' . RUTA_URL . '/clientes');
             exit();
        }

         // Si se envió el formulario (POST)
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

             // Prepara datos con la información del formulario
             $datos = [
                 'id_cliente' => $id, // ID para el WHERE de la consulta UPDATE
                 'planes' => $planes, // Para mostrar el select si hay error
                 'titulo' => 'Editar Cliente',
                 'nombre' => trim($_POST['nombre'] ?? ''),
                 'apellido' => trim($_POST['apellido'] ?? ''),
                 'telefono' => trim($_POST['telefono'] ?? ''),
                 'documento_identidad' => trim($_POST['documento_identidad'] ?? ''), // Mapeará a DNI
                 'email' => trim($_POST['email'] ?? ''),
                 'direccion' => trim($_POST['direccion'] ?? ''),
                 // 'referencia' => trim($_POST['referencia'] ?? ''), // Si existe
                 'id_plan' => trim($_POST['id_plan'] ?? ''), // Mapeará a id_plan_fk
                 'fecha_instalacion' => trim($_POST['fecha_instalacion'] ?? date('Y-m-d')),
                 'estado_servicio' => trim($_POST['estado_servicio'] ?? 'Activo'),
                 'detalles' => trim($_POST['detalles'] ?? ''),
                 // Errores
                 'nombre_error' => '', 'apellido_error' => '', 'telefono_error' => '',
                 'documento_error' => '', 'direccion_error' => '', 'plan_error' => '', 'fecha_error' => ''
                 // 'referencia_error' => ''
             ];

             // --- Realiza las VALIDACIONES (igual que en agregar) ---
             if (empty($datos['nombre'])) { $datos['nombre_error'] = 'Nombre obligatorio.'; }
             // ... resto de validaciones ...
             if (empty($datos['fecha_instalacion'])) { $datos['fecha_error'] = 'Fecha obligatoria.'; }
             // ...

             // Verifica si hubo errores
             $sin_errores_validacion = empty($datos['nombre_error']) /* && ... etc ... */;

             // Si no hay errores, intenta actualizar
             if ($sin_errores_validacion) {
                 $actualizado_ok = false;
                 try {
                     // Llama a actualizarCliente (que NO toca estado_pago)
                     $actualizado_ok = $this->clienteModelo->actualizarCliente($datos);
                 } catch (Throwable $e) { /* log error */ }

                 if ($actualizado_ok) {
                     flash('cliente_mensaje', 'Cliente actualizado exitosamente.');
                     header('location: ' . RUTA_URL . '/clientes');
                     exit();
                 } else { /* flash error, NO redirige */ }
             } else { /* flash validación, NO redirige */ }

             // Si hubo error de validación o al guardar, muestra la vista de edición con errores
             flash('mensaje_error', 'No se pudo actualizar. Corrija los errores.', 'alert alert-danger');
             $this->vista('clientes/editar', $datos);

         } else {
             // Carga inicial (GET): Prepara datos con la info actual del cliente
             $datos = [
                 'id_cliente' => $id,
                 'planes' => $planes,
                 'titulo' => 'Editar Cliente',
                 // Usa los nombres de columna de tu BD (dni, id_plan_fk) leídos por obtenerClientePorId
                 'nombre' => $cliente->nombre ?? '',
                 'apellido' => $cliente->apellido ?? '',
                 'telefono' => $cliente->telefono ?? '',
                 'documento_identidad' => $cliente->dni ?? '', // Carga DNI en el input
                 'email' => $cliente->email ?? '',
                 'direccion' => $cliente->direccion ?? '',
                 // 'referencia' => $cliente->referencia ?? '', // Si existe
                 'id_plan' => $cliente->id_plan_fk ?? '', // Carga id_plan_fk en el select
                 'fecha_instalacion' => $cliente->fecha_instalacion ?? date('Y-m-d'),
                 'estado_servicio' => $cliente->estado_servicio ?? 'Activo',
                 'detalles' => $cliente->detalles ?? '',
                 // Errores vacíos
                 'nombre_error' => '', /* ... etc ... */
             ];
             // Muestra la vista de edición
             $this->vista('clientes/editar', $datos);
         }
    }

    // --- ¡NUEVA FUNCIÓN PARA PROCESAR EL REGISTRO DE PAGO DESDE EL MODAL! ---
    /**
     * Recibe los datos del formulario (modal) de registro de pago vía POST
     * y llama al modelo para guardarlos en la tabla `pagos` y actualizar `clientes`.
     */
    public function registrarPago($id_cliente){
        // Solo debe funcionar si se envía por POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Limpia los datos recibidos del formulario modal
             $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // 2. Prepara el array con los detalles del pago para pasar al modelo
            $datos_pago = [
                // Los nombres de índice ('fecha_pago', etc.) deben coincidir con los 'name' de los inputs del modal
                'fecha_pago' => trim($_POST['fecha_pago'] ?? date('Y-m-d')),
                'monto_pagado' => trim($_POST['monto_pagado'] ?? '0'),
                'mes_correspondiente' => trim($_POST['mes_correspondiente'] ?? ''),
                'metodo_pago' => trim($_POST['metodo_pago'] ?? '')
                // El modelo se encargará de añadir id_usuario_registro desde la sesión
            ];

            // 3. Validaciones básicas de los datos del pago
            $errores_pago = [];
            if (empty($datos_pago['fecha_pago']) || !strtotime($datos_pago['fecha_pago'])) { $errores_pago[] = 'Fecha inválida.'; }
            if (!is_numeric($datos_pago['monto_pagado']) || $datos_pago['monto_pagado'] <= 0) { $errores_pago[] = 'Monto inválido.'; }
            if (empty($datos_pago['mes_correspondiente'])) { $errores_pago[] = 'Mes requerido.'; }
            // Puedes añadir más validaciones (ej: formato del mes, monto máximo, etc.)

            // 4. Si no hay errores de validación, intenta registrar el pago
            if (empty($errores_pago)) {
                $pago_registrado = false;
                try {
                    // Llama a la función del modelo que inserta en 'pagos' y actualiza 'clientes'
                    $pago_registrado = $this->clienteModelo->registrarPagoDetallado($id_cliente, $datos_pago);
                } catch (Throwable $e) {
                    error_log("Error EXCEPCIÓN al llamar registrarPagoDetallado (Controlador): " . $e->getMessage());
                    flash('mensaje_error', 'Error fatal al registrar pago. Contacte soporte.', 'alert alert-danger');
                }

                // Muestra mensaje de éxito o error general
                if ($pago_registrado) {
                    flash('cliente_mensaje', 'Pago registrado exitosamente.');
                } else {
                     if (!isset($_SESSION['mensaje_flash'])) { // Solo si no hay error fatal previo
                        flash('mensaje_error', 'No se pudo registrar el pago. Revise los logs.', 'alert alert-danger');
                     }
                }
            } else {
                // Si hubo errores de validación, muestra cuáles fueron
                flash('mensaje_error', 'Datos de pago inválidos: ' . implode(' ', $errores_pago), 'alert alert-danger');
            }

            // 5. Redirige SIEMPRE a la lista de clientes después de intentar registrar
            header('location: ' . RUTA_URL . '/clientes');
            exit();

        } else {
            // Si intentan acceder por GET (escribiendo la URL), redirige a la lista
            flash('mensaje_error', 'Acción no permitida.', 'alert alert-warning');
            header('location: ' . RUTA_URL . '/clientes');
            exit();
        }
    }

    // --- ¡NUEVO! FUNCIÓN PARA VER HISTORIAL DE PAGOS ---
    /**
     * Obtiene y muestra el historial de pagos de un cliente específico.
     * Necesita una vista 'clientes/historial.php' para mostrar los datos.
     */
     public function historialPagos($id_cliente){
         // Verifica si el cliente existe
         $cliente = false;
         try { $cliente = $this->clienteModelo->obtenerClientePorId($id_cliente); } catch (Throwable $e) { /* log */ }
         if ($cliente === false) {
             flash('mensaje_error', 'Cliente no encontrado.', 'alert alert-danger');
             header('location: ' . RUTA_URL . '/clientes');
             exit();
         }

         // Obtiene el historial de pagos desde el modelo
         $historial = [];
         try { $historial = $this->clienteModelo->obtenerHistorialPagos($id_cliente); } catch (Throwable $e) { /* log */ }

         // Prepara los datos para la vista del historial
         $datos = [
             'titulo' => 'Historial de Pagos - ' . htmlspecialchars(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')),
             'cliente' => $cliente, // Pasa la info del cliente por si la necesitas en la vista
             'pagos' => $historial // Pasa el array de pagos
         ];

         // Carga la vista que mostrará el historial (debes crear este archivo)
         // Asegúrate de crear el archivo: app/vistas/clientes/historial.php
         try {
             $this->vista('clientes/historial', $datos);
         } catch (Throwable $e) {
             error_log("Error FATAL al cargar la VISTA clientes/historial: " . $e->getMessage());
             echo "<h1>Error Crítico</h1><p>No se pudo cargar el historial. Contacte al administrador.</p>";
         }
     }

     // --- ¡NUEVO! FUNCIÓN PARA CAMBIAR ESTADO PAGO MANUALMENTE ---
     /**
      * Cambia el estado_pago de un cliente a 'Pendiente' o 'Vencido'.
      * Se podría llamar desde botones en la vista inicio.php
      */
      public function marcarEstadoPago($id_cliente, $nuevo_estado){
          // Validar que el nuevo estado sea permitido
          if ($nuevo_estado != 'Pendiente' && $nuevo_estado != 'Vencido') {
              flash('mensaje_error', 'Estado de pago inválido.', 'alert alert-danger');
              header('location: ' . RUTA_URL . '/clientes');
              exit();
          }

          $actualizado_ok = false;
          try {
              $actualizado_ok = $this->clienteModelo->actualizarEstadoPagoManual($id_cliente, $nuevo_estado);
          } catch (Throwable $e) {
              error_log("Error EXCEPCIÓN al llamar actualizarEstadoPagoManual: " . $e->getMessage());
              flash('mensaje_error', 'Error fatal al actualizar estado. Contacte soporte.', 'alert alert-danger');
          }

          if($actualizado_ok) {
              flash('cliente_mensaje', 'Estado de pago actualizado a ' . $nuevo_estado);
          } else {
              if (!isset($_SESSION['mensaje_flash'])) {
                  flash('mensaje_error', 'No se pudo actualizar el estado de pago.', 'alert alert-danger');
              }
          }

          header('location: ' . RUTA_URL . '/clientes');
          exit();
      }


} // Fin Controlador Clientes