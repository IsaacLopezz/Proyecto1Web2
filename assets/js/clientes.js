let tablaClientes;

$(document).ready(function () {
    listarClientes();

    $("#formCliente").on("submit", function (e) {
        e.preventDefault();
        guardarCliente();
    });
});

function listarClientes() {
    tablaClientes = $("#tablaClientes").DataTable({
        ajax: {
            url: "../controlador/cliente_ajax.php?op=listar",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "cedula" },
            { data: "nombre" },
            { data: "correo" },
            { data: "telefono" },
            { data: "opciones" }
        ],
        destroy: true,
        language: {
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "No hay registros disponibles",
            zeroRecords: "No se encontraron resultados",
            paginate: {
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });
}

function guardarCliente() {
    let cedula = $("#cedula").val().trim();
    let nombre = $("#nombre").val().trim();
    let telefono = $("#telefono").val().trim();
    let correo = $("#correo").val().trim();

    if (cedula === "" || nombre === "" || telefono === "") {
        alert("Cédula, nombre y teléfono son obligatorios");
        return;
    }

    if (!/^[0-9]+$/.test(telefono)) {
        alert("El teléfono solo debe contener números");
        return;
    }

    if (correo !== "" && !validarCorreo(correo)) {
        alert("El correo no tiene un formato válido");
        return;
    }

    let datosFormulario = new FormData($("#formCliente")[0]);

    $.ajax({
        url: "../controlador/cliente_ajax.php?op=guardar",
        type: "POST",
        data: datosFormulario,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                limpiarFormulario();
                tablaClientes.ajax.reload();
            }
        },
        error: function () {
            alert("Error al guardar el cliente");
        }
    });
}

function editarCliente(cedula) {
    $.ajax({
        url: "../controlador/cliente_ajax.php?op=buscar",
        type: "POST",
        data: { cedula: cedula },
        dataType: "json",
        success: function (respuesta) {
            if (respuesta.estado) {
                $("#modo").val("editar");
                $("#cedula").val(respuesta.datos.cedula);
                $("#nombre").val(respuesta.datos.nombre);
                $("#correo").val(respuesta.datos.correo);
                $("#telefono").val(respuesta.datos.telefono);

                $("#cedula").prop("readonly", true);
            } else {
                alert(respuesta.mensaje);
            }
        },
        error: function () {
            alert("Error al buscar el cliente");
        }
    });
}

function eliminarCliente(cedula) {
    let confirmar = confirm("¿Seguro que desea eliminar este cliente?");

    if (!confirmar) {
        return;
    }

    $.ajax({
        url: "../controlador/cliente_ajax.php?op=eliminar",
        type: "POST",
        data: { cedula: cedula },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                tablaClientes.ajax.reload();
            }
        },
        error: function () {
            alert("Error al eliminar el cliente");
        }
    });
}

function limpiarFormulario() {
    $("#formCliente")[0].reset();
    $("#modo").val("agregar");
    $("#cedula").prop("readonly", false);
}

function validarCorreo(correo) {
    let expresion = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return expresion.test(correo);
}