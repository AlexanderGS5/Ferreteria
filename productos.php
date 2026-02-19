<?php
if (session_status() === PHP_SESSION_NONE) session_start();{
if (!isset($_SESSION["usuario"])) { header("Location: login.php"); exit; }
require_once "conexion.php";
}
// Agregar producto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["agregar"])) {
  $nombre = $conn->real_escape_string($_POST["nombre"] ?? "");
  $precio = floatval($_POST["precio"] ?? 0);
  $stock  = intval($_POST["stock"] ?? 0);
  if ($nombre !== "" && $precio >= 0 && $stock >= 0) {
    $conn->query("INSERT INTO productos(nombre, precio, stock) VALUES('$nombre',$precio,$stock)");
    $ok = "Producto agregado.";
  } else { $err = "Datos inválidos."; }
}

// Actualizar stock
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar"])) {
  $id     = intval($_POST["id"]);
  $stock  = intval($_POST["stock"]);
  $conn->query("UPDATE productos SET stock=$stock WHERE id=$id");
  $ok = "Stock actualizado.";
}

// Eliminar producto (funciona incluso si tiene ventas, gracias a ON DELETE CASCADE)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar"])) {
  $id = intval($_POST["id"]);
  $conn->query("DELETE FROM productos WHERE id=$id");
  $ok = "Producto eliminado.";
}

$productos = $conn->query("SELECT * FROM productos ORDER BY id DESC");
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Productos · Ferreteria El Leon</title>
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
    <h4 class="mb-0">Productos</h4>
    <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Panel</a>
  </div>

  <?php if (!empty($ok)): ?><div class="alert alert-success py-2"><?php echo $ok; ?></div><?php endif; ?>
  <?php if (!empty($err)): ?><div class="alert alert-danger  py-2"><?php echo $err; ?></div><?php endif; ?>

  <!-- Formulario para agregar -->
  <div class="card p-3 mb-4">
    <form method="post" class="row g-3">
      <input type="hidden" name="agregar" value="1">
      <div class="col-md-5">
        <label class="form-label">Nombre</label>
        <input class="form-control" name="nombre" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Precio</label>
        <input type="number" step="0.01" class="form-control" name="precio" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Stock</label>
        <input type="number" class="form-control" name="stock" required>
      </div>
      <div class="col-md-2 d-grid">
        <label class="form-label invisible">.</label>
        <button class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Agregar</button>
      </div>
    </form>
  </div>

  <!-- Lista de productos -->
  <div class="card p-3">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while($p = $productos->fetch_assoc()): ?>
          <tr>
            <td><?php echo $p["id"]; ?></td>
            <td><?php echo htmlspecialchars($p["nombre"]); ?></td>
            <td>$<?php echo number_format($p["precio"],2); ?></td>
            <td>
              <form method="post" class="d-flex">
                <input type="hidden" name="actualizar" value="1">
                <input type="hidden" name="id" value="<?php echo $p["id"]; ?>">
                <input type="number" name="stock" value="<?php echo $p["stock"]; ?>" class="form-control form-control-sm me-2" style="width:80px;">
                <button class="btn btn-sm btn-success"><i class="fa-solid fa-rotate"></i></button>
              </form>
            </td>
            <td>
              <form method="post" onsubmit="return confirm('¿Eliminar este producto?')">
                <input type="hidden" name="eliminar" value="1">
                <input type="hidden" name="id" value="<?php echo $p["id"]; ?>">
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
