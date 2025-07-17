<?php
include("config.php");

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
