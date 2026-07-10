<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f6fa;
        }

        .hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            color: white;
            padding: 70px 0 60px;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 2.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.75;
            max-width: 500px;
        }

        .hero .badge-curso {
            background-color: rgba(255,255,255,0.15);
            color: white;
            font-size: 0.78rem;
            letter-spacing: 1px;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 18px;
        }

        .modulo-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            height: 100%;
        }

        .modulo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.14);
        }

        .modulo-card .card-header {
            padding: 22px 24px 18px;
            border-bottom: none;
        }

        .modulo-card .card-header .icono {
            font-size: 2.2rem;
            display: block;
            margin-bottom: 10px;
            opacity: 0.95;
        }

        .modulo-card .card-header h5 {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.2px;
        }

        .modulo-card .card-body {
            padding: 18px 24px;
            background: white;
        }

        .modulo-card .card-body p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .modulo-card .btn {
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 20px;
        }

        .seccion-title {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #adb5bd;
            margin-bottom: 20px;
        }

        footer {
            background-color: #212529;
            color: #adb5bd;
            font-size: 0.85rem;
            padding: 22px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">Proyecto Web 2</a>

        <div class="navbar-nav">
            <a class="nav-link" href="vista/clientes.php">Clientes</a>
            <a class="nav-link" href="vista/categorias.php">Categorías</a>
            <a class="nav-link" href="vista/productos.php">Productos</a>
            <a class="nav-link" href="vista/factura.php">Facturas</a>
        </div>
    </div>
</nav>

<div class="hero">
    <div class="container">
        <span class="badge-curso">ITI-613 &nbsp;·&nbsp; Proyecto Web 2</span>
        <h1>Sistema de Facturación</h1>
        <p>Gestión de clientes, productos y facturas en un solo lugar.</p>
    </div>
</div>

<div class="container">
    <p class="seccion-title">Módulos del sistema</p>

    <div class="row g-4">

        <div class="col-md-6 col-lg-3">
            <div class="modulo-card card">
                <div class="card-header bg-primary text-white">
                    <span class="icono"><i class="bi bi-people-fill"></i></span>
                    <h5>Clientes</h5>
                </div>
                <div class="card-body">
                    <p>Registro y mantenimiento de clientes. Búsqueda por cédula, edición y eliminación lógica.</p>
                    <a href="vista/clientes.php" class="btn btn-primary w-100">Ir a Clientes</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="modulo-card card">
                <div class="card-header bg-success text-white">
                    <span class="icono"><i class="bi bi-tags-fill"></i></span>
                    <h5>Categorías</h5>
                </div>
                <div class="card-body">
                    <p>Administración de categorías de productos. Agregar, editar y desactivar categorías.</p>
                    <a href="vista/categorias.php" class="btn btn-success w-100">Ir a Categorías</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="modulo-card card">
                <div class="card-header bg-warning text-dark">
                    <span class="icono"><i class="bi bi-box-seam-fill"></i></span>
                    <h5>Productos</h5>
                </div>
                <div class="card-body">
                    <p>Control de inventario con precio y stock. Asignación de categoría con búsqueda integrada.</p>
                    <a href="vista/productos.php" class="btn btn-warning w-100">Ir a Productos</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="modulo-card card">
                <div class="card-header bg-danger text-white">
                    <span class="icono"><i class="bi bi-receipt-cutoff"></i></span>
                    <h5>Facturas</h5>
                </div>
                <div class="card-body">
                    <p>Emisión de facturas con detalle de productos, cálculo automático de totales y transacción segura.</p>
                    <a href="vista/factura.php" class="btn btn-danger w-100">Ir a Facturas</a>
                </div>
            </div>
        </div>

    </div>
</div>

<footer>
    <div class="container d-flex justify-content-between align-items-center">
        <span>Sistema de Facturación &mdash; Proyecto Web 2</span>
        <span>Base de datos: <code style="color:#adb5bd">proyecto1web2</code></span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
