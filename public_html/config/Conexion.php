<?php
// Clase para la conexión a la base de datos
class Conexion {
    private $host = 'localhost';
    private $usuario = 'u467606377_RoySuperOPTC';
    private $password = 'RoyHqazx159@.'; // ¡ASEGÚRATE DE QUE ESTA SEA TU CONTRASEÑA CORRECTA!
    private $db = 'u467606377_opticcom_db';
    private $dbh; // Database Handler

    public function conectar(){
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';charset=utf8';
        $opciones = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            die('Error Crítico de Conexión: No se pudo conectar a la base de datos. Verifica la contraseña en el archivo Conexion.php. Detalle: ' . $e->getMessage());
        }
    }
}