<?php
require_once '../middlewares/CorsMiddleware.php';
require_once '../middlewares/AuthMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../models/Usuario.php';

class AuthController {
    private $usuarioModel;

    public function __construct(Usuario $usuarioModel) {
        $this->usuarioModel = $usuarioModel;
    }

    public function handleRequest() {
        AuthMiddleware::iniciarSesionSegura();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Por favor, ingresa email y contraseña."]);
            return;
        }

        $stmt = $this->usuarioModel->buscarPorEmail($data->email);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["error" => "El email ingresado no está registrado."]);
            return;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['intentos_fallidos'] >= 3 || ($row['estatus'] ?? 'Activo') === 'Deshabilitado') {
            http_response_code(403);
            echo json_encode(["error" => "Cuenta bloqueada o deshabilitada. Contacte al administrador."]);
            return;
        }

        if (password_verify($data->password, $row['password'])) {
            $this->usuarioModel->reiniciarIntentosFallidos($row['id']);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['foto'] = $row['foto'] ?? '';

            http_response_code(200);
            echo json_encode([
                "mensaje" => "Login exitoso",
                "usuario" => [
                    "nombre" => $row['nombre'],
                    "rol" => $row['rol'],
                    "foto" => $_SESSION['foto']
                ]
            ]);
        } else {
            $this->usuarioModel->incrementarIntentosFallidos($row['id']);
            $intentos_restantes = 2 - $row['intentos_fallidos'];

            http_response_code(401);
            if ($intentos_restantes <= 0) {
                echo json_encode(["error" => "Cuenta bloqueada por 3 intentos fallidos."]);
            } else {
                echo json_encode(["error" => "Contraseña incorrecta. Intentos restantes: " . $intentos_restantes]);
            }
        }
    }
}

$database = new Database();
$db = $database->getConnection();
$controller = new AuthController(new Usuario($db));
$controller->handleRequest();