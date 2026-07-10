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

    public function buscarProducto($cod_producto)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT p.cod_producto, p.nombre, p.precio, p.stock,
                       c.nombre AS categoria
                FROM producto p
                INNER JOIN categoria c ON p.cod_categoria = c.cod_categoria
                WHERE p.cod_producto = ?
                AND p.estado = 1
                AND c.estado = 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $cod_producto);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function listarProductos()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT p.cod_producto, p.nombre, p.precio, p.stock,
                       c.nombre AS categoria
                FROM producto p
                INNER JOIN categoria c ON p.cod_categoria = c.cod_categoria
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

            // Primera pasada: validar stock y calcular total
            foreach ($detalles as $item) {
                $cod_producto = $item["cod_producto"];
                $cantidad     = intval($item["cantidad"]);

                if ($cantidad <= 0) {
                    throw new Exception("La cantidad debe ser mayor a cero");
                }

                $producto = $this->buscarProducto($cod_producto);

                if (!$producto) {
                    throw new Exception("El producto " . $cod_producto . " no existe o está inactivo");
                }

                if (intval($producto["stock"]) < $cantidad) {
                    throw new Exception("No hay suficiente stock para: " . $producto["nombre"]);
                }

                $total += floatval($producto["precio"]) * $cantidad;
            }

            // Insertar encabezado de factura
            $sqlFactura = "INSERT INTO factura (fecha, cedula_cliente, total, estado)
                           VALUES (?, ?, ?, 1)";

            $stmtFactura = $conexion->prepare($sqlFactura);
            $stmtFactura->bind_param("ssd", $fecha, $cedula_cliente, $total);

            if (!$stmtFactura->execute()) {
                throw new Exception("No se pudo guardar la factura");
            }

            $num_factura = $conexion->insert_id;

            // Segunda pasada: insertar detalles y descontar stock
            foreach ($detalles as $item) {
                $cod_producto = $item["cod_producto"];
                $cantidad     = intval($item["cantidad"]);
                $producto     = $this->buscarProducto($cod_producto);
                $precio       = floatval($producto["precio"]);
                $subtotal     = $precio * $cantidad;

                $sqlDetalle = "INSERT INTO detalle_factura
                               (num_factura, cod_producto, cantidad, precio_unitario, subtotal)
                               VALUES (?, ?, ?, ?, ?)";

                $stmtDetalle = $conexion->prepare($sqlDetalle);
                $stmtDetalle->bind_param("iiidd", $num_factura, $cod_producto, $cantidad, $precio, $subtotal);

                if (!$stmtDetalle->execute()) {
                    throw new Exception("No se pudo guardar el detalle de la factura");
                }

                $sqlStock = "UPDATE producto SET stock = stock - ? WHERE cod_producto = ?";

                $stmtStock = $conexion->prepare($sqlStock);
                $stmtStock->bind_param("ii", $cantidad, $cod_producto);

                if (!$stmtStock->execute()) {
                    throw new Exception("No se pudo actualizar el stock del producto");
                }
            }

            $conexion->commit();

            return [
                "estado"      => true,
                "mensaje"     => "Factura guardada correctamente",
                "num_factura" => $num_factura
            ];

        } catch (Exception $e) {
            $conexion->rollback();

            return [
                "estado"  => false,
                "mensaje" => $e->getMessage()
            ];
        }
    }

    public function listarFacturas()
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT f.num_factura, f.fecha, f.cedula_cliente,
                       c.nombre AS cliente, f.total
                FROM factura f
                INNER JOIN cliente c ON f.cedula_cliente = c.cedula
                WHERE f.estado = 1
                ORDER BY f.num_factura DESC";

        return $conexion->query($sql);
    }

    public function obtenerDetalleFactura($num_factura)
    {
        $conexion = Conexion::conectar();

        $sql = "SELECT d.id_detalle, d.num_factura, d.cod_producto,
                       p.nombre AS producto, d.cantidad, d.precio_unitario, d.subtotal
                FROM detalle_factura d
                INNER JOIN producto p ON d.cod_producto = p.cod_producto
                WHERE d.num_factura = ?
                ORDER BY d.id_detalle ASC";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $num_factura);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function anularFactura($num_factura)
    {
        $conexion = Conexion::conectar();
        $conexion->begin_transaction();

        try {
            $sqlCheck = "SELECT num_factura FROM factura WHERE num_factura = ? AND estado = 1";

            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->bind_param("i", $num_factura);
            $stmtCheck->execute();

            if ($stmtCheck->get_result()->num_rows == 0) {
                throw new Exception("La factura no existe o ya fue anulada");
            }

            // Devolver stock de cada producto del detalle
            $detalle = $this->obtenerDetalleFactura($num_factura);

            while ($fila = $detalle->fetch_assoc()) {
                $cod_producto = intval($fila["cod_producto"]);
                $cantidad     = intval($fila["cantidad"]);

                $sqlStock = "UPDATE producto SET stock = stock + ? WHERE cod_producto = ?";

                $stmtStock = $conexion->prepare($sqlStock);
                $stmtStock->bind_param("ii", $cantidad, $cod_producto);

                if (!$stmtStock->execute()) {
                    throw new Exception("No se pudo devolver el stock");
                }
            }

            $sqlAnular = "UPDATE factura SET estado = 0 WHERE num_factura = ?";

            $stmtAnular = $conexion->prepare($sqlAnular);
            $stmtAnular->bind_param("i", $num_factura);

            if (!$stmtAnular->execute()) {
                throw new Exception("No se pudo anular la factura");
            }

            $conexion->commit();

            return [
                "estado"  => true,
                "mensaje" => "Factura anulada correctamente"
            ];

        } catch (Exception $e) {
            $conexion->rollback();

            return [
                "estado"  => false,
                "mensaje" => $e->getMessage()
            ];
        }
    }
}
?>
