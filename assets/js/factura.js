let detalles = [];
let tablaFacturas;

$(document).ready(function () {
    $("#fecha").val(obtenerFechaActual());

    listarFacturas();
    renderizarDetalle();

    $("#cedula_cliente").on("blur", function () {
        buscarClientePorCedula();
    });

    $("#codigo_producto").on("blur", function () {
        buscarProductoPorCodigo();
    });
});

function obtenerFechaActual() {
    let fecha = new Date();
    return fecha.toISOString().split("T")[0];
}

function lenguajeTabla() {
    return {
        search: "Buscar:",
        lengthMenu: "Mostrar _MENU_ registros",
        info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
        infoEmpty: "No hay registros disponibles",
        zeroRecords: "No se encontraron resultados",
        paginate: {
            next: "Siguiente",
            previous: "Anterior"
        }
    };
}

function buscarClientePorCedula() {
    let cedula = $("#cedula_cliente").val().trim();

    if (cedula === "") {
        $("#nombre_cliente").val("");
        return;
    }

    $.ajax({
        url: "../controlador/factura_ajax.php?op=buscarCliente",
        type: "POST",
        data: { cedula: cedula },
        dataType: "json",
        success: function (respuesta) {
            if (respuesta.estado) {
                $("#nombre_cliente").val(respuesta.datos.nombre);
            } else {
                $("#nombre_cliente").val("");
                alert(respuesta.mensaje);
            }
        },
        error: function () {
            alert("Error al buscar el cliente");
        }
    });
}

function abrirModalClientes() {
    $("#tablaBuscarClientes").DataTable({
        ajax: {
            url: "../controlador/factura_ajax.php?op=listarClientes",
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
        language: lenguajeTabla()
    });

    let modal = new bootstrap.Modal(document.getElementById("modalClientes"));
    modal.show();
}

function seleccionarCliente(cedula, nombre) {
    $("#cedula_cliente").val(cedula);
    $("#nombre_cliente").val(nombre);

    let modal = bootstrap.Modal.getInstance(document.getElementById("modalClientes"));
    modal.hide();
}

function buscarProductoPorCodigo() {
    let codigo = $("#codigo_producto").val().trim();

    if (codigo === "") {
        limpiarProducto();
        return;
    }

    $.ajax({
        url: "../controlador/factura_ajax.php?op=buscarProducto",
        type: "POST",
        data: { codigo: codigo },
        dataType: "json",
        success: function (respuesta) {
            if (respuesta.estado) {
                $("#codigo_producto").val(respuesta.datos.codigo);
                $("#nombre_producto").val(respuesta.datos.nombre);
                $("#precio_producto").val(parseFloat(respuesta.datos.precio).toFixed(2));
                $("#stock_producto").val(respuesta.datos.stock);
            } else {
                limpiarProducto();
                alert(respuesta.mensaje);
            }
        },
        error: function () {
            alert("Error al buscar el producto");
        }
    });
}

function abrirModalProductos() {
    $("#tablaBuscarProductos").DataTable({
        ajax: {
            url: "../controlador/factura_ajax.php?op=listarProductos",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "codigo" },
            { data: "nombre" },
            { data: "precio" },
            { data: "stock" },
            { data: "categoria" },
            { data: "opciones" }
        ],
        destroy: true,
        language: lenguajeTabla()
    });

    let modal = new bootstrap.Modal(document.getElementById("modalProductos"));
    modal.show();
}

function seleccionarProducto(codigo, nombre, precio, stock) {
    $("#codigo_producto").val(codigo);
    $("#nombre_producto").val(nombre);
    $("#precio_producto").val(parseFloat(precio).toFixed(2));
    $("#stock_producto").val(stock);
    $("#cantidad_producto").val(1);

    let modal = bootstrap.Modal.getInstance(document.getElementById("modalProductos"));
    modal.hide();
}

function agregarProductoDetalle() {
    let codigo = $("#codigo_producto").val().trim();
    let nombre = $("#nombre_producto").val().trim();
    let precio = parseFloat($("#precio_producto").val());
    let stock = parseInt($("#stock_producto").val());
    let cantidad = parseInt($("#cantidad_producto").val());
    let indice = parseInt($("#indice_detalle").val());

    if (codigo === "" || nombre === "" || isNaN(precio)) {
        alert("Debe seleccionar un producto válido");
        return;
    }

    if (isNaN(cantidad) || cantidad <= 0) {
        alert("La cantidad debe ser mayor a cero");
        return;
    }

    if (cantidad > stock) {
        alert("La cantidad no puede ser mayor al stock disponible");
        return;
    }

    let subtotal = precio * cantidad;

    let item = {
        codigo_producto: codigo,
        nombre_producto: nombre,
        precio: precio,
        stock: stock,
        cantidad: cantidad,
        subtotal: subtotal
    };

    if (indice >= 0) {
        detalles[indice] = item;
        $("#indice_detalle").val("-1");
        $("#btnAgregarProducto").text("+");
        $("#codigo_producto").prop("readonly", false);
    } else {
        let existe = detalles.findIndex(detalle => detalle.codigo_producto === codigo);

        if (existe >= 0) {
            let nuevaCantidad = detalles[existe].cantidad + cantidad;

            if (nuevaCantidad > stock) {
                alert("La cantidad total supera el stock disponible");
                return;
            }

            detalles[existe].cantidad = nuevaCantidad;
            detalles[existe].subtotal = detalles[existe].precio * nuevaCantidad;
        } else {
            detalles.push(item);
        }
    }

    limpiarProducto();
    renderizarDetalle();
}

