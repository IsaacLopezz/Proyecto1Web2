<?php
require_once __DIR__ . "/../modelo/Cliente.php";

header("Content-Type: application/json; charset=utf-8");

$cliente = new Cliente();

$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
    case "guardar":
        $cedula = isset($_POST["cedula"]) ? trim($_POST["cedula"]) : "";
        $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
        $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) : "";
        $telefono = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : "";
        $modo = isset($_POST["modo"]) ? $_POST["modo"] : "agregar";

        if ($cedula == "" || $nombre == "" || $telefono == "") {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Cédula, nombre y teléfono son obligatorios"
            ]);
            exit;
        }

        if (!preg_match("/^[0-9]+$/", $telefono)) {
            echo json_encode([
                "estado" => false,
                "mensaje" => "El teléfono solo debe contener números"
            ]);
            exit;
        }

        if ($correo != "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "estado" => false,
                "mensaje" => "El correo no tiene un formato válido"
            ]);
            exit;
        }

        if ($modo == "editar") {
            $respuesta = $cliente->editar($cedula, $nombre, $correo, $telefono);
        } else {
            $respuesta = $cliente->insertar($cedula, $nombre, $correo, $telefono);
        }

        echo json_encode($respuesta);
        break;

    case "listar":
        $resultado = $cliente->listar();

        $data = [];

        while ($fila = $resultado->fetch_assoc()) {
            $cedula = htmlspecialchars($fila["cedula"], ENT_QUOTES, "UTF-8");

            $data[] = [
                "cedula" => htmlspecialchars($fila["cedula"], ENT_QUOTES, "UTF-8"),
                "nombre" => htmlspecialchars($fila["nombre"], ENT_QUOTES, "UTF-8"),
                "correo" => htmlspecialchars($fila["correo"], ENT_QUOTES, "UTF-8"),
                "telefono" => htmlspecialchars($fila["telefono"], ENT_QUOTES, "UTF-8"),
                "opciones" => '
                    <button class="btn btn-warning btn-sm" onclick="editarCliente(\'' . $cedula . '\')">
                        Editar
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarCliente(\'' . $cedula . '\')">
                        Eliminar
                    </button>
                '
            ];
        }

        echo json_encode(["data" => $data]);
        break;

    case "buscar":
        $cedula = isset($_POST["cedula"]) ? trim($_POST["cedula"]) : "";

        if ($cedula == "") {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Debe ingresar una cédula"
            ]);
            exit;
        }

        $resultado = $cliente->buscar($cedula);

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

    case "eliminar":
        $cedula = isset($_POST["cedula"]) ? trim($_POST["cedula"]) : "";

        if ($cedula == "") {
            echo json_encode([
                "estado" => false,
                "mensaje" => "Debe seleccionar un cliente"
            ]);
            exit;
        }

        $respuesta = $cliente->eliminar($cedula);

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