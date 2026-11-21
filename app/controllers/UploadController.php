<?php
//header("Access-Control-Allow-Origin: http://localhost");
//header("Access-Control-Allow-Credentials: true");
//header("Content-Type: application/json; charset=UTF-8");
//header("Access-Control-Allow-Methods: POST, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//    http_response_code(200);
//    exit;
//}

class UploadController {
    public function subirImagen() {
        if (!function_exists('curl_init')) {
            http_response_code(500);
            echo json_encode(["error" => "La extensión cURL no está habilitada en php.ini de XAMPP."]);
            exit;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(["error" => "Error al recibir la imagen (código: " . ($_FILES['imagen']['error'] ?? 'sin archivo') . ")."]);
            exit;
        }

        $cloud_name = "dkqbijjd";
        $upload_preset = "cxrse_portadas";
        $file_tmp_path = $_FILES['imagen']['tmp_name'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $cloud_name . "/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new CURLFile($file_tmp_path),
            'upload_preset' => $upload_preset
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evita bloqueo SSL en XAMPP local

        $response = curl_exec($ch);
        $curl_err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curl_err) {
            http_response_code(500);
            echo json_encode(["error" => "Error de conexión cURL: " . $curl_err]);
            exit;
        }

        $result = json_decode($response);

        if ($http_code === 200 && isset($result->secure_url)) {
            http_response_code(200);
            echo json_encode([
                "mensaje" => "Imagen subida exitosamente a Cloudinary",
                "url_imagen" => $result->secure_url
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error de Cloudinary: " . ($result->error->message ?? "Respuesta inválida")]);
        }
    }
}

$controller = new UploadController();
$controller->subirImagen();