<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $usuario = $conn->real_escape_string($_POST["usuario"] ?? "");
  $clave   = $conn->real_escape_string($_POST["clave"] ?? "");

  $sql = "SELECT usuario, rol FROM usuarios WHERE usuario='$usuario' AND clave='$clave' LIMIT 1";
  $res = $conn->query($sql);

  if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $_SESSION["usuario"] = $row["usuario"];
    $_SESSION["rol"]     = $row["rol"];
    header("Location: index.php"); exit;
  } else {
    $error = "Usuario o clave incorrectos.";
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión · FerrePOS</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
  <link href="estilos.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark sticky-top">
  <div class="container">
    <span class="navbar-brand"><i class="fa-solid fa-screwdriver-wrench me-2"></i>FerrePOS</span>
  </div>
</nav>

<section class="py-5 header-hero">
  <div class="container d-flex justify-content-center">
    <div class="card p-4 p-md-5" style="max-width:420px;width:100%">
      <h4 class="mb-3">Bienvenido 👋</h4>
      <p class="text-secondary mb-4">Inicia sesión para continuar.</p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="mb-3">
          <label class="form-label">Usuario</label>
          <input class="form-control" name="usuario" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Clave</label>
          <input type="password" class="form-control" name="clave" required>
        </div>
        <button class="btn btn-primary w-100">
          <i class="fa-solid fa-right-to-bracket me-2"></i>Entrar
        </button>
      </form>
      <div class="mt-3 small text-secondary">
        Usuario demo: <span class="badge badge-soft">admin</span> · Clave: <span class="badge badge-soft">admin123</span>
      </div>
    </div>
  </div>
</section>
</body>
</html>
