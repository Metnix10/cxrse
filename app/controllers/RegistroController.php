<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/Database.php';
require_once '../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->nombre) && !empty($data->email) && !empty($data->password) && !empty($data->genero) && !empty($data->fecha_nacimiento)) {

    $stmt = $usuario->buscarPorEmail($data->email);
    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["error" => "El correo electrónico ya está registrado."]);
        exit;
    }

    $usuario->nombre = $data->nombre;
    $usuario->email = $data->email;
    $usuario->genero = $data->genero;
    $usuario->fecha_nacimiento = $data->fecha_nacimiento;
    
    $usuario->rol = isset($data->rol) ? $data->rol : 'Estudiante'; 

    $usuario->password = password_hash($data->password, PASSWORD_DEFAULT);

    if ($usuario->crear()) {
        http_response_code(201);
        echo json_encode(["mensaje" => "Usuario registrado exitosamente."]);
    } else {
        http_response_code(503);
        echo json_encode(["error" => "No se pudo registrar el usuario. Intente más tarde."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos. Por favor llene todos los campos obligatorios."]);
}
?>