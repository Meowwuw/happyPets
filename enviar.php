<?php
$host = "sql312.infinityfree.com";
$user = "if0_39496179";
$pass = "1lk845CRkxT";
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
