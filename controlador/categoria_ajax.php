<?php
require_once __DIR__ . "/../modelo/Categoria.php";

header("Content-Type: application/json; charset=utf-8");

$categoria = new Categoria();

$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
    case "guardar":
        $nombre       = isset($_POST["nombre"])       ? trim($_POST["nombre"])       : "";
        $cod_categoria = isset($_POST["cod_categoria"]) ? trim($_POST["cod_categoria"]) : "";
        $modo         = isset($_POST["modo"])         ? $_POST["modo"]              : "agregar";

        if ($nombre == "") {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "El nombre de la categoría es obligatorio"
            ]);
            exit;
        }

        if ($modo == "editar") {
            $respuesta = $categoria->editar($cod_categoria, $nombre);
        } else {
            $respuesta = $categoria->insertar($nombre);
        }

        echo json_encode($respuesta);
        break;

    case "listar":
        $resultado = $categoria->listar();

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $cod = (int) $fila["cod_categoria"];

            $data[] = [
                "cod_categoria" => $cod,
                "nombre"        => htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"),
                "opciones"      => '
                    <button class="btn btn-warning btn-sm" onclick="editarCategoria(' . $cod . ')">
                        Editar
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarCategoria(' . $cod . ')">
                        Eliminar
                    </button>
                '
            ];
        }

        echo json_encode(["data" => $data]);
        break;

    case "buscar":
        $cod_categoria = isset($_POST["cod_categoria"]) ? trim($_POST["cod_categoria"]) : "";

        if ($cod_categoria == "") {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Debe seleccionar una categoría"
            ]);
            exit;
        }

        $resultado = $categoria->buscar($cod_categoria);

        if ($resultado) {
            echo json_encode([
                "estado" => true,
                "datos"  => $resultado
            ]);
        } else {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Categoría no encontrada"
            ]);
        }
        break;

    case "eliminar":
        $cod_categoria = isset($_POST["cod_categoria"]) ? trim($_POST["cod_categoria"]) : "";

        if ($cod_categoria == "") {
            echo json_encode([
                "estado"  => false,
                "mensaje" => "Debe seleccionar una categoría"
            ]);
            exit;
        }

        $respuesta = $categoria->eliminar($cod_categoria);
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
