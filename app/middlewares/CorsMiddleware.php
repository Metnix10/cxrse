<?php
class CorsMiddleware {
    public static function manejar() {
        $origenesPermitidos = [
            'http://localhost',
            'http://localhost:8080',
            'http://127.0.0.1',
            'http://127.0.0.1:8080'
        ];

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if ($origin !== '') {
            if (in_array($origin, $origenesPermitidos, true)) {
                header("Access-Control-Allow-Origin: " . $origin);
                header("Access-Control-Allow-Credentials: true");
            } else {
                header("Content-Type: application/json; charset=UTF-8");
                http_response_code(403);
                echo json_encode(["error" => "Origen no autorizado por política CORS."]);
                exit;
            }
        }

        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}