<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mantenimiento de Productos</title>

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
            <a class="nav-link active" href="productos.php">Productos</a>
            <a class="nav-link" href="factura.php">Facturas</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4">Mantenimiento de Productos</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Datos del Producto
        </div>

        <div class="card-body">
            <form id="formProducto">
                <input type="hidden" id="modo" name="modo" value="agregar">
                <input type="hidden" id="cod_producto" name="cod_producto" value="">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" step="1">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="cod_categoria" class="form-label">Cód. Categoría</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="cod_categoria" name="cod_categoria">
                            <button type="button" class="btn btn-outline-secondary" onclick="abrirModalCategorias()" title="Buscar categoría">
                                &#128269;
                            </button>
                        </div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <label for="nombre_categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="nombre_categoria" readonly>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">Limpiar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            Listado de Productos
        </div>

        <div class="card-body">
            <table id="tablaProductos" class="display table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Opciones</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para buscar categoría -->
<div class="modal fade" id="modalCategorias" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table id="tablaModalCat" class="display table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
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

<script src="../assets/js/productos.js"></script>

</body>
</html>
