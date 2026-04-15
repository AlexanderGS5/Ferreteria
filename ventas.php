<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit; }
require_once "conexion.php";

// Inicializar carrito
if (!isset($_SESSION["carrito"])) $_SESSION["carrito"] = [];

// Obtener productos para el formulario
$productos = $conn->query("SELECT * FROM productos ORDER BY nombre ASC");

// Agregar producto al carrito
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['agregar'])) {
    $producto_id = intval($_POST["producto_id"] ?? 0);
    $cantidad    = intval($_POST["cantidad"] ?? 0);

    if ($producto_id > 0 && $cantidad > 0) {
        if (isset($_SESSION["carrito"][$producto_id])) {
            $_SESSION["carrito"][$producto_id] += $cantidad;
        } else {
            $_SESSION["carrito"][$producto_id] = $cantidad;
        }
    }
}

// Finalizar venta
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['finalizar'])) {
    if (!empty($_SESSION["carrito"])) {
        $total = 0;
        foreach ($_SESSION["carrito"] as $prod_id => $cant) {
            $prod = $conn->query("SELECT precio, stock FROM productos WHERE id=$prod_id")->fetch_assoc();
            if ($prod['stock'] < $cant) {
                $err = "Stock insuficiente para {$prod_id}.";
                break;
            }
            $total += $prod['precio'] * $cant;
        }

        if (!isset($err)) {
            $conn->query("INSERT INTO ventas(total) VALUES ($total)");
            $venta_id = $conn->insert_id;

            foreach ($_SESSION["carrito"] as $prod_id => $cant) {
                $prod = $conn->query("SELECT precio FROM productos WHERE id=$prod_id")->fetch_assoc();
                $precio = $prod['precio'];
                $conn->query("INSERT INTO detalle_venta(venta_id, producto_id, cantidad, precio_unitario)
                              VALUES ($venta_id, $prod_id, $cant, $precio)");
                $conn->query("UPDATE productos SET stock = stock - $cant WHERE id=$prod_id");
            }

            $_SESSION["carrito"] = []; // vaciar carrito
            $ok = "Venta registrada correctamente (ID #$venta_id). <a href='ticket.php?id=$venta_id' target='_blank'>Ver Ticket</a>";
        }
    } else {
        $err = "El carrito está vacío.";
    }
}

// Eliminar venta
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['eliminar'])) {
    $id = intval($_POST["id"]);
    $conn->query("DELETE FROM ventas WHERE id=$id");
    $ok = "Venta eliminada.";
}

// Eliminar producto del carrito
if (isset($_GET['eliminar_carrito'])) {
    $prod_id = intval($_GET['eliminar_carrito']);
    unset($_SESSION["carrito"][$prod_id]);
}

// Historial de ventas
$hist = $conn->query("
  SELECT v.id, v.fecha, v.total,
         GROUP_CONCAT(CONCAT(d.cantidad,' x ',p.nombre) ORDER BY d.id SEPARATOR ', ') AS items
  FROM ventas v
  LEFT JOIN detalle_venta d ON d.venta_id = v.id
  LEFT JOIN productos p ON p.id = d.producto_id
  GROUP BY v.id
  ORDER BY v.fecha DESC
");
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ventas · FerrePOS</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="estilos.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fa-solid fa-screwdriver-wrench me-2"></i>Ferreteria El Leon</a>
    <a href="logout.php" class="btn btn-outline-light btn-sm">Salir</a>
  </div>
</nav>

<section class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Ventas</h4>
    <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Panel</a>
  </div>

  <?php if (!empty($ok)): ?><div class="alert alert-success py-2"><?php echo $ok; ?></div><?php endif; ?>
  <?php if (!empty($err)): ?><div class="alert alert-danger py-2"><?php echo $err; ?></div><?php endif; ?>

  <!-- Formulario para agregar productos al carrito -->
  <div class="card p-3 mb-4">
    <form method="post" class="row g-3">
      <input type="hidden" name="agregar" value="1">
      <div class="col-md-6">
        <label class="form-label">Producto</label>
        <select name="producto_id" class="form-select" required>
          <option value="">Selecciona…</option>
          <?php foreach ($productos as $pr): ?>
            <option value="<?php echo $pr['id']; ?>">
              <?php echo htmlspecialchars($pr['nombre']); ?> — $<?php echo number_format($pr['precio'],2); ?> (Stock: <?php echo $pr['stock']; ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Cantidad</label>
        <input type="number" min="1" class="form-control" name="cantidad" required>
      </div>
      <div class="col-md-3 d-grid">
        <label class="form-label invisible">.</label>
        <button class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Agregar al carrito</button>
      </div>
    </form>
  </div>

  <!-- Mostrar carrito -->
  <div class="card p-3 mb-4">
    <h5>Carrito</h5>
    <?php if (!empty($_SESSION["carrito"])): ?>
    <table class="table table-striped align-middle">
      <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th><th>Acciones</th></tr></thead>
      <tbody>
        <?php
        $total_carrito = 0;
        foreach ($_SESSION["carrito"] as $prod_id => $cant):
            $prod = $conn->query("SELECT * FROM productos WHERE id=$prod_id")->fetch_assoc();
            $subtotal = $prod['precio'] * $cant;
            $total_carrito += $subtotal;
        ?>
        <tr>
          <td><?= htmlspecialchars($prod['nombre']) ?></td>
          <td><?= $cant ?></td>
          <td>$<?= number_format($prod['precio'],2) ?></td>
          <td>$<?= number_format($subtotal,2) ?></td>
          <td><a href="?eliminar_carrito=<?= $prod_id ?>" class="btn btn-sm btn-danger">Eliminar</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p><strong>Total: $<?= number_format($total_carrito,2) ?></strong></p>
    <form method="post"><button type="submit" name="finalizar" class="btn btn-success">Finalizar Venta</button></form>
    <?php else: ?>
    <p>El carrito está vacío.</p>
    <?php endif; ?>
  </div>

  <!-- Historial de ventas -->
  <div class="card p-3">
    <h6 class="mb-3">Historial</h6>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr><th>ID</th><th>Fecha</th><th>Detalle</th><th>Total</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <?php while($v = $hist->fetch_assoc()): ?>
          <tr>
            <td>#<?php echo $v["id"]; ?></td>
            <td><?php echo $v["fecha"]; ?></td>
            <td><?php echo htmlspecialchars($v["items"] ?? "—"); ?></td>
            <td>$<?php echo number_format($v["total"],2); ?></td>
            <td class="d-flex gap-1">
              <a href="ticket.php?id=<?php echo $v["id"]; ?>" target="_blank" class="btn btn-sm btn-info"><i class="fa-solid fa-print"></i></a>
              <form method="post" onsubmit="return confirm('¿Eliminar esta venta?')">
                <input type="hidden" name="eliminar" value="1">
                <input type="hidden" name="id" value="<?php echo $v["id"]; ?>">
                <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</section>
</body>
</html>
