<?php
class Cliente {
    private $db;
    private $conexion_exitosa = false;

    // Constructor: Establece la conexión y controla errores iniciales
    public function __construct(){
        try {
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("FALLO CRÍTICO CONEXIÓN MODELO CLIENTE: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /**
     * Obtiene los datos de un cliente específico por su ID.
     */
    public function obtenerClientePorId($id){
        if (!$this->conexion_exitosa) {
            error_log("obtenerClientePorId: Sin conexión BD.");
            return false;
        }
        $sql = "SELECT c.id_cliente, c.nombre, c.apellido, c.dni, c.email, c.telefono,
                       c.direccion, c.detalles, c.id_plan_fk, c.estado_servicio,
                       c.estado_pago,
                       c.fecha_instalacion, c.fecha_creacion,
                       p.nombre_plan
                FROM clientes c
                LEFT JOIN planes p ON c.id_plan_fk = p.id_plan
                WHERE c.id_cliente = :id";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado : false;
        } catch (Throwable $e) { return false; }
    }

    /**
     * Actualiza los datos generales de un cliente existente.
     */
    public function actualizarCliente($datos){
         if (!$this->conexion_exitosa) { return false; }
         $email = empty($datos['email']) ? null : trim($datos['email']);
         $detalles = empty($datos['detalles']) ? null : trim($datos['detalles']);
         $id_plan_valido = isset($datos['id_plan']) && is_numeric($datos['id_plan']) ? (int)$datos['id_plan'] : null;
         $estado_servicio = $datos['estado_servicio'] ?? 'Activo';
         if (!in_array($estado_servicio, ['Activo', 'Suspendido', 'Cancelado'])) { $estado_servicio = 'Activo'; }
         $fecha_instalacion = (!empty($datos['fecha_instalacion']) && strtotime($datos['fecha_instalacion'])) ? $datos['fecha_instalacion'] : null;

         $sql = "UPDATE clientes SET
                     nombre = :nombre, apellido = :apellido, dni = :dni,
                     telefono = :telefono, direccion = :direccion, email = :email,
                     id_plan_fk = :id_plan_fk, fecha_instalacion = :fecha_instalacion,
                     detalles = :detalles, estado_servicio = :estado_servicio
                 WHERE id_cliente = :id_cliente";
         try {
             $stmt = $this->db->prepare($sql); // CORREGIDO
             if ($stmt === false) { return false; }
             $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
             $stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
             $stmt->bindParam(':dni', $datos['documento_identidad'], PDO::PARAM_STR);
             $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
             $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
             $stmt->bindParam(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
             $stmt->bindParam(':id_plan_fk', $id_plan_valido, $id_plan_valido === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
             $stmt->bindParam(':fecha_instalacion', $fecha_instalacion, $fecha_instalacion === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
             $stmt->bindParam(':detalles', $detalles, $detalles === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
             $stmt->bindParam(':estado_servicio', $estado_servicio, PDO::PARAM_STR);
             $stmt->bindParam(':id_cliente', $datos['id_cliente'], PDO::PARAM_INT);
             return $stmt->execute();
         } catch (Throwable $e) { return false; }
    }

    /**
     * Agrega un nuevo cliente a la BD. VERSIÓN LIMPIA: NO MANEJA LA CONTRASEÑA.
     */
    public function agregarCliente($datos){
        if (!$this->conexion_exitosa) {
            error_log("agregarCliente: Sin conexión BD.");
            return false;
        }

        $email = empty($datos['email']) ? null : trim($datos['email']);
        $detalles = empty($datos['detalles']) ? null : trim($datos['detalles']);
        $id_plan_valido = isset($datos['id_plan']) && is_numeric($datos['id_plan']) ? (int)$datos['id_plan'] : null;
        $fecha_instalacion = (!empty($datos['fecha_instalacion']) && strtotime($datos['fecha_instalacion'])) ? $datos['fecha_instalacion'] : date('Y-m-d');
        $estado_servicio = $datos['estado_servicio'] ?? 'Activo';
        $estado_pago_inicial = 'Al día';

        // Consulta INSERT (SIN la columna 'password')
        $sql = "INSERT INTO clientes (nombre, apellido, dni, telefono, direccion, email, id_plan_fk, fecha_instalacion, estado_servicio, estado_pago, detalles)
                VALUES (:nombre, :apellido, :dni, :telefono, :direccion, :email, :id_plan_fk, :fecha_instalacion, :estado_servicio, :estado_pago, :detalles)";

        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }

            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido', $datos['apellido'], PDO::PARAM_STR);
            $stmt->bindParam(':dni', $datos['documento_identidad'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_plan_fk', $id_plan_valido, $id_plan_valido === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':fecha_instalacion', $fecha_instalacion, PDO::PARAM_STR);
            $stmt->bindParam(':estado_servicio', $estado_servicio, PDO::PARAM_STR);
            $stmt->bindParam(':estado_pago', $estado_pago_inicial, PDO::PARAM_STR);
            $stmt->bindParam(':detalles', $detalles, $detalles === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
             error_log("Error PDO en agregarCliente: " . $e->getMessage());
             return false;
        } catch (Throwable $e) {
              error_log("Error Throwable en agregarCliente: " . $e->getMessage());
              return false;
        }
    }

    /**
     * Obtiene la lista de todos los clientes (MODIFICADO: incluye password).
     */
    public function obtenerClientes($busqueda = null){
        if (!$this->conexion_exitosa) { return []; }
        // Se añade c.password al SELECT (para que /usuarios vea si tiene acceso)
        $sql = "SELECT c.id_cliente, c.nombre, c.apellido, c.dni, c.email, c.telefono, c.direccion, c.detalles, c.id_plan_fk, c.estado_servicio,
                       c.estado_pago, c.password,
                       c.fecha_instalacion, c.fecha_creacion,
                       p.nombre_plan
                FROM clientes c LEFT JOIN planes p ON c.id_plan_fk = p.id_plan";
        if (!empty($busqueda)) { $sql .= " WHERE c.nombre LIKE :busqueda OR c.apellido LIKE :busqueda OR c.dni LIKE :busqueda"; }
        $sql .= " ORDER BY c.id_cliente ASC";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return []; }
            if (!empty($busqueda)) { $termino_busqueda = '%' . trim($busqueda) . '%'; $stmt->bindParam(':busqueda', $termino_busqueda, PDO::PARAM_STR); }
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $resultado ? $resultado : [];
        } catch (Throwable $e) { return []; }
    }

    /**
     * Cuenta el número total de clientes.
     */
    public function contarClientes(){
        if (!$this->conexion_exitosa) { return 0; }
        $sql = "SELECT COUNT(*) as total FROM clientes";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return 0; }
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? (int)$resultado->total : 0;
        } catch (Throwable $e) { return 0; }
    }

    /**
     * Cuenta clientes por estado de servicio.
     */
    public function contarClientesPorEstado($estado){
        if (!$this->conexion_exitosa) { return 0; }
        $estados_validos = ['Activo', 'Suspendido', 'Cancelado'];
        if (!in_array($estado, $estados_validos)) { return 0; }
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE estado_servicio = :estado";
        try {
            $stmt = $this->db->prepare($sql); // CORREGIDO
            if ($stmt === false) { return 0; }
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? (int)$resultado->total : 0;
        } catch (Throwable $e) { return 0; }
    }

    /**
     * Obtiene los últimos N clientes registrados.
     */
    public function obtenerUltimosClientes($limite = 5){
         if (!$this->conexion_exitosa) { return []; }
        $sql = "SELECT * FROM clientes ORDER BY fecha_creacion DESC LIMIT :limite";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return []; }
            $limite_int = (int)$limite;
            $stmt->bindParam(':limite', $limite_int, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $resultado ? $resultado : [];
        } catch (Throwable $e) { return []; }
    }

    /**
     * Borra un cliente por su ID.
     */
    public function borrarCliente($id){
         if (!$this->conexion_exitosa) { return false; }
        $sql = "DELETE FROM clientes WHERE id_cliente = :id";
        try {
            $stmt = $this->db->prepare($sql); // CORREGIDO
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $e) { return false; }
    }

    // --- FUNCIONES PARA PAGOS DETALLADOS (¡TU CÓDIGO FUNCIONAL RESTAURADO!) ---
    public function registrarPagoDetallado($id_cliente, $datos_pago){
         if (!$this->conexion_exitosa) { return false; }
         $this->db->beginTransaction();
         try {
             $sql_insert = "INSERT INTO pagos (id_cliente_fk, fecha_pago, monto_pagado, mes_correspondiente, metodo_pago, id_usuario_registro) VALUES (:id_cliente, :fecha_pago, :monto, :mes, :metodo, :id_usuario)";
             $stmt_insert = $this->db->prepare($sql_insert);
             if ($stmt_insert === false) { throw new Exception("Error PREPARE INSERT pagos"); }
             $fecha_pago = (!empty($datos_pago['fecha_pago']) && strtotime($datos_pago['fecha_pago'])) ? $datos_pago['fecha_pago'] : date('Y-m-d');
             $monto = isset($datos_pago['monto_pagado']) && is_numeric($datos_pago['monto_pagado']) ? number_format((float)$datos_pago['monto_pagado'], 2, '.', '') : '0.00';
             $mes = trim($datos_pago['mes_correspondiente'] ?? 'N/A');
             $metodo = isset($datos_pago['metodo_pago']) ? trim($datos_pago['metodo_pago']) : null;
             $id_usuario = $_SESSION['id_usuario'] ?? null;
             $stmt_insert->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
             $stmt_insert->bindParam(':fecha_pago', $fecha_pago, PDO::PARAM_STR);
             $stmt_insert->bindParam(':monto', $monto, PDO::PARAM_STR);
             $stmt_insert->bindParam(':mes', $mes, PDO::PARAM_STR);
             $stmt_insert->bindParam(':metodo', $metodo, $metodo === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
             $stmt_insert->bindParam(':id_usuario', $id_usuario, $id_usuario === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
             $insert_ok = $stmt_insert->execute();
             if (!$insert_ok) { throw new Exception("Error EXECUTE INSERT pagos: " . implode(":", $stmt_insert->errorInfo())); }

             $sql_update = "UPDATE clientes SET estado_pago = 'Al día' WHERE id_cliente = :id_cliente";
             $stmt_update = $this->db->prepare($sql_update); // CORREGIDO
             if ($stmt_update === false) { throw new Exception("Error PREPARE UPDATE clientes"); }
             $stmt_update->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
             $update_ok = $stmt_update->execute();
             if (!$update_ok) { throw new Exception("Error EXECUTE UPDATE clientes: " . implode(":", $stmt_update->errorInfo())); }

             $this->db->commit();
             return true;
         } catch (Throwable $e) {
             $this->db->rollBack(); // CORREGIDO
             error_log("Error Throwable en registrarPagoDetallado (Cliente ID: $id_cliente): " . $e->getMessage());
             return false;
         }
    }

    public function obtenerHistorialPagos($id_cliente) {
         if (!$this->conexion_exitosa) { return []; }
        $sql = "SELECT p.*, u.nombre as nombre_usuario_registro
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario_registro = u.id_usuario
                WHERE p.id_cliente_fk = :id_cliente
                ORDER BY p.fecha_pago DESC, p.fecha_registro DESC";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return []; }
            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $resultado ? $resultado : [];
        } catch (Throwable $e) { return []; }
    }

     public function actualizarEstadoPagoManual($id_cliente, $nuevo_estado) {
         if (!$this->conexion_exitosa) { return false; }
         if ($nuevo_estado != 'Pendiente' && $nuevo_estado != 'Vencido') { return false; }
         $sql = "UPDATE clientes SET estado_pago = :estado WHERE id_cliente = :id";
         try {
             $stmt = $this->db->prepare($sql); // ¡¡¡CORREGIDO EL ERROR PRINCIPAL!!! ($this->db)
             if ($stmt === false) { return false; }
             $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_STR);
             $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
             return $stmt->execute();
         } catch (Throwable $e) { return false; }
     }
    // --- FIN FUNCIONES RESTAURADAS ---


    // --- NUEVAS FUNCIONES AÑADIDAS (Necesarias para tu lógica de /usuarios) ---
    /**
     * Verifica las credenciales de un cliente (DNI y password).
     * Se usará para el portal cliente.
     */
    public function loginCliente($dni, $password){
        if (!$this->conexion_exitosa) { return false; }
        try {
            $sql = "SELECT * FROM clientes WHERE dni = :dni";
            $stmt = $this->db->prepare($sql); // CORREGIDO
            if ($stmt === false) { return false; }
            $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
            $stmt->execute();
            $cliente = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$cliente) { return false; }
            if (empty($cliente->password)) { return false; }

            if (password_verify($password, $cliente->password)) {
                return $cliente;
            } else {
                return false;
            }
        } catch (Throwable $e) {
            error_log("Error Throwable en loginCliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guarda una contraseña hasheada para un cliente específico.
     * Esta será la función que llamaremos desde /usuarios (tu idea).
     */
    public function crearPasswordCliente($id_cliente, $password_plana){
         if (!$this->conexion_exitosa) { return false; }

         $password_hasheada = password_hash($password_plana, PASSWORD_DEFAULT);
         if ($password_hasheada === false) {
             error_log("Error al hashear contraseña para cliente ID: " . $id_cliente);
             return false;
         }

         $sql = "UPDATE clientes SET password = :password WHERE id_cliente = :id_cliente";
         try {
             $stmt = $this->db->prepare($sql); // CORREGIDO
             if ($stmt === false) { return false; }
             $stmt->bindParam(':password', $password_hasheada, PDO::PARAM_STR);
             $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
             return $stmt->execute();
         } catch (Throwable $e) {
             error_log("Error Throwable en crearPasswordCliente ID $id_cliente: " . $e->getMessage());
             return false;
         }
    }

} // Fin Clase Cliente