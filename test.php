<?php
// Test completo para XAMPP local con conexión a AWS RDS
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Happy Pets - XAMPP Local</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .success { color: green; background: #f0fff0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #fff0f0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #f0f8ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-test { background: #f9f9f9; padding: 20px; border-radius: 10px; margin: 20px 0; }
        input, textarea, button { width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #4CAF50; color: white; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <h1>🧪 Test Completo - Happy Pets en XAMPP</h1>
    
    <?php
    // Test 1: Verificar XAMPP
    echo "<h2>📋 Verificación de Entorno</h2>";
    echo "<div class='info'>✅ <strong>XAMPP funcionando:</strong> PHP " . phpversion() . "</div>";
    echo "<div class='info'>✅ <strong>Servidor web:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</div>";
    echo "<div class='info'>✅ <strong>Directorio actual:</strong> " . __DIR__ . "</div>";
    echo "<div class='info'>✅ <strong>URL actual:</strong> " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" . "</div>";
    
    // Test 2: Verificar extensiones PHP necesarias
    echo "<h2>🔌 Extensiones PHP</h2>";
    $extensiones = ['pdo', 'pdo_mysql', 'curl', 'openssl', 'json'];
    foreach ($extensiones as $ext) {
        if (extension_loaded($ext)) {
            echo "<div class='success'>✅ <strong>$ext:</strong> Habilitada</div>";
        } else {
            echo "<div class='error'>❌ <strong>$ext:</strong> NO habilitada</div>";
        }
    }
    
    // Test 3: Conexión a AWS RDS
    echo "<h2>🌐 Conexión a AWS RDS</h2>";
    
    $host = 'magenta.c7eoowk848fz.us-east-2.rds.amazonaws.com';
    $usuario = 'admin';
    $clave = '5wpMSZQcOvKuR81S734D';
    $nombre_base = 'MAGENTA';
    
    try {
        $dsn = "mysql:host=$host;dbname=$nombre_base;charset=utf8mb4;port=3306";
        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30
        ];
        
        $inicio = microtime(true);
        $pdo = new PDO($dsn, $usuario, $clave, $opciones);
        $tiempo = round((microtime(true) - $inicio) * 1000, 2);
        
        echo "<div class='success'>✅ <strong>Conexión exitosa a AWS RDS</strong> (${tiempo}ms)</div>";
        
        // Verificar tabla contactos
        $stmt = $pdo->query("SHOW TABLES LIKE 'contactos'");
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✅ <strong>Tabla 'contactos' encontrada</strong></div>";
            
            // Mostrar estructura
            $stmt = $pdo->query("DESCRIBE contactos");
            $columnas = $stmt->fetchAll();
            
            echo "<h3>📋 Estructura de la tabla:</h3>";
            echo "<table>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th></tr>";
            foreach ($columnas as $col) {
                echo "<tr>";
                echo "<td>{$col['Field']}</td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "<td>{$col['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM contactos");
            $total = $stmt->fetch()['total'];
            echo "<div class='info'>📊 <strong>Total de contactos:</strong> $total</div>";
            
        } else {
            echo "<div class='error'>❌ <strong>Tabla 'contactos' NO encontrada</strong></div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "</div>";
    }
    
    // Test 4: Verificar archivos del proyecto
    echo "<h2>📁 Archivos del Proyecto</h2>";
    $archivos_necesarios = [
        'contactanos.html' => 'Página de contacto',
        'procesar_contacto.php' => 'Procesador PHP',
        'style/Contactanos.css' => 'Estilos CSS',
        'js/contacto.js' => 'JavaScript del formulario'
    ];
    
    foreach ($archivos_necesarios as $archivo => $descripcion) {
        if (file_exists($archivo)) {
            echo "<div class='success'>✅ <strong>$archivo:</strong> $descripcion</div>";
        } else {
            echo "<div class='error'>❌ <strong>$archivo:</strong> $descripcion (NO encontrado)</div>";
        }
    }
    ?>
    
    <h2>🧪 Formulario de Prueba</h2>
    <div class="form-test">
        <h3>Prueba tu formulario de contacto:</h3>
        <form method="POST" action="procesar_contacto.php" target="_blank">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="Juan Pérez Test" required>
            
            <label>Teléfono:</label>
            <input type="tel" name="telefono" value="987654321" required>
            
            <label>Correo:</label>
            <input type="email" name="correo" value="test@happypets.com" required>
            
            <label>Sede:</label>
            <input type="text" name="sede" value="Santa Anita" required>
            
            <label>Mensaje:</label>
            <textarea name="mensaje" rows="3" required>Este es un mensaje de prueba desde XAMPP local conectando a AWS RDS. Fecha: <?php echo date('Y-m-d H:i:s'); ?></textarea>
            
            <label>
                <input type="checkbox" name="politica" value="on" checked required style="width: auto;">
                Acepto la política de privacidad
            </label>
            
            <button type="submit">🚀 Probar Envío a AWS RDS</button>
        </form>
    </div>
    
    <h2>🔗 Enlaces Útiles</h2>
    <ul>
        <li><a href="http://localhost/dashboard" target="_blank">Panel XAMPP</a></li>
        <li><a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
        <li><a href="contactanos.html" target="_blank">Tu página de contacto</a></li>
        <li><a href="procesar_contacto.php" target="_blank">Procesador PHP</a></li>
    </ul>
    
    <div class="info">
        <h3>💡 Próximos pasos:</h3>
        <ol>
            <li>Si todo está ✅, tu XAMPP está configurado correctamente</li>
            <li>Prueba el formulario de arriba para verificar la conexión a AWS</li>
            <li>Abre tu página <code>contactanos.html</code> y prueba el formulario real</li>
            <li>Revisa los datos en AWS RDS o usa phpMyAdmin para conectar a la base remota</li>
        </ol>
    </div>
</body>
</html>