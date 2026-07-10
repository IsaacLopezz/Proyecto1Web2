<?php
require_once __DIR__ . "/../config/Conexion.php";

class Factura
{
    public function buscarCliente($cedula)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT cedula, nombre 
                FROM cliente 
                WHERE cedula = ? AND estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $cedula);
        $stmt->execute();

        $resultado = $stmt->get_result();

        return $resultado->fetch_assoc();
    }

    public function listarClientes()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT cedula, nombre, correo, telefono
                FROM cliente
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $conexion->query($sql);
    }

    public function buscarProducto($codigo)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT 
                    p.codigo,
                    p.nombre,
                    p.precio,
                    p.stock,
                    p.id_categoria,
                    c.nombre AS categoria
                FROM producto p
                INNER JOIN categoria c ON p.id_categoria = c.id
                WHERE p.codigo = ? 
                AND p.estado = 1
                AND c.estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $codigo);
        $stmt->execute();

        $resultado = $stmt->get_result();

        return $resultado->fetch_assoc();
    }

    public function listarProductos()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT 
                    p.codigo,
                    p.nombre,
                    p.precio,
                    p.stock,
                    c.nombre AS categoria
                FROM producto p
                INNER JOIN categoria c ON p.id_categoria = c.id
                WHERE p.estado = 1
                AND c.estado = 1
                ORDER BY p.nombre ASC";

        return $conexion->query($sql);
    }

    public function guardar($fecha, $cedula_cliente, $detalles)
    {
        $conexion = Conexion::conectar();
        $conexion->begin_transaction();

        try {
            if (empty($detalles)) {
                throw new Exception("Debe agregar al menos un producto a la factura");
            }

            $cliente = $this->buscarCliente($cedula_cliente);

            if (!$cliente) {
                throw new Exception("El cliente no existe o está inactivo");
            }

            $total = 0;

            foreach ($detalles as $item) {
                $codigo = $item["codigo_producto"];
                $cantidad = intval($item["cantidad"]);

                if ($cantidad <= 0) {
                    throw new Exception("La cantidad debe ser mayor a cero");
                }

                $producto = $this->buscarProducto($codigo);

                if (!$producto) {
                    throw new Exception("El producto " . $codigo . " no existe o está inactivo");
                }

                if (intval($producto["stock"]) < $cantidad) {
                    throw new Exception("No hay suficiente stock para el producto: " . $producto["nombre"]);
                }

                $precio = floatval($producto["precio"]);
                $subtotal = $cantidad * $precio;
                $total += $subtotal;
            }

            $sqlFactura = "INSERT INTO factura (fecha, cedula_cliente, total, estado)
                           VALUES (?, ?, ?, 1)";

            $stmtFactura = $conexion->prepare($sqlFactura);
            $stmtFactura->bind_param("ssd", $fecha, $cedula_cliente, $total);

            if (!$stmtFactura->execute()) {
                throw new Exception("No se pudo guardar la factura");
            }

            $id_factura = $conexion->insert_id;

            foreach ($detalles as $item) {
                $codigo = $item["codigo_producto"];
                $cantidad = intval($item["cantidad"]);

                $producto = $this->buscarProducto($codigo);

                $precio = floatval($producto["precio"]);
                $subtotal = $cantidad * $precio;

                $sqlDetalle = "INSERT INTO detalle_factura 
                               (id_factura, codigo_producto, cantidad, precio, subtotal)
                               VALUES (?, ?, ?, ?, ?)";

                $stmtDetalle = $conexion->prepare($sqlDetalle);
                $stmtDetalle->bind_param("isidd", $id_factura, $codigo, $cantidad, $precio, $subtotal);

                if (!$stmtDetalle->execute()) {
                    throw new Exception("No se pudo guardar el detalle de la factura");
                }

                $sqlStock = "UPDATE producto
                             SET stock = stock - ?
                             WHERE codigo = ?";

                $stmtStock = $conexion->prepare($sqlStock);
                $stmtStock->bind_param("is", $cantidad, $codigo);

                if (!$stmtStock->execute()) {
                    throw new Exception("No se pudo actualizar el stock del producto");
                }
            }

            $conexion->commit();

            return [
                "estado" => true,
                "mensaje" => "Factura guardada correctamente",
                "id_factura" => $id_factura
            ];

        } catch (Exception $e) {
            $conexion->rollback();

            return [
                "estado" => false,
                "mensaje" => $e->getMessage()
            ];
        }
    }

    public function listarFacturas()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT 
                    f.id,
                    f.fecha,
                    f.cedula_cliente,
                    c.nombre AS cliente,
                    f.total
                FROM factura f
                INNER JOIN cliente c ON f.cedula_cliente = c.cedula
                WHERE f.estado = 1
                ORDER BY f.id DESC";

        return $conexion->query($sql);
    }

    public function obtenerDetalleFactura($id_factura)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT 
                    d.id,
                    d.id_factura,
                    d.codigo_producto,
                    p.nombre AS producto,
                    d.cantidad,
                    d.precio,
                    d.subtotal
                FROM detalle_factura d
                INNER JOIN producto p ON d.codigo_producto = p.codigo
                WHERE d.id_factura = ?
                ORDER BY d.id ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_factura);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function anularFactura($id_factura)
    {
        $conexion = Conexion::conectar();
        $conexion->begin_transaction();

        try {
            $sqlFactura = "SELECT id 
                           FROM factura 
                           WHERE id = ? AND estado = 1";

            $stmtFactura = $conexion->prepare($sqlFactura);
            $stmtFactura->bind_param("i", $id_factura);
            $stmtFactura->execute();

            $resultadoFactura = $stmtFactura->get_result();

            if ($resultadoFactura->num_rows == 0) {
                throw new Exception("La factura no existe o ya fue anulada");
            }

            $detalle = $this->obtenerDetalleFactura($id_factura);

            while ($fila = $detalle->fetch_assoc()) {
                $codigo = $fila["codigo_producto"];
                $cantidad = intval($fila["cantidad"]);

                $sqlStock = "UPDATE producto
                             SET stock = stock + ?
                             WHERE codigo = ?";

                $stmtStock = $conexion->prepare($sqlStock);
                $stmtStock->bind_param("is", $cantidad, $codigo);

                if (!$stmtStock->execute()) {
                    throw new Exception("No se pudo devolver el stock");
                }
            }

            $sqlAnular = "UPDATE factura 
                          SET estado = 0 
                          WHERE id = ?";

            $stmtAnular = $conexion->prepare($sqlAnular);
            $stmtAnular->bind_param("i", $id_factura);

            if (!$stmtAnular->execute()) {
                throw new Exception("No se pudo anular la factura");
            }

            $conexion->commit();

            return [
                "estado" => true,
                "mensaje" => "Factura anulada correctamente"
            ];

        } catch (Exception $e) {
            $conexion->rollback();

            return [
                "estado" => false,
                "mensaje" => $e->getMessage()
            ];
        }
    }
}
?>