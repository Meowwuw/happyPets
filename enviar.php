<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "sql312.infinityfree.com";
$user = "if0_39496179";
$pass = "1Lk845CRKxT";
$db   = "if0_39496179_magenta"; 

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST["nombre"];
    $telefono = $_POST["telefono"];
    $correo   = $_POST["correo"];
    $sede     = $_POST["sede"];
    $mensaje  = $_POST["mensaje"];

    $stmt = $conexion->prepare("INSERT INTO contactos (nombre, telefono, correo, sede, mensaje) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $telefono, $correo, $sede, $mensaje);

    if ($stmt->execute()) {
        echo "✅ ¡Mensaje enviado correctamente!";
    } else {
        echo "❌ Error al guardar: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>
