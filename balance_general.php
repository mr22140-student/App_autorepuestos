<?php
include("conexion.php");

$query_saldos = "
    SELECT c.codigo, c.nombre, c.tipo,
    (IFNULL(SUM(l.debe), 0) - IFNULL(SUM(l.haber), 0)) AS saldo_deudor,
    (IFNULL(SUM(l.haber), 0) - IFNULL(SUM(l.debe), 0)) AS saldo_acreedor
    FROM catalogo_cuentas c
    LEFT JOIN libro_diario l ON c.id = l.cuenta_id
    GROUP BY c.id
    HAVING SUM(l.debe) > 0 OR SUM(l.haber) > 0
    ORDER BY c.codigo ASC
";
$resultado = mysqli_query($conn, $query_saldos);

$activos = []; $pasivos = []; $patrimonio = [];
$total_activos = 0; $total_pasivos = 0; $total_patrimonio = 0;

if ($resultado) {
    while ($row = mysqli_fetch_assoc($resultado)) {
        if ($row['tipo'] == 'ACTIVO') {
            $saldo = floatval($row['saldo_deudor']);
            if($saldo != 0) { $activos[] = $row; $total_activos += $saldo; }
        } elseif ($row['tipo'] == 'PASIVO') {
            $saldo = floatval($row['saldo_acreedor']);
            if($saldo != 0) { $pasivos[] = $row; $total_pasivos += $saldo; }
        } elseif (in_array($row['tipo'], ['PATRIMONIO', 'CAPITAL'])) {
            $saldo = floatval($row['saldo_acreedor']);
            if($saldo != 0) { $patrimonio[] = $row; $total_patrimonio += $saldo; }
        }
    }
}
$total_pasivo_mas_patrimonio = $total_pasivos + $total_patrimonio;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance General - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-top: 30px; }
        .text-warning-custom { color: #ffc107 !important; }
        .table-custom { color: #ffffff; background-color: #262626; }
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
            <a href="clientes.php" class="nav-link-custom">Clientes</a>
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
                    <li><a class="dropdown-item fw-bold text-warning" href="balance_general.php">Balance General</a></li>
                    <li><a class="dropdown-item" href="analisis_financiero.php">Análisis H/V</a></li>
                    <li><hr class="dropdown-divider" style="border-color: #444;"></li>
                    <li><a class="dropdown-item" href="reportes_financieros.php">Reportes Financieros</a></li>
                    <li><a class="dropdown-item" href="reportes.php">Módulo de Reportes</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="card-custom">
            <h2 class="text-warning-custom text-center mb-4">Balance General Real-Time</h2>
            <div class="row">
                <div class="col-md-6 border-end border-secondary">
                    <h5 class="text-info mb-3">1. ACTIVOS</h5>
                    <table class="table table-custom table-striped border">
                        <tbody>
                            <?php foreach($activos as $a) { ?>
                                <tr><td><?php echo $a['nombre']; ?></td><td class="text-end">$<?php echo number_format($a['saldo_deudor'],2); ?></td></tr>
                            <?php } ?>
                            <tr class="table-dark text-info fw-bold"><td>TOTAL ACTIVOS:</td><td class="text-end">$<?php echo number_format($total_activos,2); ?></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="text-danger mb-3">2. PASIVOS Y PATRIMONIO</h5>
                    <table class="table table-custom table-striped border">
                        <tbody>
                            <?php foreach($pasivos as $p) { ?>
                                <tr><td><?php echo $p['nombre']; ?></td><td class="text-end">$<?php echo number_format($p['saldo_acreedor'],2); ?></td></tr>
                            <?php } foreach($patrimonio as $pa) { ?>
                                <tr><td><?php echo $pa['nombre']; ?></td><td class="text-end">$<?php echo number_format($pa['saldo_acreedor'],2); ?></td></tr>
                            <?php } ?>
                            <tr class="table-dark text-warning fw-bold"><td>TOTAL PASIVO + CAPITAL:</td><td class="text-end">$<?php echo number_format($total_pasivo_mas_patrimonio,2); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>