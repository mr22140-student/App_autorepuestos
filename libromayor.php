<?php
include("conexion.php");

$cuentas_query = "
    SELECT DISTINCT c.id, c.codigo, c.nombre, c.tipo 
    FROM catalogo_cuentas c
    INNER JOIN libro_diario l ON c.id = l.cuenta_id
    ORDER BY c.codigo ASC
";
$cuentas_result = mysqli_query($conn, $cuentas_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro Mayor - ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 30px; }
        .text-warning-custom { color: #ffc107 !important; }
        .t-account-title { border-bottom: 2px solid #ffc107; padding-bottom: 5px; margin-bottom: 10px; }
        .t-headers { font-weight: bold; color: #ffc107; border-bottom: 1px solid #444; }
        .t-col-left { border-right: 1px solid #444; padding-right: 15px; }
        .t-col-right { padding-left: 15px; }
        .t-total-row { border-top: 1px solid #ffc107; font-weight: bold; margin-top: 5px; padding-top: 5px; }
        .t-balance-row { border-top: 2px double #2ecc71; font-weight: bold; color: #2ecc71; margin-top: 5px; padding-top: 5px; }
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
                <button class="btn btn-light btn-sm dropdown-toggle fw-bold nav-dropdown-btn" type="button" id="dropContabilidad" data-bs-toggle="dropdown" aria-expanded="false">
                    📖 Contabilidad
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropContabilidad">
                    <li><a class="dropdown-item" href="librodiario.php">Libro Diario</a></li>
                    <li><a class="dropdown-item" href="libromayor.php" style="color: #ffc107 !important; font-weight: bold;">Libro Mayor</a></li>
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
            <h2 class="text-warning-custom mb-2">Libro Mayor (Cuentas T)</h2>
            <p class="text-white-50">Centralización de cargos, abonos y saldos finales analizados por cada cuenta contable del ERP.</p>
        </div>

        <div class="row">
            <?php 
            while($cuenta = mysqli_fetch_assoc($cuentas_result)) { 
                $cuenta_id = $cuenta['id'];
                $movimientos_query = "SELECT descripcion, debe, haber, fecha FROM libro_diario WHERE cuenta_id = $cuenta_id ORDER BY fecha ASC";
                $movimientos_result = mysqli_query($conn, $movimientos_query);
                
                $movimientos = [];
                $suma_debe = 0;
                $suma_haber = 0;
                
                while($m = mysqli_fetch_assoc($movimientos_result)) {
                    $movimientos[] = $m;
                    $suma_debe += floatval($m['debe']);
                    $suma_haber += floatval($m['haber']);
                }
            ?>
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card-custom h-100">
                    <div class="t-account-title d-flex justify-content-between align-items-center">
                        <h5 class="m-0 text-truncate"><span class="text-warning-custom"><?php echo $cuenta['codigo']; ?></span> - <?php echo $cuenta['nombre']; ?></h5>
                        <span class="badge bg-secondary text-uppercase small"><?php echo $cuenta['tipo']; ?></span>
                    </div>
                    
                    <div class="row text-center t-headers mb-2">
                        <div class="col-6 py-1 border-end border-secondary">DEBE (Cargos)</div>
                        <div class="col-6 py-1">HABER (Abonos)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 t-col-left text-end">
                            <?php 
                            foreach($movimientos as $mov) {
                                if($mov['debe'] > 0) {
                                    echo "<div class='d-flex justify-content-between small text-white-50 mb-1'>";
                                    echo "<span class='text-start text-truncate me-1' title='".htmlspecialchars($mov['descripcion'], ENT_QUOTES, 'UTF-8')."'>• ".$mov['descripcion']."</span>";
                                    echo "<span>$".number_format($mov['debe'], 2)."</span>";
                                    echo "</div>";
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="col-6 t-col-right text-end">
                            <?php 
                            foreach($movimientos as $mov) {
                                if($mov['haber'] > 0) {
                                    echo "<div class='d-flex justify-content-between small text-white-50 mb-1'>";
                                    echo "<span class='text-start text-truncate me-1' title='".htmlspecialchars($mov['descripcion'], ENT_QUOTES, 'UTF-8')."'>• ".$mov['descripcion']."</span>";
                                    echo "<span>$".number_format($mov['haber'], 2)."</span>";
                                    echo "</div>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="row text-end t-total-row mt-3">
                        <div class="col-6 border-end border-secondary">
                            $<?php echo number_format($suma_debe, 2); ?>
                        </div>
                        <div class="col-6">
                            $<?php echo number_format($suma_haber, 2); ?>
                        </div>
                    </div>
                    
                    <div class="row text-end t-balance-row">
                        <?php
                        $saldo_deudor = 0;
                        $saldo_acreedor = 0;
                        
                        if (in_array($cuenta['tipo'], ['ACTIVO', 'COSTO', 'GASTO'])) {
                            $balance = $suma_debe - $suma_haber;
                            if($balance >= 0) $saldo_deudor = $balance;
                            else $saldo_acreedor = abs($balance);
                        } else { 
                            $balance = $suma_haber - $suma_debe;
                            if($balance >= 0) $saldo_acreedor = $balance;
                            else $saldo_deudor = abs($balance);
                        }
                        ?>
                        <div class="col-6 border-end border-secondary text-info fw-bold">
                            <?php echo $saldo_deudor > 0 ? "Saldo Deudor: $".number_format($saldo_deudor, 2) : ""; ?>
                        </div>
                        <div class="col-6 text-warning fw-bold">
                            <?php echo $saldo_acreedor > 0 ? "Saldo Acreedor: $".number_format($saldo_acreedor, 2) : ""; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>