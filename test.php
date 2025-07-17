<?php

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🧪 Test Conexión AWS RDS</h1>";


$host = 'magenta.c7eoowk848fz.us-east-2.rds.amazonaws.com';
$usuario = 'admin';
$clave = '5wpMSZQcOvKuR81S734D';
$nombre_base = 'MAGENTA';

echo "<p><strong>Host:</strong> $host</p>";
echo "<p><strong>Base de datos:</strong> $nombre_base</p>";
echo "<p><strong>Usuario:</strong> $usuario</p>";

try {
    echo "<h2>📡 Intentando conectar...</h2>";
    
    $dsn = "mysql:host=$host;dbname=$nombre_base;charset=utf8mb4;port=3306";
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30
    ];
    
    $inicio = microtime(true);
    $pdo = new PDO($dsn, $usuario, $clave, $opciones);
    $tiempo = round((microtime(true) - $inicio) * 1000, 2);
    
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ Conexión exitosa!</h3>";
    echo "<p>Tiempo: {$tiempo}ms</p>";
    echo "</div>";
    
    echo "<h2>📋 Verificando tabla contactos...</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'contactos'");
    if ($stmt->rowCount() > 0) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
        echo "<h3>✅ Tabla 'contactos' encontrada!</h3>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM contactos");
        $total = $stmt->fetch()['total'];
        echo "<p>Total de registros: $total</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "<h3>❌ Tabla 'contactos' NO encontrada</h3>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Error de conexión</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código:</strong> " . $e->getCode() . "</p>";
    echo "</div>";
    
    echo "<h3>🔧 Posibles soluciones:</h3>";
    echo "<ul>";
    echo "<li>Verificar que las credenciales de Base de datos sean correctas</li>";
    echo "<li>Verificar que el Security Group permita conexiones desde tu IP</li>";
    echo "<li>Verificar que la instancia RDS esté 'Available'</li>";
    echo "<li>Verificar conectividad de red</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><em>Hora: " . date('Y-m-d H:i:s') . "</em></p>";
?>