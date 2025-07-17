<?php
$host = "sql312.infinityfree.com";
$user = "if0_39496179";
$pass = "1Lk845CRKxT";  
$db   = "if0_39496179_magenta";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
