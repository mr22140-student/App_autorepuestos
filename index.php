<?php
// 1. Incluimos la conexión a la base de datos
include("conexion.php");

// 2. Traemos la consulta de los clientes
$resultado_clientes = mysqli_query($conn, "SELECT * FROM cliente");

// 3. Extraer métricas rápidas financieras para el panel de inicio
$ventas_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total), 0) AS total FROM venta"));
$totalVentas = floatval($ventas_q['total']);

$compras_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total), 0) AS total FROM compra"));
$totalCompras = floatval($compras_q['total']);

$productos_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(stock), 0) AS unidades FROM producto"));
$totalStock = intval($productos_q['unidades']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - ERP Auto Repuestos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .text-warning-custom { color: #ffc107 !important; }
        .metric-box { border-left: 4px solid #ffc107; background: #1f1f1f; padding: 20px; border-radius: 6px; text-align: center; }
        .panel-icon { font-size: 2rem; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="navbar-custom">
        <span class="navbar-title">ERP Auto Repuestos</span>
        <div>
            <a href="index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="clientes.php">Clientes</a>
            <a href="ventas.php">Ventas</a>
            <a href="compras.php">Compras</a>
            <a href="librodiario.php">Libro Diario</a>
            <a href="libromayor.php">Libro Mayor</a>
            <a href="catalogo.php">Catálogo y Manual</a>
            <a href="razones.php">Razones Financieras</a>
            <a href="balance_general.php">Balance General</a>
            <a href="balance_comprobacion.php">Balance Comprobación</a>
            <a href="analisis_financiero.php">Análisis H/V</a>
            <a href="reportes_financieros.php">Reportes Financieros</a>
            <a href="reportes.php">Reportes</a>
        </div>
    </div>

    <div class="main container py-4">
        <div class="card-custom text-center py-4 mb-4">
            <h1 class="text-warning-custom display-5 fw-bold mb-2">Panel de Control Principal</h1>
            <p class="lead text-white-50">Sistema integrado de inventario, facturación multimétodo y contabilidad automatizada.</p>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="ventas.php" class="btn btn-warning px-4 fw-bold">Registrar Venta</a>
                <a href="compras.php" class="btn btn-outline-light px-4">Registrar Compra</a>
                <a href="librodiario.php" class="btn btn-dark px-4 border-secondary">Ver Libro Diario</a>
            </div>
        </div>

        <h3 class="mb-3 text-warning">Resumen de Reportes Financieros</h3>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="metric-box" style="border-left-color: #2cba00;">
                    <div class="panel-icon">💰</div>
                    <span class="text-white-50 small d-block">VENTAS ACUMULADAS</span>
                    <h2 class="text-success fw-bold my-1">$<?php echo number_format($totalVentas, 2); ?></h2>
                    <a href="reportes.php" class="btn btn-sm btn-outline-success mt-2 w-100">Ver Gráficas de Rendimiento</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="metric-box" style="border-left-color: #dc3545;">
                    <div class="panel-icon">🛒</div>
                    <span class="text-white-50 small d-block">INVERSIÓN EN COMPRAS</span>
                    <h2 class="text-danger fw-bold my-1">$<?php echo number_format($totalCompras, 2); ?></h2>
                    <a href="compras.php" class="btn btn-sm btn-outline-danger mt-2 w-100">Ver Historial de Compras</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="metric-box" style="border-left-color: #0dcaf0;">
                    <div class="panel-icon">📦</div>
                    <span class="text-white-50 small d-block">STOCK EN INVENTARIO</span>
                    <h2 class="text-info fw-bold my-1"><?php echo $totalStock; ?> <span class="fs-5 text-white-50">Uds.</span></h2>
                    <a href="productos.php" class="btn btn-sm btn-outline-info mt-2 w-100">Ver Catálogo de Repuestos</a>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <h3 class="mb-4 text-warning">Nuestros Clientes Registrados</h3>
            <div class="row">
                <?php 
                if(mysqli_num_rows($resultado_clientes) > 0) {
                    while($fila = mysqli_fetch_assoc($resultado_clientes)){ 
                ?>
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
                <?php 
                    } 
                } else {
                    echo "<p class='text-muted ps-3'>No hay clientes registrados en el sistema todavía.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>