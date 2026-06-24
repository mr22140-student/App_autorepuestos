<?php
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cantidad'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $tipo_pago_form = $_POST['tipo_pago'];
    $cantidades = $_POST['cantidad'];
    $fecha_actual = date('Y-m-d H:i:s');

    $total_venta = 0;
    $productos_a_vender = [];

    // Calcular el monto total de la venta buscando precios reales
    foreach ($cantidades as $id_producto => $cantidad) {
        $cantidad = intval($cantidad);
        if ($cantidad > 0) {
            $res = mysqli_query($conn, "SELECT precio, stock FROM producto WHERE id = $id_producto");
            $prod = mysqli_fetch_assoc($res);
            
            if ($prod && $prod['stock'] >= $cantidad) {
                $subtotal = $prod['precio'] * $cantidad;
                $total_venta += $subtotal;
                $productos_a_vender[] = [
                    'id' => $id_producto,
                    'cantidad' => $cantidad,
                    'precio' => $prod['precio']
                ];
            }
        }
    }

    if ($total_venta > 0) {
        // 1. Guardar el registro general en la tabla 'venta'
        $query_venta = "INSERT INTO venta (cliente_id, fecha, total) VALUES ($cliente_id, '$fecha_actual', $total_venta)";
        
        if (mysqli_query($conn, $query_venta)) {
            $venta_id = mysqli_insert_id($conn); // Recuperamos el ID de la venta creada

            // 2. Descontar las existencias del inventario y guardar el detalle de la venta
            foreach ($productos_a_vender as $p) {
                $pid = $p['id'];
                $cant = $p['cantidad'];
                $prc = $p['precio'];

                // Restar del stock
                mysqli_query($conn, "UPDATE producto SET stock = stock - $cant WHERE id = $pid");
                
                // CORREGIDO: Usando 'precio_unitario' tal como está en tu phpMyAdmin
                mysqli_query($conn, "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario) VALUES ($venta_id, $pid, $cant, $prc)");
            }

            // 3. Formatear las variables para tu tabla relacional 'pago'
            $subtipo_tarjeta = 'NINGUNO';
            if ($tipo_pago_form == 'TARJETA_DEBITO') {
                $tipo_pago_base = 'TARJETA';
                $subtipo_tarjeta = 'DEBITO';
            } elseif ($tipo_pago_form == 'TARJETA_CREDITO') {
                $tipo_pago_base = 'TARJETA';
                $subtipo_tarjeta = 'CREDITO';
            } else {
                $tipo_pago_base = 'EFECTIVO';
            }

            // 4. Insertar en tu tabla 'pago'
            $query_pago = "INSERT INTO pago (tipo, subtipo_tarjeta, monto, compra_id) 
                           VALUES ('$tipo_pago_base', '$subtipo_tarjeta', $total_venta, NULL)";
            
            mysqli_query($conn, $query_pago);

            // Redireccionar al éxito
            header("Location: ventas.php?success=1");
            exit;
        } else {
            echo "Error crítico al guardar la venta: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Debes seleccionar al menos un repuesto con cantidad válida.";
    }
}
?>