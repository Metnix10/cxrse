<?php
require_once '../middlewares/CorsMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
    exit;
}

class UploadController {
    private $cloudName;
    private $uploadPreset;

    public function __construct(array $config) {
        $this->cloudName = $config['cloud_name'] ?? '';
        $this->uploadPreset = $config['upload_preset'] ?? '';
    }

    public function subirArchivo() {
        if ($this->cloudName === '' || $this->uploadPreset === '') {
            http_response_code(500);
            echo json_encode(["error" => "Falta configurar las credenciales de Cloudinary en el servidor."]);
            exit;
        }

        if (!function_exists('curl_init')) {
            http_response_code(500);
            echo json_encode(["error" => "La extensión cURL no está habilitada en php.ini de XAMPP."]);
            exit;
        }

        $archivo = $_FILES['imagen'] ?? $_FILES['archivo'] ?? null;

        if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(["error" => "Error al recibir el archivo (código: " . ($archivo['error'] ?? 'sin archivo') . ")."]);
            exit;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $this->cloudName . "/auto/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new CURLFile($archivo['tmp_name'], $archivo['type'], $archivo['name']),
            'upload_preset' => $this->uploadPreset
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErr) {
            http_response_code(500);
            echo json_encode(["error" => "Error de conexión cURL: " . $curlErr]);
            exit;
        }

        $result = json_decode($response);

        if ($httpCode === 200 && isset($result->secure_url)) {
            http_response_code(200);
            echo json_encode([
                "mensaje" => "Archivo subido exitosamente a Cloudinary",
                "url_imagen" => $result->secure_url,
                "url_archivo" => $result->secure_url
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error de Cloudinary: " . ($result->error->message ?? "Respuesta inválida")]);
        }
    }
}

$configPath = __DIR__ . '/../config/cloudinary.php';
$cloudinaryConfig = file_exists($configPath) ? require $configPath : [];

$controller = new UploadController($cloudinaryConfig);
$controller->subirArchivo();