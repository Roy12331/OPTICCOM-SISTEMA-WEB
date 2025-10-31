<?php
class Solicitud {
    private $db;
    private $conexion_exitosa = false; // Control de conexión

    public function __construct(){
        try {
            // Usa tu forma de conexión
            $this->db = (new Conexion)->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("FALLO CRÍTICO CONEXIÓN MODELO SOLICITUD: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /**
     * Agrega una nueva solicitud B2C a la base de datos.
     * Usado por el formulario público.
     */
    public function agregarSolicitud($datos){
        if (!$this->conexion_exitosa) { return false; }
        // Estado inicial siempre 'pendiente' al crear
        $estado_inicial = 'pendiente';
        // Ajusta los nombres de parámetros (:documento, :id_plan) para coincidir con tu bindParam original
        $sql = "INSERT INTO solicitudes (nombres, apellidos, telefono, documento_identidad, email, direccion, referencia, id_plan_interesado, estado_solicitud)
                VALUES (:nombres, :apellidos, :telefono, :documento, :email, :direccion, :referencia, :id_plan, :estado)";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                 error_log("Error PREPARE agregarSolicitud: " . implode(":", $this->db->errorInfo()));
                 return false;
             }

            // Asigna los valores (asegurando que email/referencia sean null si están vacíos)
            $email = empty($datos['email']) ? null : $datos['email'];
            $referencia = empty($datos['referencia']) ? null : $datos['referencia'];
            $id_plan = empty($datos['id_plan_interesado']) ? null : $datos['id_plan_interesado'];

            // Usa los nombres de parámetro correctos de tu SQL
            $stmt->bindParam(':nombres', $datos['nombres'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datos['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':documento', $datos['documento_identidad'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':referencia', $referencia, $referencia === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_plan', $id_plan, $id_plan === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado_inicial, PDO::PARAM_STR);

            return $stmt->execute(); // Devuelve true si éxito
        } catch (PDOException $e) {
             error_log("Error PDO en agregarSolicitud: [Code ".$e->getCode()."] ".$e->getMessage());
             return false;
        } catch (Throwable $e) {
             error_log("Error Throwable en agregarSolicitud: ".$e->getMessage());
             return false;
        }
    }

     /**
      * Obtiene TODAS las solicitudes B2C, uniendo con planes para obtener nombre y precio.
      * Mantenemos tu JOIN y ORDER BY original.
      */
     public function obtenerSolicitudes() {
         if (!$this->conexion_exitosa) { return []; }
         // Tu consulta original con JOIN y ORDER BY
         $sql = "SELECT s.*, p.nombre_plan, p.precio_mensual
                 FROM solicitudes s
                 LEFT JOIN planes p ON s.id_plan_interesado = p.id_plan
                 ORDER BY s.estado_solicitud ASC, s.fecha_solicitud DESC";
         try {
             $stmt = $this->db->prepare($sql);
             if ($stmt === false) {
                  error_log("Error PREPARE obtenerSolicitudes: " . implode(":", $this->db->errorInfo()));
                  return [];
              }
             $stmt->execute();
             $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
             return $resultado ? $resultado : [];
         } catch (Throwable $e) {
             error_log("Error Throwable en obtenerSolicitudes: " . $e->getMessage());
             return [];
         }
     }


    // --- ¡FUNCIÓN AÑADIDA! ---
    /**
     * Obtiene los datos de UNA solicitud específica por su ID.
     * Necesaria para la función convertir() en Ventas.php.
     */
    public function obtenerSolicitudPorId($id_solicitud) {
        if (!$this->conexion_exitosa) {
            error_log("obtenerSolicitudPorId: Sin conexión BD.");
            return false; // Devuelve false si no hay conexión
        }
        // Selecciona todos los campos de la solicitud específica
        $sql = "SELECT * FROM solicitudes WHERE id_solicitud = :id";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                error_log("Error PREPARE obtenerSolicitudPorId ID $id_solicitud: " . implode(":", $this->db->errorInfo()));
                return false; // Devuelve false si falla el prepare
            }
            // Vincula el ID de forma segura
            $stmt->bindParam(':id', $id_solicitud, PDO::PARAM_INT);
            $stmt->execute();
            // Obtiene el resultado como objeto
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            // Devuelve el objeto solicitud si se encontró, o false si no
            return $resultado ? $resultado : false;
        } catch (Throwable $e) {
            error_log("Error Throwable en obtenerSolicitudPorId ID $id_solicitud: " . $e->getMessage());
            return false; // Devuelve false en caso de cualquier error
        }
    }
    // --- FIN FUNCIÓN AÑADIDA ---


    /**
     * Actualiza SOLO el estado de una solicitud B2C.
     * Usado por el controlador Ventas.
     */
    public function actualizarEstado($id, $estado){ // Mantenemos tus nombres de parámetros originales
        if (!$this->conexion_exitosa) { return false; }
        // Valida estados aquí también por seguridad
        $estados_validos = ['pendiente', 'contactado', 'con_cobertura', 'sin_cobertura', 'instalado'];
        if (!in_array($estado, $estados_validos)) {
             error_log("actualizarEstado Solicitud: Estado no válido '$estado' para ID $id");
             return false;
        }
        $sql = "UPDATE solicitudes SET estado_solicitud = :estado WHERE id_solicitud = :id";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                 error_log("Error PREPARE actualizarEstado Solicitud ID $id: " . implode(":", $this->db->errorInfo()));
                 return false;
             }
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute(); // Devuelve true si éxito
        } catch (Throwable $e) {
            error_log("Error Throwable en actualizarEstado Solicitud ID $id: " . $e->getMessage());
            return false;
        }
    }

} // Fin clase Solicitud