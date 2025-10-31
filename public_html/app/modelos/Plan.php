<?php
class Plan {
    private $db;

    public function __construct(){
        $this->db = (new Conexion)->conectar();
    }

    public function obtenerPlanes(){
        $sql = "SELECT * FROM planes ORDER BY precio_mensual ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function agregarPlan($datos){
        $sql = "INSERT INTO planes (nombre_plan, velocidad, precio_mensual, descripcion) VALUES (:nombre, :velocidad, :precio, :descripcion)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $datos['nombre_plan']);
        $stmt->bindParam(':velocidad', $datos['velocidad']);
        $stmt->bindParam(':precio', $datos['precio_mensual']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        return $stmt->execute();
    }

    public function contarPlanes(){
        $sql = "SELECT COUNT(*) as total FROM planes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    // (Aquí irían las futuras funciones para editar y borrar planes)
}
?>