function renderizarDetalle() {
    if ($.fn.DataTable.isDataTable("#tablaDetalle")) {
        $("#tablaDetalle").DataTable().destroy();
    }

    let tbody = $("#tablaDetalle tbody");
    tbody.empty();

    let total = 0;

    detalles.forEach(function (item, index) {
        total += item.subtotal;

        let fila = `
            <tr>
                <td>${item.codigo_producto}</td>
                <td>${item.nombre_producto}</td>
                <td>${item.precio.toFixed(2)}</td>
                <td>${item.cantidad}</td>
                <td>${item.subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editarDetalle(${index})">
                        Editar
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarDetalle(${index})">
                        Eliminar
                    </button>
                </td>
            </tr>
        `;

        tbody.append(fila);
    });

    $("#total_general").text(total.toFixed(2));

    $("#tablaDetalle").DataTable({
        destroy: true,
        paging: false,
        searching: false,
        info: false,
        language: lenguajeTabla()
    });
}

function editarDetalle(index) {
    let item = detalles[index];

    $("#indice_detalle").val(index);
    $("#codigo_producto").val(item.codigo_producto);
    $("#nombre_producto").val(item.nombre_producto);
    $("#precio_producto").val(item.precio.toFixed(2));
    $("#stock_producto").val(item.stock);
    $("#cantidad_producto").val(item.cantidad);

    $("#codigo_producto").prop("readonly", true);
    $("#btnAgregarProducto").text("✓");
}

function eliminarDetalle(index) {
    let confirmar = confirm("¿Desea eliminar este producto del detalle?");

    if (!confirmar) {
        return;
    }

    detalles.splice(index, 1);
    renderizarDetalle();
}

function limpiarProducto() {
    $("#codigo_producto").val("");
    $("#nombre_producto").val("");
    $("#precio_producto").val("");
    $("#stock_producto").val("");
    $("#cantidad_producto").val("");
    $("#indice_detalle").val("-1");
    $("#codigo_producto").prop("readonly", false);
    $("#btnAgregarProducto").text("+");
}

function guardarFactura() {
    let fecha = $("#fecha").val();
    let cedula_cliente = $("#cedula_cliente").val().trim();
    let nombre_cliente = $("#nombre_cliente").val().trim();

    if (fecha === "") {
        alert("Debe seleccionar la fecha");
        return;
    }

    if (cedula_cliente === "" || nombre_cliente === "") {
        alert("Debe seleccionar un cliente válido");
        return;
    }

    if (detalles.length === 0) {
        alert("Debe agregar productos a la factura");
        return;
    }

    $.ajax({
        url: "../controlador/factura_ajax.php?op=guardar",
        type: "POST",
        data: {
            fecha: fecha,
            cedula_cliente: cedula_cliente,
            detalles: JSON.stringify(detalles)
        },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                limpiarFactura();
                tablaFacturas.ajax.reload();
            }
        },
        error: function () {
            alert("Error al guardar la factura");
        }
    });
}

function limpiarFactura() {
    $("#fecha").val(obtenerFechaActual());
    $("#cedula_cliente").val("");
    $("#nombre_cliente").val("");

    limpiarProducto();

    detalles = [];
    renderizarDetalle();
}

function listarFacturas() {
    tablaFacturas = $("#tablaFacturas").DataTable({
        ajax: {
            url: "../controlador/factura_ajax.php?op=listarFacturas",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: "id" },
            { data: "fecha" },
            { data: "cedula_cliente" },
            { data: "cliente" },
            { data: "total" },
            { data: "opciones" }
        ],
        destroy: true,
        language: lenguajeTabla()
    });
}

function verFactura(id_factura) {
    $.ajax({
        url: "../controlador/factura_ajax.php?op=detalleFactura",
        type: "POST",
        data: { id_factura: id_factura },
        dataType: "json",
        success: function (respuesta) {
            if (!respuesta.estado) {
                alert(respuesta.mensaje);
                return;
            }

            if ($.fn.DataTable.isDataTable("#tablaVerDetalleFactura")) {
                $("#tablaVerDetalleFactura").DataTable().destroy();
            }

            let tbody = $("#tablaVerDetalleFactura tbody");
            tbody.empty();

            respuesta.data.forEach(function (item) {
                let fila = `
                    <tr>
                        <td>${item.codigo_producto}</td>
                        <td>${item.producto}</td>
                        <td>${item.precio}</td>
                        <td>${item.cantidad}</td>
                        <td>${item.subtotal}</td>
                    </tr>
                `;

                tbody.append(fila);
            });

            $("#tablaVerDetalleFactura").DataTable({
                destroy: true,
                language: lenguajeTabla()
            });

            let modal = new bootstrap.Modal(document.getElementById("modalDetalleFactura"));
            modal.show();
        },
        error: function () {
            alert("Error al cargar el detalle de la factura");
        }
    });
}

function anularFactura(id_factura) {
    let confirmar = confirm("¿Seguro que desea anular esta factura? Se devolvera el stock de los productos.");

    if (!confirmar) {
        return;
    }

    $.ajax({
        url: "../controlador/factura_ajax.php?op=anularFactura",
        type: "POST",
        data: { id_factura: id_factura },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                tablaFacturas.ajax.reload();
            }
        },
        error: function () {
            alert("Error al anular la factura");
        }
    });
}