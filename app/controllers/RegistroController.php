<?php
require_once '../middlewares/CorsMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../models/Usuario.php';

class RegistroController {
    private $usuarioModel;

    public function __construct(Usuario $usuarioModel) {
        $this->usuarioModel = $usuarioModel;
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));

        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Cuerpo de petición JSON inválido."]);
            return;
        }

        $nombre = trim($data->nombre ?? '');
        $email = trim($data->email ?? '');
        $password = $data->password ?? '';
        $genero = $data->genero ?? '';
        $fecha_nacimiento = $data->fecha_nacimiento ?? '';
        $rol = $data->rol ?? 'Estudiante';

        if ($nombre === '' || $email === '' || $password === '' || $genero === '' || $fecha_nacimiento === '') {
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos. Por favor llene todos los campos obligatorios."]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["error" => "El formato del correo electrónico no es válido."]);
            return;
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
            http_response_code(400);
            echo json_encode(["error" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial."]);
            return;
        }

        if (!in_array($genero, ['Femenino', 'Masculino', 'Otro'], true)) {
            http_response_code(400);
            echo json_encode(["error" => "Género no válido."]);
            return;
        }

        if (!in_array($rol, ['Estudiante', 'Instructor'], true)) {
            http_response_code(400);
            echo json_encode(["error" => "Rol no permitido para registro público."]);
            return;
        }

        $stmt = $this->usuarioModel->buscarPorEmail($email);
        if ($stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["error" => "El correo electrónico ya está registrado."]);
            return;
        }

        $this->usuarioModel->nombre = $nombre;
        $this->usuarioModel->email = $email;
        $this->usuarioModel->genero = $genero;
        $this->usuarioModel->fecha_nacimiento = $fecha_nacimiento;
        $this->usuarioModel->foto = !empty($data->foto) ? trim($data->foto) : 'default-avatar.png';
        $this->usuarioModel->rol = $rol;
        $this->usuarioModel->password = password_hash($password, PASSWORD_DEFAULT);

        if ($this->usuarioModel->crear()) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Usuario registrado exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo registrar el usuario. Intente más tarde."]);
        }
    }
}

$database = new Database();
$db = $database->getConnection();
$controller = new RegistroController(new Usuario($db));
$controller->handleRequest();