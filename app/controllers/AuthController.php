<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

session_start();

require_once '../config/Database.php';
require_once '../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    
    $stmt = $usuario->buscarPorEmail($data->email);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['intentos_fallidos'] >= 3) {
            http_response_code(403);
            echo json_encode(["error" => "Cuenta bloqueada por 3 intentos fallidos. Contacte al administrador."]);
            exit;
        }

        if (password_verify($data->password, $row['password'])) {
            $usuario->reiniciarIntentosFallidos($row['id']);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['nombre'] = $row['nombre'];

            http_response_code(200);
            echo json_encode([
                "mensaje" => "Login exitoso",
                "usuario" => [
                    "nombre" => $row['nombre'],
                    "rol" => $row['rol']
                ]
            ]);
        } else {
            $usuario->incrementarIntentosFallidos($row['id']);
            $intentos_restantes = 2 - $row['intentos_fallidos'];

            http_response_code(401);
            if ($intentos_restantes < 0) {
                echo json_encode(["error" => "Cuenta bloqueada por 3 intentos fallidos."]);
            } else {
                echo json_encode(["error" => "Contraseña incorrecta. Intentos restantes: " . ($intentos_restantes + 1)]);
            }
        }
    } else {
        http_response_code(404);
        echo json_encode(["error" => "El email ingresado no está registrado."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Por favor, ingresa email y contraseña."]);
}
?>