<?php
class Usuario {
    private $db;
    private $conexion_exitosa = false;

    public function __construct(){
        try {
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("FALLO CRÍTICO CONEXIÓN MODELO USUARIO: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /**
     * Función de Login para Administradores
     */
    public function login($email, $password){
        if (!$this->conexion_exitosa) { return false; }
        try {
            $usuario = $this->obtenerUsuarioPorEmail($email);
            if ($usuario) {
                // Verifica que el usuario tenga una contraseña (no sea NULL)
                if (empty($usuario->password)) return false; 
                
                // Verifica la contraseña
                if (password_verify($password, $usuario->password)) {
                    return $usuario; // Devuelve objeto usuario si éxito
                }
            }
            return false; // Falla email o password
        } catch (Throwable $e) { return false; }
    }

    /**
     * Obtiene todos los usuarios administrativos
     */
    public function obtenerUsuarios(){
        if (!$this->conexion_exitosa) { return []; }
        $sql = "SELECT id_usuario, nombre, email, rol, fecha_creacion FROM usuarios ORDER BY nombre ASC";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return []; }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Throwable $e) { return []; }
    }

    /**
     * Agrega un nuevo usuario administrativo
     */
    public function agregarUsuario($datos){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "INSERT INTO usuarios (nombre, email, rol, password) VALUES (:nombre, :email, :rol, :password)";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datos['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $datos['password'], PDO::PARAM_STR); // Asume que la contraseña ya está hasheada
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error PDO en agregarUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un usuario administrativo (con o sin contraseña)
     */
    public function actualizarUsuario($datos){
        if (!$this->conexion_exitosa) { return false; }
        
        try {
            // Si la contraseña está vacía, no la actualizamos
            if (empty($datos['password'])) {
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id_usuario = :id_usuario";
                $stmt = $this->db->prepare($sql);
                if ($stmt === false) { return false; }
            } else {
                // Si hay contraseña nueva, la hasheamos y actualizamos
                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol, password = :password WHERE id_usuario = :id_usuario";
                $stmt = $this->db->prepare($sql);
                if ($stmt === false) { return false; }
                $stmt->bindParam(':password', $datos['password'], PDO::PARAM_STR);
            }
            
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datos['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error PDO en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Borra un usuario administrativo
     */
    public function borrarUsuario($id_usuario){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error PDO en borrarUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por email (para login y validación de 'agregar')
     */
    public function obtenerUsuarioPorEmail($email){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado : false; // Devuelve usuario o false
        } catch (Throwable $e) { return false; }
    }

    /**
     * Busca un usuario por ID (para 'editar' y 'perfil')
     */
    public function obtenerUsuarioPorId($id_usuario){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "SELECT id_usuario, nombre, email, rol, fecha_creacion FROM usuarios WHERE id_usuario = :id_usuario";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado : false;
        } catch (Throwable $e) { return false; }
    }

} // Fin Clase Usuario