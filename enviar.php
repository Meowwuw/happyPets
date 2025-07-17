<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

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
    
    $pdo = new PDO($dsn, $usuario, $clave, $opciones);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error de conexión a la base de datos',
        'debug' => 'Error Base de datos: ' . $e->getMessage()
    ]);
    exit;
}

function validarCampo($campo, $tipo, $obligatorio = true) {
    $valor = '';
    
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
    $nombre = validarCampo('nombre', 'nombre');
    $telefono = validarCampo('telefono', 'telefono');
    $correo = validarCampo('correo', 'email');
    $sede = validarCampo('sede', 'sede');
    $mensaje = validarCampo('mensaje', 'mensaje');
    
    if (!isset($_POST['politica']) || ($_POST['politica'] !== 'on' && $_POST['politica'] !== '1')) {
        throw new Exception('Debes aceptar la política de privacidad');
    }
    
    $sql = "INSERT INTO contactos (nombre, telefono, correo, sede, mensaje) 
            VALUES (:nombre, :telefono, :correo, :sede, :mensaje)";
    
    $stmt = $pdo->prepare($sql);
    
    $resultado = $stmt->execute([
        ':nombre' => $nombre,
        ':telefono' => $telefono,
        ':correo' => $correo,
        ':sede' => $sede,
        ':mensaje' => $mensaje
    ]);
    
    if ($resultado) {
        $contacto_id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => '¡Mensaje enviado correctamente! Gracias por contactarnos.',
            'id' => $contacto_id,
            'datos' => [
                'nombre' => $nombre,
                'correo' => $correo,
                'sede' => $sede,
                'fecha' => date('Y-m-d H:i:s')
            ],
            'debug' => 'Guardado en Base de datos exitosamente'
        ]);
        
    } else {
        throw new Exception('Error al guardar el mensaje en la base de datos');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => 'Error procesando formulario'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'debug' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}

$pdo = null;
?>