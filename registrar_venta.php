<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperamos datos del formulario
    $productos = $_POST['productos']; // debería ser un array con id, cantidad y precio
    $total = $_POST['total'];

    // Guardamos la venta
    $sql = "INSERT INTO ventas (fecha, total) VALUES (NOW(), $total)";
    if ($conn->query($sql) === TRUE) {
        $venta_id = $conn->insert_id;

        // Guardamos los detalles
        foreach ($productos as $producto) {
            $id = $producto['id'];
            $cantidad = $producto['cantidad'];
            $precio = $producto['precio'];

            $sql_detalle = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario) 
                            VALUES ($venta_id, $id, $cantidad, $precio)";
            $conn->query($sql_detalle);

            // Actualizamos el stock del producto
            $sql_stock = "UPDATE productos SET stock = stock - $cantidad WHERE id = $id";
            $conn->query($sql_stock);
        }

        // Redirigimos al ticket
        header("Location: ticket.php?id=" . $venta_id);
        exit;
    } else {
        echo "Error al registrar la venta: " . $conn->error;
    }
}
?>
