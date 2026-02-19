<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ferrepos";

$conn = new mysqli($host, $user, $dbname);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
