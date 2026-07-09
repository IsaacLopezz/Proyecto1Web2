<?php
require_once __DIR__ . "/../config/Conexion.php";

class Producto
{
    public function insertar($nombre, $precio, $stock, $cod_categoria)
    {
        $conexion = Conexion::conectar();

        $sql = "INSERT INTO producto (nombre, precio, stock, cod_categoria, estado)
                VALUES (?, ?, ?, ?, 1)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sdii", $nombre, $precio, $stock, $cod_categoria);

        if ($stmt->execute()) {
            return [
                "estado"  => true,
                "mensaje" => "Producto guardado correctamente"
            ];
        }

        return [
            "estado"  => false,
            "mensaje" => "No se pudo guardar el producto"
        ];
    }

    public function editar($cod_producto, $nombre, $precio, $stock, $cod_categoria)
    {
        $conexion = Conexion::conectar();

        $sql = "UPDATE producto
                SET nombre = ?, precio = ?, stock = ?, cod_categoria = ?
                WHERE cod_producto = ? AND estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sdiii", $nombre, $precio, $stock, $cod_categoria, $cod_producto);

        if ($stmt->execute()) {
            return [
                "estado"  => true,
                "mensaje" => "Producto actualizado correctamente"
            ];
        }

        return [
            "estado"  => false,
            "mensaje" => "No se pudo actualizar el producto"
        ];
    }

    public function eliminar($cod_producto)
    {
        $conexion = Conexion::conectar();

        // Eliminación lógica
        $sql = "UPDATE producto SET estado = 0 WHERE cod_producto = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_producto);

        if ($stmt->execute()) {
            return [
                "estado"  => true,
                "mensaje" => "Producto eliminado correctamente"
            ];
        }

        return [
            "estado"  => false,
            "mensaje" => "No se pudo eliminar el producto"
        ];
    }

    public function buscar($cod_producto)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT p.cod_producto, p.nombre, p.precio, p.stock, p.cod_categoria,
                       c.nombre AS nombre_categoria
                FROM producto p
                LEFT JOIN categoria c ON p.cod_categoria = c.cod_categoria
                WHERE p.cod_producto = ? AND p.estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_producto);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function listar()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT p.cod_producto, p.nombre, p.precio, p.stock,
                       c.nombre AS nombre_categoria
                FROM producto p
                LEFT JOIN categoria c ON p.cod_categoria = c.cod_categoria
                WHERE p.estado = 1
                ORDER BY p.nombre ASC";

        return $conexion->query($sql);
    }
}
?>
