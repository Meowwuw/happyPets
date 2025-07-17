<?php
// Configuración de headers para CORS y JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Configuración de la base de datos AWS RDS
$host = 'magenta.c7eoowk848fz.us-east-2.rds.amazonaws.com';
$usuario = 'admin';
$clave = '5wpMSZQcOvKuR81S734D';
$nombre_base = 'MAGENTA';

try {
    // Crear conexión PDO con configuración para AWS RDS
    $dsn = "mysql:host=$host;dbname=$nombre_base;charset=utf8mb4;port=3306";
    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => false, // Para AWS RDS
        PDO::ATTR_TIMEOUT => 30
    ];
    
    $pdo = new PDO($dsn, $usuario, $clave, $opciones);
    
} catch (PDOException $e) {
    error_log("Error de conexión AWS RDS: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error de conexión a la base de datos',
        'debug' => 'Revisa la configuración de AWS RDS'
    ]);
    exit;
}

// Función para validar y limpiar datos
function validarCampo($campo, $tipo, $obligatorio = true) {
    $valor = '';
    
    // Obtener valor según el tipo de envío
    if (isset($_POST[$campo])) {
        $valor = trim($_POST[$campo]);
    }
    
    if ($obligatorio && empty($valor)) {
        throw new Exception("El campo $campo es obligatorio");
    }
    
    switch ($tipo) {
        case 'nombre':
            if (!empty($valor) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/', $valor)) {
                throw new Exception('El nombre debe contener solo letras (2-100 caracteres)');
            }
            break;
            
        case 'telefono':
            if (!empty($valor) && !preg_match('/^[+]?[\d\s\-\(\)]{9,20}$/', $valor)) {
                throw new Exception('El teléfono debe tener entre 9 y 20 dígitos');
            }
            break;
            
        case 'email':
            if (!empty($valor) && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido');
            }
            if (!empty($valor) && strlen($valor) > 100) {
                throw new Exception('El correo es demasiado largo');
            }
            break;
            
        case 'mensaje':
            if (!empty($valor) && (strlen($valor) < 10 || strlen($valor) > 1000)) {
                throw new Exception('El mensaje debe tener entre 10 y 1000 caracteres');
            }
            break;
            
        case 'sede':
            if (!empty($valor) && strlen($valor) > 50) {
                throw new Exception('La sede es demasiado larga');
            }
            break;
    }
    
    return $valor;
}

try {
    // Log para debugging
    error_log("Procesando contacto: " . print_r($_POST, true));
    
    // Validar todos los campos
    $nombre = validarCampo('nombre', 'nombre');
    $telefono = validarCampo('telefono', 'telefono');
    $correo = validarCampo('correo', 'email');
    $sede = validarCampo('sede', 'sede');
    $mensaje = validarCampo('mensaje', 'mensaje');
    
    // Verificar que se aceptó la política de privacidad
    if (!isset($_POST['politica']) || ($_POST['politica'] !== 'on' && $_POST['politica'] !== '1')) {
        throw new Exception('Debes aceptar la política de privacidad');
    }
    
    // Preparar la consulta SQL para tu tabla
    $sql = "INSERT INTO contactos (nombre, telefono, correo, sede, mensaje) 
            VALUES (:nombre, :telefono, :correo, :sede, :mensaje)";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta con los parámetros
    $resultado = $stmt->execute([
        ':nombre' => $nombre,
        ':telefono' => $telefono,
        ':correo' => $correo,
        ':sede' => $sede,
        ':mensaje' => $mensaje
    ]);
    
    if ($resultado) {
        $contacto_id = $pdo->lastInsertId();
        
        // Log de éxito
        error_log("Contacto guardado exitosamente con ID: $contacto_id");
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => '¡Mensaje enviado correctamente! Gracias por contactarnos.',
            'id' => $contacto_id,
            'debug' => 'Guardado en AWS RDS correctamente'
        ]);
        
    } else {
        throw new Exception('Error al guardar el mensaje en la base de datos');
    }
    
} catch (Exception $e) {
    // Log del error
    error_log("Error en contacto: " . $e->getMessage());
    
    // Respuesta de error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => 'Error procesando formulario'
    ]);
    
} catch (PDOException $e) {
    // Error específico de base de datos
    error_log("Error PDO: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'debug' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}

// Cerrar conexión
$pdo = null;
?>