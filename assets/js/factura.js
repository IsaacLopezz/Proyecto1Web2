let detalles = [];
let tablaFacturas;
let tablaBuscarClientes  = null;
let tablaBuscarProductos = null;

$(document).ready(function () {
    $("#fecha").val(obtenerFechaActual());

    listarFacturas();
    renderizarDetalle();

    // Inicializar tabla de clientes la primera vez que se abre el modal
    $("#modalClientes").on("shown.bs.modal", function () {
        if (!tablaBuscarClientes) {
            tablaBuscarClientes = $("#tablaBuscarClientes").DataTable({
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
                language: lenguajeTabla()
            });
        } else {
            tablaBuscarClientes.columns.adjust();
        }
    });

    // Inicializar tabla de productos la primera vez que se abre el modal
    $("#modalProductos").on("shown.bs.modal", function () {
        if (!tablaBuscarProductos) {
            tablaBuscarProductos = $("#tablaBuscarProductos").DataTable({
                ajax: {
                    url: "../controlador/factura_ajax.php?op=listarProductos",
                    type: "GET",
                    dataSrc: "data"
                },
                columns: [
                    { data: "cod_producto" },
                    { data: "nombre" },
                    { data: "precio" },
                    { data: "stock" },
                    { data: "categoria" },
                    { data: "opciones" }
                ],
                language: lenguajeTabla()
            });
        } else {
            tablaBuscarProductos.columns.adjust();
        }
    });

    // Autocompletar cédula: busca en el <tbody> de la tabla de clientes
    $("#cedula_cliente").on("blur", function () {
        let cedula = $(this).val().trim();

        if (cedula === "") {
            $("#nombre_cliente").val("");
            return;
        }

        let encontrado = false;

        $("#tablaBuscarClientes tbody tr").each(function () {
            let cedulaFila = $(this).find("td:eq(0)").text().trim();

            if (cedulaFila === cedula) {
                $("#nombre_cliente").val($(this).find("td:eq(1)").text().trim());
                encontrado = true;
                return false;
            }
        });

        if (!encontrado) {
            $("#nombre_cliente").val("");
            alert("Cliente no encontrado");
        }
    });

    // Autocompletar código producto: busca en el <tbody> de la tabla de productos
    $("#codigo_producto").on("blur", function () {
        let cod = $(this).val().trim();

        if (cod === "") {
            limpiarProducto();
            return;
        }

        let encontrado = false;

        $("#tablaBuscarProductos tbody tr").each(function () {
            let codFila = $(this).find("td:eq(0)").text().trim();

            if (codFila === cod) {
                $("#nombre_producto").val($(this).find("td:eq(1)").text().trim());
                $("#precio_producto").val(parseFloat($(this).find("td:eq(2)").text().trim()).toFixed(2));
                $("#stock_producto").val($(this).find("td:eq(3)").text().trim());
                encontrado = true;
                return false;
            }
        });

        if (!encontrado) {
            limpiarProducto();
            alert("Producto no encontrado");
        }
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

function abrirModalClientes() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalClientes")).show();
}

function seleccionarCliente(cedula, nombre) {
    $("#cedula_cliente").val(cedula);
    $("#nombre_cliente").val(nombre);
    bootstrap.Modal.getInstance(document.getElementById("modalClientes")).hide();
}

function abrirModalProductos() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalProductos")).show();
}

function seleccionarProducto(cod, nombre, precio, stock) {
    $("#codigo_producto").val(cod);
    $("#nombre_producto").val(nombre);
    $("#precio_producto").val(parseFloat(precio).toFixed(2));
    $("#stock_producto").val(stock);
    $("#cantidad_producto").val(1);
    bootstrap.Modal.getInstance(document.getElementById("modalProductos")).hide();
}

function agregarProductoDetalle() {
    let cod      = $("#codigo_producto").val().trim();
    let nombre   = $("#nombre_producto").val().trim();
    let precio   = parseFloat($("#precio_producto").val());
    let stock    = parseInt($("#stock_producto").val());
    let cantidad = parseInt($("#cantidad_producto").val());
    let indice   = parseInt($("#indice_detalle").val());

    if (cod === "" || nombre === "" || isNaN(precio)) {
        alert("Debe seleccionar un producto válido");
        return;
    }

    if (isNaN(cantidad) || cantidad <= 0) {
        alert("La cantidad debe ser mayor a cero");
        return;
    }

    if (cantidad > stock) {
        alert("La cantidad no puede ser mayor al stock disponible (" + stock + ")");
        return;
    }

    let subtotal = precio * cantidad;

    let item = {
        cod_producto:    cod,
        nombre_producto: nombre,
        precio:          precio,
        stock:           stock,
        cantidad:        cantidad,
        subtotal:        subtotal
    };

    if (indice >= 0) {
        detalles[indice] = item;
        $("#indice_detalle").val("-1");
        $("#btnAgregarProducto").text("+");
        $("#codigo_producto").prop("readonly", false);
    } else {
        let existe = detalles.findIndex(function (d) { return d.cod_producto === cod; });

        if (existe >= 0) {
            let nuevaCantidad = detalles[existe].cantidad + cantidad;

            if (nuevaCantidad > stock) {
                alert("La cantidad total supera el stock disponible (" + stock + ")");
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
                <td>${item.cod_producto}</td>
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
    $("#codigo_producto").val(item.cod_producto);
    $("#nombre_producto").val(item.nombre_producto);
    $("#precio_producto").val(item.precio.toFixed(2));
    $("#stock_producto").val(item.stock);
    $("#cantidad_producto").val(item.cantidad);

    $("#codigo_producto").prop("readonly", true);
    $("#btnAgregarProducto").text("✓");
}

function eliminarDetalle(index) {
    if (!confirm("¿Desea eliminar este producto del detalle?")) {
        return;
    }

    detalles.splice(index, 1);
    renderizarDetalle();
}

function limpiarProducto() {
    $("#codigo_producto").val("").prop("readonly", false);
    $("#nombre_producto").val("");
    $("#precio_producto").val("");
    $("#stock_producto").val("");
    $("#cantidad_producto").val("");
    $("#indice_detalle").val("-1");
    $("#btnAgregarProducto").text("+");
}

function guardarFactura() {
    let fecha          = $("#fecha").val();
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
        alert("Debe agregar al menos un producto a la factura");
        return;
    }

    $.ajax({
        url: "../controlador/factura_ajax.php?op=guardar",
        type: "POST",
        data: {
            fecha:          fecha,
            cedula_cliente: cedula_cliente,
            detalles:       JSON.stringify(detalles)
        },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                limpiarFactura();
                tablaFacturas.ajax.reload();
                if (tablaBuscarProductos) {
                    tablaBuscarProductos.ajax.reload();
                }
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
    detalles = [];
    limpiarProducto();
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
            { data: "num_factura" },
            { data: "fecha" },
            { data: "cedula_cliente" },
            { data: "cliente" },
            { data: "total" },
            { data: "opciones", orderable: false }
        ],
        destroy: true,
        language: lenguajeTabla()
    });
}

function verFactura(num_factura) {
    $.ajax({
        url: "../controlador/factura_ajax.php?op=detalleFactura",
        type: "POST",
        data: { num_factura: num_factura },
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
                        <td>${item.cod_producto}</td>
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
                paging: false,
                searching: false,
                info: false,
                language: lenguajeTabla()
            });

            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalDetalleFactura")).show();
        },
        error: function () {
            alert("Error al cargar el detalle de la factura");
        }
    });
}

function anularFactura(num_factura) {
    if (!confirm("¿Seguro que desea anular esta factura? Se devolverá el stock de los productos.")) {
        return;
    }

    $.ajax({
        url: "../controlador/factura_ajax.php?op=anularFactura",
        type: "POST",
        data: { num_factura: num_factura },
        dataType: "json",
        success: function (respuesta) {
            alert(respuesta.mensaje);

            if (respuesta.estado) {
                tablaFacturas.ajax.reload();
                if (tablaBuscarProductos) {
                    tablaBuscarProductos.ajax.reload();
                }
            }
        },
        error: function () {
            alert("Error al anular la factura");
        }
    });
}
