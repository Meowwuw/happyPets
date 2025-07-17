<?php
$host = "magenta.c7eoowk848fz.us-east-2.rds.amazonaws.com";
$user = "admin";
$pass = "5wpMSZQcOvKuR81S734D";
$db   = "MAGENTA";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
