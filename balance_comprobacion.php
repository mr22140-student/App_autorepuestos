<?php
include("conexion.php");

// Consulta SQL avanzada que une el catálogo con las sumas del libro diario
$query = "
    SELECT 
        c.codigo,
        c.nombre,
        c.tipo,
        IFNULL(SUM(l.debe), 0) AS total_debe,
        IFNULL(SUM(l.haber), 0) AS total_haber
    FROM catalogo_cuentas c
    LEFT JOIN libro_diario l ON c.id = l.cuenta_id
    GROUP BY c.id
    ORDER BY c.codigo ASC
";

$resultado = mysqli_query($conn, $query);

// Variables para acumular los totales generales del balance
$global_debe = 0;
$global_haber = 0;
$global_deudor = 0;
$global_acreedor = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Balance de Comprobación - ERP Auto Repuestos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 25px; }
        .text-warning-custom { color: #ffc107 !important; }
        .table-custom { color: #ffffff; background-color: #262626; }
        .total-row { background-color: #333333 !important; font-weight: bold; }

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
                    <li><a class="dropdown-item" href="balance_comprobacion.php" style="color: #ffc107 !important; font-weight: bold;">Balance de Comprobación</a></li>
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
            <h2 class="text-warning-custom mb-2">Balance de Comprobación</h2>
            <p class="text-white-50 m-0">Verificación del principio de partida doble y saldos acumulados por cuenta contable.</p>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th rowspan="2" class="align-middle text-start">Código</th>
                            <th rowspan="2" class="align-middle text-start">Nombre de la Cuenta</th>
                            <th colspan="2">Movimientos</th>
                            <th colspan="2">Saldos</th>
                        </tr>
                        <tr>
                            <th>Debe ($)</th>
                            <th>Haber ($)</th>
                            <th>Deudor ($)</th>
                            <th>Acreedor ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while($row = mysqli_fetch_assoc($resultado)){ 
                            $debe = floatval($row['total_debe']);
                            $haber = floatval($row['total_haber']);
                            
                            $deudor = 0;
                            $acreedor = 0;

                            // Determinar el saldo según la naturaleza del tipo de cuenta
                            if (in_array($row['tipo'], ['ACTIVO', 'COSTO', 'GASTO'])) {
                                $saldo = $debe - $haber;
                                if ($saldo >= 0) {
                                    $deudor = $saldo;
                                } else {
                                    $acreedor = abs($saldo); // Si es negativo invierte su naturaleza
                                }
                            } else { // PASIVO, PATRIMONIO, INGRESO
                                $saldo = $haber - $debe;
                                if ($saldo >= 0) {
                                    $acreedor = $saldo;
                                } else {
                                    $deudor = abs($saldo);
                                }
                            }

                            // Sumar a los totales globales
                            $global_debe += $debe;
                            $global_haber += $haber;
                            $global_deudor += $deudor;
                            $global_acreedor += $acreedor;
                            
                            // Omitir visualmente cuentas que no tienen ningún movimiento ni saldo para no saturar la tabla
                            if ($debe == 0 && $haber == 0) continue;
                        ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                            <td><?php echo htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?> <small class="text-white-50">(<?php echo $row['tipo']; ?>)</small></td>
                            <td class="text-end"><?php echo number_format($debe, 2); ?></td>
                            <td class="text-end"><?php echo number_format($haber, 2); ?></td>
                            <td class="text-end text-info"><?php echo $deudor > 0 ? number_format($deudor, 2) : '-'; ?></td>
                            <td class="text-end text-warning"><?php echo $acreedor > 0 ? number_format($acreedor, 2) : '-'; ?></td>
                        </tr>
                        <?php } ?>
                        
                        <tr class="table-dark total-row">
                            <td colspan="2" class="text-start text-uppercase text-warning">Totales</td>
                            <td class="text-end text-warning">$<?php echo number_format($global_debe, 2); ?></td>
                            <td class="text-end text-warning">$<?php echo number_format($global_haber, 2); ?></td>
                            <td class="text-end text-success">$<?php echo number_format($global_deudor, 2); ?></td>
                            <td class="text-end text-success">$<?php echo number_format($global_acreedor, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php if (number_format($global_debe, 2) === number_format($global_haber, 2) && number_format($global_deudor, 2) === number_format($global_acreedor, 2)): ?>
                    <div class="alert alert-success bg-success text-white border-0 text-center m-0">
                        ✔ <strong>¡Partida Doble Cuadrada Exitosamente!</strong> Los movimientos y saldos se encuentran en perfecto equilibrio contable.
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger bg-danger text-white border-0 text-center m-0">
                        ⚠ <strong>Desbalance detectado:</strong> Las sumas de los movimientos o saldos no coinciden. Revisa que todas las transacciones comerciales tengan su contrapartida correcta.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>