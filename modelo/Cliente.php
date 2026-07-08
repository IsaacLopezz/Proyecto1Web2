<?php
require_once __DIR__ . "/../config/Conexion.php";

class Cliente
{
    public function insertar($cedula, $nombre, $correo, $telefono)
    {
        $conexion = Conexion::conectar();

        // Primero revisamos si la cedula ya existe
        $sqlBuscar = "SELECT estado FROM cliente WHERE cedula = ?";
        $stmtBuscar = $conexion->prepare($sqlBuscar);
        $stmtBuscar->bind_param("s", $cedula);
        $stmtBuscar->execute();
        $resultado = $stmtBuscar->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            // Si existe y esta activo, no lo deja duplicar
            if ($fila["estado"] == 1) {
                return [
                    "estado" => false,
                    "mensaje" => "Ya existe un cliente con esa cédula"
                ];
            }

            // Si existe pero estaba eliminado, lo reactiva
            $sqlReactivar = "UPDATE cliente 
                             SET nombre = ?, correo = ?, telefono = ?, estado = 1
                             WHERE cedula = ?";

            $stmtReactivar = $conexion->prepare($sqlReactivar);
            $stmtReactivar->bind_param("ssss", $nombre, $correo, $telefono, $cedula);

            if ($stmtReactivar->execute()) {
                return [
                    "estado" => true,
                    "mensaje" => "Cliente guardado correctamente"
                ];
            }

            return [
                "estado" => false,
                "mensaje" => "No se pudo guardar el cliente"
            ];
        }

        // Si no existe, lo inserta normal
        $sql = "INSERT INTO cliente (cedula, nombre, correo, telefono, estado)
                VALUES (?, ?, ?, ?, 1)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $cedula, $nombre, $correo, $telefono);

        if ($stmt->execute()) {
            return [
                "estado" => true,
                "mensaje" => "Cliente guardado correctamente"
            ];
        }

        return [
            "estado" => false,
            "mensaje" => "No se pudo guardar el cliente"
        ];
    }

    public function editar($cedula, $nombre, $correo, $telefono)
    {
        $conexion = Conexion::conectar();

        $sql = "UPDATE cliente
                SET nombre = ?, correo = ?, telefono = ?
                WHERE cedula = ? AND estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $telefono, $cedula);

        if ($stmt->execute()) {
            return [
                "estado" => true,
                "mensaje" => "Cliente actualizado correctamente"
            ];
        }

        return [
            "estado" => false,
            "mensaje" => "No se pudo actualizar el cliente"
        ];
    }

    public function eliminar($cedula)
    {
        $conexion = Conexion::conectar();

        // Eliminacion logica: no borra de verdad, solo oculta el registro
        $sql = "UPDATE cliente SET estado = 0 WHERE cedula = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $cedula);

        if ($stmt->execute()) {
            return [
                "estado" => true,
                "mensaje" => "Cliente eliminado correctamente"
            ];
        }

        return [
            "estado" => false,
            "mensaje" => "No se pudo eliminar el cliente"
        ];
    }

    public function buscar($cedula)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT cedula, nombre, correo, telefono
                FROM cliente
                WHERE cedula = ? AND estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $cedula);
        $stmt->execute();

        $resultado = $stmt->get_result();

        return $resultado->fetch_assoc();
    }

    public function listar()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT cedula, nombre, correo, telefono
                FROM cliente
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $conexion->query($sql);
    }
}
?>