<?php
require_once __DIR__ . "/global.php";

class Conexion
{
    public static function conectar()
    {
        $conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        $conexion->set_charset(DB_ENCODE);

        return $conexion;
    }
}
?>