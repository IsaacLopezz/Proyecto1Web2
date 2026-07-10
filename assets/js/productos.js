let tablaProductos;
let tablaModalCat = null;

$(document).ready(function () {
    listarProductos();

    $("#formProducto").on("submit", function (e) {
        e.preventDefault();
        guardarProducto();
    });

    // Inicializar tabla de categorías la primera vez que se abre el modal
    $("#modalCategorias").on("shown.bs.modal", function () {
        if (!tablaModalCat) {
            tablaModalCat = $("#tablaModalCat").DataTable({
                ajax: {
                    url: "../controlador/categoria_ajax.php?op=listar",
                    type: "GET",
                    dataSrc: "data"
                },
                columns: [
                    { data: "cod_categoria" },
                    { data: "nombre" }
                ],
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay categorías disponibles",
                    zeroRecords: "No se encontraron resultados",
                    paginate: {
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }
            });

            $("#tablaModalCat tbody").css("cursor", "pointer");
        } else {
            tablaModalCat.columns.adjust();
        }
    });

    // Autocompletado: busca en el <tbody> del modal al salir del campo
    $("#cod_categoria").on("blur", function () {
        let cod = $(this).val().trim();

        if (cod === "") {
            $("#nombre_categoria").val("");
            return;
        }

        let encontrado = false;

        $("#tablaModalCat tbody tr").each(function () {
            let codFila = $(this).find("td:eq(0)").text().trim();

            if (codFila === cod) {
                $("#nombre_categoria").val($(this).find("td:eq(1)").text().trim());
                encontrado = true;
                return false;
            }
        });

        if (!encontrado) {
            $("#nombre_categoria").val("");
            alert("Categoría no encontrada");
            $("#cod_categoria").val("").focus();
        }
    });

    // Click en fila del modal para seleccionar categoría
    $("#modalCategorias").on("click", "#tablaModalCat tbody tr", function () {
        if (!tablaModalCat) return;

        let datos = tablaModalCat.row(this).data();

        if (datos) {
            $("#cod_categoria").val(datos.cod_categoria);
            $("#nombre_categoria").val($("<div>").html(datos.nombre).text());
            bootstrap.Modal.getInstance(document.getElementById("modalCategorias")).hide();
        }
    });
});

function listarProductos() {
    tablaProductos = $("#tablaProductos").DataTable({
        ajax: {
            url: "../controlador/producto_ajax.php?op=listar",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "cod_producto" },
            { data: "nombre" },
            { data: "precio" },
            { data: "stock" },
            { data: "nombre_categoria" },
            { data: "opciones", orderable: false }
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

function guardarProducto() {
    let nombre       = $("#nombre").val().trim();
    let precio       = $("#precio").val().trim();
    let stock        = $("#stock").val().trim();
    let codCategoria = $("#cod_categoria").val().trim();

    if (nombre === "") {
        alert("El nombre del producto es obligatorio");
        return;
    }

    if (precio === "" || isNaN(parseFloat(precio)) || parseFloat(precio) < 0) {
        alert("El precio debe ser un número mayor o igual a cero");
        return;
    }

    if (!/^\d+$/.test(stock)) {
        alert("El stock debe ser un número entero mayor o igual a cero");
        return;
    }

    if (codCategoria === "") {
        alert("Debe seleccionar una categoría");
        return;
    }

    let datosFormulario = new FormData($("#formProducto")[0]);

    $.ajax({
        url: "../controlador/producto_ajax.php?op=guardar",
        type: "POST",
        data: datosFormulario,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                limpiarFormulario();
                tablaProductos.ajax.reload();
            }
        },
        error: function () {
            alert("Error al guardar el producto");
        }
    });
}

function editarProducto(cod) {
    $.ajax({
        url: "../controlador/producto_ajax.php?op=buscar",
        type: "POST",
        data: { cod_producto: cod },
        dataType: "json",
        success: function (respuesta) {
            if (respuesta.estado) {
                let d = respuesta.datos;

                $("#modo").val("editar");
                $("#cod_producto").val(d.cod_producto);
                $("#nombre").val(d.nombre);
                $("#precio").val(d.precio);
                $("#stock").val(d.stock);
                $("#cod_categoria").val(d.cod_categoria);
                $("#nombre_categoria").val(d.nombre_categoria);
            } else {
                alert(respuesta.mensaje);
            }
        },
        error: function () {
            alert("Error al buscar el producto");
        }
    });
}

function eliminarProducto(cod) {
    if (!confirm("¿Seguro que desea eliminar este producto?")) {
        return;
    }

    $.ajax({
        url: "../controlador/producto_ajax.php?op=eliminar",
        type: "POST",
        data: { cod_producto: cod },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                tablaProductos.ajax.reload();
            }
        },
        error: function () {
            alert("Error al eliminar el producto");
        }
    });
}

function limpiarFormulario() {
    $("#formProducto")[0].reset();
    $("#modo").val("agregar");
    $("#cod_producto").val("");
    $("#nombre_categoria").val("");
}

function abrirModalCategorias() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalCategorias")).show();
}
