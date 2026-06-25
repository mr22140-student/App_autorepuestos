<?php
include("conexion.php");
$resultado = mysqli_query($conn, "SELECT * FROM cliente");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .form-control-dark { background-color: #333; border: 1px solid #444; color: #fff; }
        .form-control-dark:focus { background-color: #3a3a3a; color: #fff; border-color: #ffc107; box-shadow: none; }
        .nav-link-custom { color: #e0e0e0; text-decoration: none; font-size: 0.9rem; font-weight: 500; padding: 6px 10px; border-radius: 4px; transition: all 0.2s ease; }
        .nav-link-custom:hover { color: #ffc107; background-color: rgba(255, 193, 7, 0.05); }
        .nav-dropdown-btn { font-size: 0.88rem !important; padding: 5px 12px !important; border-radius: 4px !important; box-shadow: none !important; }
        .custom-dropdown-ul { background-color: #262626 !important; border: 1px solid #444 !important; padding: 6px 0 !important; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .custom-dropdown-ul .dropdown-item { font-size: 0.9rem !important; padding: 7px 16px !important; color: #ffffff !important; transition: background 0.15s ease; }
        .custom-dropdown-ul .dropdown-item:hover { background-color: #ffc107 !important; color: #000000 !important; font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar-custom" style="background-color: #262626; padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333;">
        <span class="navbar-title" style="font-weight: bold; font-size: 1.35rem; color: #ffc107; letter-spacing: 0.5px;">
            ERP Auto Repuestos
        </span>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="index.php" class="nav-link-custom">Inicio</a>
            <a href="productos.php" class="nav-link-custom">Productos</a>
            <a href="clientes.php" class="nav-link-custom" style="color: #ffc107; font-weight: bold;">Clientes</a>
            <a href="ventas.php" class="nav-link-custom">Ventas</a>
            <a href="compras.php" class="nav-link-custom">Compras</a>
            <a href="catalogo.php" class="nav-link-custom">Catálogo y Manual</a>
            
            <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold nav-dropdown-btn" type="button" id="dropContabilidad" data-bs-toggle="dropdown" aria-expanded="false">
                    📖 Contabilidad
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropContabilidad">
                    <li><a class="dropdown-item" href="librodiario.php">Libro Diario</a></li>
                    <li><a class="dropdown-item" href="libromayor.php">Libro Mayor</a></li>
                    <li><a class="dropdown-item" href="razones.php">Razones Financieras</a></li>
                </ul>
            </div>

            <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-warning btn-sm dropdown-toggle fw-bold text-dark nav-dropdown-btn" type="button" id="dropReportes" data-bs-toggle="dropdown" aria-expanded="false">
                    📊 Estados y Reportes
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropReportes">
                    <li><a class="dropdown-item" href="balance_comprobacion.php">Balance de Comprobación</a></li>
                    <li><a class="dropdown-item" href="balance_general.php">Balance General</a></li>
                    <li><a class="dropdown-item" href="analisis_financiero.php">Análisis H/V</a></li>
                    <li><hr class="dropdown-divider" style="border-color: #444;"></li>
                    <li><a class="dropdown-item" href="reportes_financieros.php">Reportes Financieros</a></li>
                    <li><a class="dropdown-item" href="reportes.php">Módulo de Reportes</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main container py-4">
        <div class="card-custom">
            <h2 class="mb-4 text-warning">Clientes</h2>

            <form action="agregar_cliente.php" method="POST">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="nombre" class="form-control form-control-dark" placeholder="Nombre" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="apellido" class="form-control form-control-dark" placeholder="Apellido" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="telefono" class="form-control form-control-dark" placeholder="Teléfono">
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="correo" class="form-control form-control-dark" placeholder="Correo">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning fw-bold text-dark mb-4">Agregar Cliente</button>
            </form>

            <div class="row">
                <?php while($fila = mysqli_fetch_assoc($resultado)){ ?>
                <div class="col-md-6 mb-3 pb-2 border-bottom border-secondary">
                    <strong>
                        <?php echo htmlspecialchars($fila['nombre']." ".$fila['apellido'], ENT_QUOTES, 'UTF-8'); ?>
                    </strong>
                    <br>
                    <span class="text-white-50 small">
                        📞 <?php echo !empty($fila['telefono']) ? htmlspecialchars($fila['telefono'], ENT_QUOTES, 'UTF-8') : 'Sin teléfono'; ?> | 
                        ✉ <?php echo !empty($fila['correo']) ? htmlspecialchars($fila['correo'], ENT_QUOTES, 'UTF-8') : 'Sin correo'; ?>
                    </span>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>