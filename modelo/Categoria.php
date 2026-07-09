<?php
require_once __DIR__ . "/../config/Conexion.php";

class Categoria
{
    public function insertar($nombre)
    {
        $conexion = Conexion::conectar();

        // Revisamos si ya existe una categoría con ese nombre
        $sqlBuscar = "SELECT cod_categoria, estado FROM categoria WHERE nombre = ?";
        $stmtBuscar = $conexion->prepare($sqlBuscar);
        $stmtBuscar->bind_param("s", $nombre);
        $stmtBuscar->execute();
        $resultado = $stmtBuscar->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            // Si existe y está activa, no la deja duplicar
            if ($fila["estado"] == 1) {
                return [
                    "estado" => false,
                    "mensaje" => "Ya existe una categoría con ese nombre"
                ];
            }

            // Si existe pero estaba eliminada, la reactiva
            $sqlReactivar = "UPDATE categoria SET nombre = ?, estado = 1 WHERE cod_categoria = ?";
            $stmtReactivar = $conexion->prepare($sqlReactivar);
            $stmtReactivar->bind_param("si", $nombre, $fila["cod_categoria"]);

            if ($stmtReactivar->execute()) {
                return [
                    "estado" => true,
                    "mensaje" => "Categoría guardada correctamente"
                ];
            }

            return [
                "estado" => false,
                "mensaje" => "No se pudo guardar la categoría"
            ];
        }

        // Si no existe, la inserta normal
        $sql = "INSERT INTO categoria (nombre, estado) VALUES (?, 1)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $nombre);

        if ($stmt->execute()) {
            return [
                "estado" => true,
                "mensaje" => "Categoría guardada correctamente"
            ];
        }

        return [
            "estado" => false,
            "mensaje" => "No se pudo guardar la categoría"
        ];
    }

    public function editar($cod_categoria, $nombre)
    {
        $conexion = Conexion::conectar();

        $sql = "UPDATE categoria
                SET nombre = ?
                WHERE cod_categoria = ? AND estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $nombre, $cod_categoria);

        if ($stmt->execute()) {
            return [
                "estado" => true,
                "mensaje" => "Categoría actualizada correctamente"
            ];
        }

        return [
            "estado" => false,
            "mensaje" => "No se pudo actualizar la categoría"
        ];
    }

    public function eliminar($cod_categoria)
    {
        $conexion = Conexion::conectar();

        // Eliminación lógica: no borra de verdad, solo desactiva el registro
        $sql = "UPDATE categoria SET estado = 0 WHERE cod_categoria = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_categoria);

        if ($stmt->execute()) {
            return [
                "estado" => true,
                "mensaje" => "Categoría eliminada correctamente"
            ];
        }

        return [
            "estado" => false,
            "mensaje" => "No se pudo eliminar la categoría"
        ];
    }

    public function buscar($cod_categoria)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT cod_categoria, nombre
                FROM categoria
                WHERE cod_categoria = ? AND estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_categoria);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function listar()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT cod_categoria, nombre
                FROM categoria
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $conexion->query($sql);
    }
}
?>
