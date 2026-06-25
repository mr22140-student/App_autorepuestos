<?php
include("conexion.php");

$buscar_texto = isset($_GET['buscar_texto']) ? mysqli_real_escape_string($conn, $_GET['buscar_texto']) : '';
$buscar_fecha = isset($_GET['buscar_fecha']) ? mysqli_real_escape_string($conn, $_GET['buscar_fecha']) : '';
$buscar_mes   = isset($_GET['buscar_mes']) ? mysqli_real_escape_string($conn, $_GET['buscar_mes']) : '';

$where_clauses = [];

if (!empty($buscar_texto)) {
    $where_clauses[] = "(l.descripcion LIKE '%$buscar_texto%' OR c.nombre LIKE '%$buscar_texto%')";
}
if (!empty($buscar_fecha)) {
    $where_clauses[] = "l.fecha = '$buscar_fecha'";
}
if (!empty($buscar_mes)) {
    $where_clauses[] = "DATE_FORMAT(l.fecha, '%Y-%m') = '$buscar_mes'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$asientos = mysqli_query($conn, "
    SELECT l.*, c.codigo, c.nombre as cuenta_nombre 
    FROM libro_diario l
    JOIN catalogo_cuentas c ON l.cuenta_id = c.id
    $where_sql
    ORDER BY l.fecha DESC, l.id ASC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Libro Diario General</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background-color: #1a1a1a; color: #ffffff; }
        .card-custom { background: #262626; border-radius: 8px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-top: 20px; }
        .text-warning-custom { color: #ffc107 !important; }
        .table-custom { color: #ffffff; background-color: #262626; border-color: #383838 !important; }
        .partida-header { background-color: #343a40 !important; color: #ffc107; font-weight: bold; }
        .sangria-abono { padding-left: 45px !important; color: #dcdcdc; }
        .form-control-dark { background-color: #333; color: #fff; border: 1px solid #444; }
        .form-control-dark:focus { background-color: #444; color: #fff; border-color: #ffc107; box-shadow: none; }
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
                <button class="btn btn-warning btn-sm dropdown-toggle fw-bold text-dark nav-dropdown-btn" type="button" id="dropContabilidad" data-bs-toggle="dropdown" aria-expanded="false">
                    📖 Contabilidad
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark custom-dropdown-ul" aria-labelledby="dropContabilidad">
                    <li><a class="dropdown-item fw-bold text-warning" href="librodiario.php">Libro Diario</a></li>
                    <li><a class="dropdown-item" href="libromayor.php">Libro Mayor</a></li>
                    <li><a class="dropdown-item" href="razones.php">Razones Financieras</a></li>
                </ul>
            </div>

            <div class="dropdown" style="display: inline-block;">
                <button class="btn btn-outline-light btn-sm dropdown-toggle fw-bold nav-dropdown-btn" type="button" id="dropReportes" data-bs-toggle="dropdown" aria-expanded="false">
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

    <div class="container py-4">
        <div class="card-custom mb-3" style="padding: 20px;">
            <h5 class="text-warning-custom mb-3">🔍 Panel de Búsqueda y Filtros</h5>
            <form method="GET" action="librodiario.php" class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small text-white-50">Buscar por Concepto / Nombre</label>
                    <input type="text" name="buscar_texto" class="form-control form-control-dark form-control-sm" placeholder="Ej: Venta, Caja, Compra..." value="<?php echo htmlspecialchars($buscar_texto); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-white-50">Filtrar por Día Exacto</label>
                    <input type="date" name="buscar_fecha" class="form-control form-control-dark form-control-sm" value="<?php echo $buscar_fecha; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-white-50">Filtrar por Mes Completo</label>
                    <input type="month" name="buscar_mes" class="form-control form-control-dark form-control-sm" value="<?php echo $buscar_mes; ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark w-100">Filtrar</button>
                    <a href="librodiario.php" class="btn btn-secondary btn-sm w-100">Limpiar</a>
                </div>
            </form>
        </div>

        <div class="card-custom">
            <h2 class="mb-4 text-warning-custom">Libro Diario de Operaciones</h2>
            <table class="table table-custom table-hover align-middle border">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 120px;">Código</th>
                        <th>Cuentas y Detalles</th>
                        <th class="text-end" style="width: 150px;">Debe (Cargo)</th>
                        <th class="text-end" style="width: 150px;">Haber (Abono)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $id_partida_actual = null; 

                    if ($asientos && mysqli_num_rows($asientos) > 0) {
                        while($row = mysqli_fetch_assoc($asientos)){ 
                            $es_abono = ($row['haber'] > 0);
                            $identificador_partida = $row['fecha'] . '_' . $row['descripcion'];

                            if ($id_partida_actual !== $identificador_partida) {
                                $id_partida_actual = $identificador_partida;
                                echo '<tr class="partida-header">';
                                echo '  <td colspan="2">📋 PARTIDA CONTABLE — ' . date("d/m/Y", strtotime($row['fecha'])) . '</td>';
                                echo '  <td colspan="2" class="text-end text-white-50 small font-monospace">Concepto: ' . $row['descripcion'] . '</td>';
                                echo '</tr>';
                            }
                    ?>
                    <tr>
                        <td><code><?php echo $row['codigo']; ?></code></td>
                        <td class="<?php echo $es_abono ? 'sangria-abono' : 'fw-semibold text-info'; ?>">
                            <?php echo $row['cuenta_nombre']; ?>
                        </td>
                        <td class="text-end text-success fw-bold">
                            <?php echo $row['debe'] > 0 ? '$' . number_format($row['debe'], 2) : '-'; ?>
                        </td>
                        <td class="text-end text-danger fw-bold">
                            <?php echo $row['haber'] > 0 ? '$' . number_format($row['haber'], 2) : '-'; ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center text-muted py-4'>No se encontraron partidas con los filtros especificados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>