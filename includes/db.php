<?php
$conexion = new mysqli("localhost", "root", "password", "ferrepos");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
session_start();
?>