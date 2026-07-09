let tablaCategorias;

$(document).ready(function () {
    listarCategorias();

    $("#formCategoria").on("submit", function (e) {
        e.preventDefault();
        guardarCategoria();
    });
});

function listarCategorias() {
    tablaCategorias = $("#tablaCategorias").DataTable({
        ajax: {
            url: "../controlador/categoria_ajax.php?op=listar",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "cod_categoria" },
            { data: "nombre" },
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

function guardarCategoria() {
    let nombre = $("#nombre").val().trim();

    if (nombre === "") {
        alert("El nombre de la categoría es obligatorio");
        return;
    }

    let datosFormulario = new FormData($("#formCategoria")[0]);

    $.ajax({
        url: "../controlador/categoria_ajax.php?op=guardar",
        type: "POST",
        data: datosFormulario,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                limpiarFormulario();
                tablaCategorias.ajax.reload();
            }
        },
        error: function () {
            alert("Error al guardar la categoría");
        }
    });
}

function editarCategoria(cod) {
    $.ajax({
        url: "../controlador/categoria_ajax.php?op=buscar",
        type: "POST",
        data: { cod_categoria: cod },
        dataType: "json",
        success: function (respuesta) {
            if (respuesta.estado) {
                $("#modo").val("editar");
                $("#cod_categoria").val(respuesta.datos.cod_categoria);
                $("#nombre").val(respuesta.datos.nombre);
            } else {
                alert(respuesta.mensaje);
            }
        },
        error: function () {
            alert("Error al buscar la categoría");
        }
    });
}

function eliminarCategoria(cod) {
    let confirmar = confirm("¿Seguro que desea eliminar esta categoría?");

    if (!confirmar) {
        return;
    }

    $.ajax({
        url: "../controlador/categoria_ajax.php?op=eliminar",
        type: "POST",
        data: { cod_categoria: cod },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                tablaCategorias.ajax.reload();
            }
        },
        error: function () {
            alert("Error al eliminar la categoría");
        }
    });
}

function limpiarFormulario() {
    $("#formCategoria")[0].reset();
    $("#modo").val("agregar");
    $("#cod_categoria").val("");
}
