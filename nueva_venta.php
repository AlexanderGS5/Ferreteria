<?php include("includes/db.php");
if (!isset($_SESSION["usuario"])) header("Location: login.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Nueva Venta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2>Registrar Venta</h2>
  <form method="POST" class="mb-3">
    <label>Producto</label>
    <select name="producto_id" class="form-control mb-2">
      <?php
      $res = $conexion->query("SELECT * FROM productos");
      while ($row = $res->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
      }
      ?>
    </select>
    <label>Cantidad</label>
    <input type="number" name="cantidad" required class="form-control mb-2">
    <button type="submit" class="btn btn-primary">Registrar Venta</button>
  </form>
  <?php
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $producto_id = $_POST["producto_id"];
    $cantidad = $_POST["cantidad"];
    $producto = $conexion->query("SELECT * FROM productos WHERE id = $producto_id")->fetch_assoc();
    $precio = $producto["precio"];
    $total = $precio * $cantidad;
    $conexion->query("INSERT INTO ventas(total) VALUES ($total)");
    $venta_id = $conexion->insert_id;
    $conexion->query("INSERT INTO detalle_venta(venta_id, producto_id, cantidad, precio_unitario)
                      VALUES ($venta_id, $producto_id, $cantidad, $precio)");
    $conexion->query("UPDATE productos SET stock = stock - $cantidad WHERE id = $producto_id");
    echo "<div class='alert alert-success'>Venta registrada con total: $$total</div>";
  }
  ?>
</body>
</html>
