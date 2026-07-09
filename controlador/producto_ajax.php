<?php
require_once __DIR__ . "/../modelo/Producto.php";

header("Content-Type: application/json; charset=utf-8");

$producto = new Producto();

$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
    case "guardar":
        $nombre        = isset($_POST["nombre"])        ? trim($_POST["nombre"])        : "";
        $precio        = isset($_POST["precio"])        ? trim($_POST["precio"])        : "";
        $stock         = isset($_POST["stock"])         ? trim($_POST["stock"])         : "";
        $cod_categoria = isset($_POST["cod_categoria"]) ? trim($_POST["cod_categoria"]) : "";
        $cod_producto  = isset($_POST["cod_producto"])  ? trim($_POST["cod_producto"])  : "";
        $modo          = isset($_POST["modo"])          ? $_POST["modo"]               : "agregar";

        if ($nombre == "" || $precio === "" || $stock === "" || $cod_categoria == "") {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Nombre, precio, stock y categoría son obligatorios"
            ]);
            exit;
        }

        if (!is_numeric($precio) || (float) $precio < 0) {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "El precio debe ser un número mayor o igual a cero"
            ]);
            exit;
        }

        if (!preg_match('/^\d+$/', $stock)) {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "El stock debe ser un número entero mayor o igual a cero"
            ]);
            exit;
        }

        if ($modo == "editar") {
            $respuesta = $producto->editar(
                (int) $cod_producto,
                $nombre,
                (float) $precio,
                (int) $stock,
                (int) $cod_categoria
            );
        } else {
            $respuesta = $producto->insertar(
                $nombre,
                (float) $precio,
                (int) $stock,
                (int) $cod_categoria
            );
        }

        echo json_encode($respuesta);
        break;

    case "listar":
        $resultado = $producto->listar();

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $cod = (int) $fila["cod_producto"];

            $data[] = [
                "cod_producto"    => $cod,
                "nombre"          => htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"),
                "precio"          => number_format((float) $fila["precio"], 2),
                "stock"           => (int) $fila["stock"],
                "nombre_categoria" => htmlspecialchars($fila["nombre_categoria"] ?? "Sin categoría", ENT_QUOTES, "UTF-8"),
                "opciones"        => '
                    <button class="btn btn-warning btn-sm" onclick="editarProducto(' . $cod . ')">
                        Editar
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarProducto(' . $cod . ')">
                        Eliminar
                    </button>
                '
            ];
        }

        echo json_encode(["data" => $data]);
        break;

    case "buscar":
        $cod_producto = isset($_POST["cod_producto"]) ? trim($_POST["cod_producto"]) : "";

        if ($cod_producto == "") {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Debe seleccionar un producto"
            ]);
            exit;
        }

        $resultado = $producto->buscar($cod_producto);

        if ($resultado) {
            echo json_encode([
                "estado" => true,
                "datos"  => $resultado
            ]);
        } else {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Producto no encontrado"
            ]);
        }
        break;

    case "eliminar":
        $cod_producto = isset($_POST["cod_producto"]) ? trim($_POST["cod_producto"]) : "";

        if ($cod_producto == "") {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Debe seleccionar un producto"
            ]);
            exit;
        }

        $respuesta = $producto->eliminar($cod_producto);
        echo json_encode($respuesta);
        break;

    default:
        echo json_encode([
            "estado"  => false,
            "mensaje" => "Operación no válida"
        ]);
        break;
}
?>
