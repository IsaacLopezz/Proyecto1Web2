<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Proyecto Web 2</a>

        <div class="navbar-nav">
            <a class="nav-link" href="clientes.php">Clientes</a>
            <a class="nav-link" href="categorias.php">Categorías</a>
            <a class="nav-link" href="productos.php">Productos</a>
            <a class="nav-link active" href="factura.php">Facturas</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4">Sistema de Facturación</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Encabezado de Factura
        </div>

        <div class="card-body">
            <form id="formFactura">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="cedula_cliente" class="form-label">Cédula Cliente</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cedula_cliente" name="cedula_cliente">
                            <button type="button" class="btn btn-success" onclick="abrirModalClientes()">🔍</button>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nombre_cliente" class="form-label">Nombre Cliente</label>
                        <input type="text" class="form-control" id="nombre_cliente" readonly>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            Agregar Productos
        </div>

        <div class="card-body">
            <input type="hidden" id="indice_detalle" value="-1">

            <div class="row">
                <div class="col-md-2 mb-3">
                    <label for="codigo_producto" class="form-label">Código</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="codigo_producto">
                        <button type="button" class="btn btn-success" onclick="abrirModalProductos()">🔍</button>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="nombre_producto" class="form-label">Producto</label>
                    <input type="text" class="form-control" id="nombre_producto" readonly>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="precio_producto" class="form-label">Precio</label>
                    <input type="text" class="form-control" id="precio_producto" readonly>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="stock_producto" class="form-label">Stock</label>
                    <input type="text" class="form-control" id="stock_producto" readonly>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="cantidad_producto" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad_producto" min="1">
                </div>

                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="btnAgregarProducto" onclick="agregarProductoDetalle()">
                        +
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            Detalle de Factura
        </div>

        <div class="card-body">
            <table id="tablaDetalle" class="display table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Opciones</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-8"></div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>Total: ₡<span id="total_general">0.00</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success mt-3" onclick="guardarFactura()">
                Guardar Factura
            </button>

            <button type="button" class="btn btn-secondary mt-3" onclick="limpiarFactura()">
                Limpiar
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">
            Facturas Guardadas
        </div>

        <div class="card-body">
            <table id="tablaFacturas" class="display table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Fecha</th>
                        <th>Cédula</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Opciones</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClientes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table id="tablaBuscarClientes" class="display table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Seleccionar</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProductos" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table id="tablaBuscarProductos" class="display table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Seleccionar</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleFactura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table id="tablaVerDetalleFactura" class="display table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/factura.js"></script>

</body>
</html>