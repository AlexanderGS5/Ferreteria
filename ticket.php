<?php
include 'conexion.php';

if (!isset($_GET['id'])) {
    die("ID de venta no especificado.");
}

$venta_id = $_GET['id'];

// Datos de la venta
$sql_venta = "SELECT * FROM ventas WHERE id = $venta_id";
$result_venta = $conn->query($sql_venta);
$venta = $result_venta->fetch_assoc();

// Detalles de la venta
$sql_detalle = "SELECT dv.*, p.nombre 
                FROM detalle_venta dv 
                JOIN productos p ON dv.producto_id = p.id 
                WHERE dv.venta_id = $venta_id";
$result_detalle = $conn->query($sql_detalle);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket de Venta</title>
<style>
    body {
        font-family: 'Courier New', monospace;
        max-width: 400px;
        margin: auto;
        padding: 10px;
        background-color: #f5f5f5;
    }
    .ticket {
        background: #fff;
        padding: 20px;
        border: 1px solid #ddd;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
    h2, h3 {
        text-align: center;
        margin: 0;
    }
    .ticket-header {
        border-bottom: 2px solid #000;
        margin-bottom: 10px;
        padding-bottom: 5px;
    }
    .detalle {
        margin: 10px 0;
    }
    .detalle p {
        display: flex;
        justify-content: space-between;
        margin: 2px 0;
    }
    .total {
        font-weight: bold;
        border-top: 2px dashed #000;
        padding-top: 5px;
        text-align: right;
    }
    .btn-print {
        display: block;
        width: 100%;
        margin-top: 15px;
        padding: 10px;
        background: #28a745;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 4px;
    }
    .btn-print:hover {
        background: #218838;
    }
</style>
</head>
<body>
<div class="ticket">
    <div class="ticket-header">
        <h2>Ferretería El León</h2>
        <h3>Ticket de Venta</h3>
    </div>

    <p><strong>Folio:</strong> <?= $venta['id'] ?></p>
    <p><strong>Fecha:</strong> <?= $venta['fecha'] ?></p>

    <div class="detalle">
        <?php while ($row = $result_detalle->fetch_assoc()): ?>
            <p>
                <span><?= htmlspecialchars($row['nombre']) ?> x<?= $row['cantidad'] ?></span>
                <span>$<?= number_format($row['precio_unitario'] * $row['cantidad'],2) ?></span>
            </p>
        <?php endwhile; ?>
    </div>

    <p class="total">Total: $<?= number_format($venta['total'],2) ?></p>

    <button class="btn-print" onclick="window.print()">Imprimir Ticket</button>
</div>
</body>
</html>
