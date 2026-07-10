<?php
require_once __DIR__ . "/../modelo/Factura.php";

header("Content-Type: application/json; charset=utf-8");

$factura = new Factura();

$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
    case "buscarCliente":
        $cedula = isset($_POST["cedula"]) ? trim($_POST["cedula"]) : "";

        if ($cedula == "") {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Debe ingresar la cédula del cliente"
            ]);
            exit;
        }

        $resultado = $factura->buscarCliente($cedula);

        if ($resultado) {
            echo json_encode([
                "estado" => true,
                "datos" => $resultado
            ]);
        } else {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Cliente no encontrado"
            ]);
        }
        break;

    case "listarClientes":
        $resultado = $factura->listarClientes();

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $data[] = [
                "cedula" => htmlspecialchars($fila["cedula"], ENT_QUOTES, "UTF-8"),
                "nombre" => htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"),
                "correo" => htmlspecialchars($fila["correo"], ENT_QUOTES, "UTF-8"),
                "telefono" => htmlspecialchars($fila["telefono"], ENT_QUOTES, "UTF-8"),
                "opciones" => '
                    <button class="btn btn-success btn-sm" onclick=\'seleccionarCliente(' . 
                        json_encode($fila["cedula"]) . ', ' . 
                        json_encode($fila["nombre"]) . 
                    ')\'>
                        Seleccionar
                    </button>'
            ];
        }

        echo json_encode(["data" => $data]);
        break;

    case "buscarProducto":
        $codigo = isset($_POST["codigo"]) ? trim($_POST["codigo"]) : "";

        if ($codigo == "") {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Debe ingresar el código del producto"
            ]);
            exit;
        }

        $resultado = $factura->buscarProducto($codigo);

        if ($resultado) {
            echo json_encode([
                "estado" => true,
                "datos" => $resultado
            ]);
        } else {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Producto no encontrado"
            ]);
        }
        break;

    case "listarProductos":
        $resultado = $factura->listarProductos();

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $data[] = [
                "codigo" => htmlspecialchars($fila["codigo"], ENT_QUOTES, "UTF-8"),
                "nombre" => htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"),
                "precio" => number_format($fila["precio"], 2, ".", ""),
                "stock" => intval($fila["stock"]),
                "categoria" => htmlspecialchars($fila["categoria"], ENT_QUOTES, "UTF-8"),
                "opciones" => '
                    <button class="btn btn-success btn-sm" onclick=\'seleccionarProducto(' . 
                        json_encode($fila["codigo"]) . ', ' . 
                        json_encode($fila["nombre"]) . ', ' . 
                        json_encode(number_format($fila["precio"], 2, ".", "")) . ', ' . 
                        json_encode(intval($fila["stock"])) . 
                    ')\'>
                        Seleccionar
                    </button>'
            ];
        }

        echo json_encode(["data" => $data]);
        break;

    case "guardar":
        $fecha = isset($_POST["fecha"]) ? trim($_POST["fecha"]) : "";
        $cedula_cliente = isset($_POST["cedula_cliente"]) ? trim($_POST["cedula_cliente"]) : "";
        $detallesJson = isset($_POST["detalles"]) ? $_POST["detalles"] : "";

        if ($fecha == "" || $cedula_cliente == "") {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Fecha y cliente son obligatorios"
            ]);
            exit;
        }

        $detalles = json_decode($detallesJson, true);

        if (!is_array($detalles) || count($detalles) == 0) {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Debe agregar productos a la factura"
            ]);
            exit;
        }

        $respuesta = $factura->guardar($fecha, $cedula_cliente, $detalles);

        echo json_encode($respuesta);
        break;

    case "listarFacturas":
        $resultado = $factura->listarFacturas();

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $id = intval($fila["id"]);

            $data[] = [
                "id" => $id,
                "fecha" => htmlspecialchars($fila["fecha"], ENT_QUOTES, "UTF-8"),
                "cedula_cliente" => htmlspecialchars($fila["cedula_cliente"], ENT_QUOTES, "UTF-8"),
                "cliente" => htmlspecialchars($fila["cliente"], ENT_QUOTES, "UTF-8"),
                "total" => number_format($fila["total"], 2, ".", ""),
                "opciones" => '
                    <button class="btn btn-info btn-sm" onclick="verFactura(' . $id . ')">
                        Ver
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="anularFactura(' . $id . ')">
                        Anular
                    </button>
                '
            ];
        }

        echo json_encode(["data" => $data]);
        break;

    case "detalleFactura":
        $id_factura = isset($_POST["id_factura"]) ? intval($_POST["id_factura"]) : 0;

        if ($id_factura <= 0) {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Factura no válida"
            ]);
            exit;
        }

        $resultado = $factura->obtenerDetalleFactura($id_factura);

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $data[] = [
                "codigo_producto" => htmlspecialchars($fila["codigo_producto"], ENT_QUOTES, "UTF-8"),
                "producto" => htmlspecialchars($fila["producto"], ENT_QUOTES, "UTF-8"),
                "cantidad" => intval($fila["cantidad"]),
                "precio" => number_format($fila["precio"], 2, ".", ""),
                "subtotal" => number_format($fila["subtotal"], 2, ".", "")
            ];
        }

        echo json_encode([
            "estado" => true,
            "data" => $data
        ]);
        break;

    case "anularFactura":
        $id_factura = isset($_POST["id_factura"]) ? intval($_POST["id_factura"]) : 0;

        if ($id_factura <= 0) {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Factura no válida"
            ]);
            exit;
        }

        $respuesta = $factura->anularFactura($id_factura);

        echo json_encode($respuesta);
        break;

    default:
        echo json_encode([
            "estado" => false,
            "mensaje" => "Operación no válida"
        ]);
        break;
}
?>