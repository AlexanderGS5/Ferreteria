<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Ventas - Ferretería</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            display: flex;
        }

        /* Menú lateral */
        .sidebar {
            width: 240px;
            background-color: #1e1e2f;
            color: #fff;
            min-height: 100vh;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            color: #fff;
            font-size: 16px;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #007BFF;
            color: #fff;
            padding-left: 25px;
        }

        /* Contenido principal */
        .main {
            flex: 1;
            padding: 20px;
        }
        .main h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Ferretería El Leon</h2>
        <a href="ventas.php">🛒 Ventas</a>
        <a href="productos.php">📦 Productos</a>
        <a href="logout.php">🚪 Cerrar Sesión</a>
    </div>

    <div class="main">
        <h1>Bienvenido, <?php echo $_SESSION['usuario']; ?> 👋</h1>
        <div class="card">
            <h2>Panel de Ventas</h2>
            <p>Desde aquí puedes gestionar ventas y productos.</p>
        </div>
    </div>

</body>
</html>
