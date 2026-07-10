<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mantenimiento de Clientes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Proyecto Web 2</a>

        <div class="navbar-nav">
            <a class="nav-link active" href="clientes.php">Clientes</a>
            <a class="nav-link" href="categorias.php">Categorías</a>
            <a class="nav-link" href="productos.php">Productos</a>
            <a class="nav-link" href="factura.php">Facturas</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4">Mantenimiento de Clientes</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Datos del Cliente
        </div>

        <div class="card-body">
            <form id="formCliente">
                <input type="hidden" id="modo" name="modo" value="agregar">

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="cedula" class="form-label">Cédula</label>
                        <input type="text" class="form-control" id="cedula" name="cedula">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">Limpiar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            Listado de Clientes
        </div>

        <div class="card-body">
            <table id="tablaClientes" class="display table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Opciones</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="../assets/js/clientes.js"></script>

</body>
</html>