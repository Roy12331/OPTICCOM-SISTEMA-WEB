<?php
class Cotizacion {
    private $db;

    public function __construct(){
        $this->db = (new Conexion)->conectar();
    }

    public function agregarCotizacion($datos){
        $sql = "INSERT INTO cotizaciones (razon_social, ruc, persona_contacto, telefono_contacto, email_contacto, direccion_proyecto, mensaje) 
                VALUES (:razon_social, :ruc, :contacto, :telefono, :email, :direccion, :mensaje)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':razon_social', $datos['razon_social']);
        $stmt->bindParam(':ruc', $datos['ruc']);
        $stmt->bindParam(':contacto', $datos['persona_contacto']);
        $stmt->bindParam(':telefono', $datos['telefono_contacto']);
        $stmt->bindParam(':email', $datos['email_contacto']);
        $stmt->bindParam(':direccion', $datos['direccion_proyecto']);
        $stmt->bindParam(':mensaje', $datos['mensaje']);

        return $stmt->execute();
    }

    public function obtenerCotizaciones(){
        $sql = "SELECT * FROM cotizaciones ORDER BY estado_cotizacion ASC, fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function actualizarEstado($id, $estado){
        $sql = "UPDATE cotizaciones SET estado_cotizacion = :estado WHERE id_cotizacion = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}