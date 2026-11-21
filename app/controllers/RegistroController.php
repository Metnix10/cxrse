<?php
require_once '../middlewares/CorsMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Se requiere POST."]);
    exit;
}

require_once '../config/database.php';
require_once '../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Cuerpo de petición JSON inválido."]);
    exit;
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
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "El formato del correo electrónico no es válido."]);
    exit;
}

if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
    http_response_code(400);
    echo json_encode(["error" => "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial."]);
    exit;
}

if (!in_array($genero, ['Femenino', 'Masculino', 'Otro'], true)) {
    http_response_code(400);
    echo json_encode(["error" => "Género no válido."]);
    exit;
}

if (!in_array($rol, ['Estudiante', 'Instructor'], true)) {
    http_response_code(400);
    echo json_encode(["error" => "Rol no permitido para registro público."]);
    exit;
}

$stmt = $usuario->buscarPorEmail($email);
if ($stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["error" => "El correo electrónico ya está registrado."]);
    exit;
}

$usuario->nombre = $nombre;
$usuario->email = $email;
$usuario->genero = $genero;
$usuario->fecha_nacimiento = $fecha_nacimiento;
$usuario->foto = !empty($data->foto) ? trim($data->foto) : 'default-avatar.png';
$usuario->rol = $rol;
$usuario->password = password_hash($password, PASSWORD_DEFAULT);

if ($usuario->crear()) {
    http_response_code(201);
    echo json_encode(["mensaje" => "Usuario registrado exitosamente."]);
    exit;
} else {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo registrar el usuario. Intente más tarde."]);
    exit;
}