<?php
require_once '../middlewares/CorsMiddleware.php';
require_once '../middlewares/AuthMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../models/Usuario.php';

AuthMiddleware::iniciarSesionSegura();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesión."]);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$usuarioModel = new Usuario($db);

$method = $_SERVER['REQUEST_METHOD'];
$userId = (int)$_SESSION['user_id'];

try {
    if ($method === 'GET') {
        $perfil = $usuarioModel->obtenerPorId($userId);
        if (!$perfil) {
            http_response_code(404);
            echo json_encode(["error" => "Usuario no encontrado."]);
            return;
        }
        http_response_code(200);
        echo json_encode($perfil);

    } elseif ($method === 'PUT' || $method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || empty(trim($data['nombre'] ?? '')) || empty($data['genero']) || empty($data['fecha_nacimiento'])) {
            http_response_code(400);
            echo json_encode(["error" => "Nombre, género y fecha de nacimiento son obligatorios."]);
            return;
        }

        $generosValidos = ['Femenino', 'Masculino', 'Otro'];
        if (!in_array($data['genero'], $generosValidos, true)) {
            http_response_code(400);
            echo json_encode(["error" => "Género no válido."]);
            return;
        }

        $nuevaPassword = $data['password'] ?? '';
        if ($nuevaPassword !== '') {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nuevaPassword)) {
                http_response_code(400);
                echo json_encode(["error" => "La nueva contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial."]);
                return;
            }
        }

        $usuarioModel->actualizarPerfil($userId, [
            'nombre'           => $data['nombre'],
            'genero'           => $data['genero'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'foto'             => $data['foto'] ?? null,
            'password'         => $nuevaPassword
        ]);

        $_SESSION['nombre'] = trim($data['nombre']);
        if (!empty($data['foto'])) {
            $_SESSION['foto'] = trim($data['foto']);
        }

        http_response_code(200);
        echo json_encode(["mensaje" => "Perfil actualizado correctamente."]);

    } else {
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido."]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno al actualizar el perfil."]);
}