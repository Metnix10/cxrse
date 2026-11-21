<?php
require_once '../middlewares/CorsMiddleware.php';
require_once '../middlewares/AuthMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../models/Interaccion.php';

$database = new Database();
$db = $database->getConnection();
$interaccionModel = new Interaccion($db);

$method = $_SERVER['REQUEST_METHOD'];
$recurso = $_GET['recurso'] ?? '';

try {
    if ($method === 'GET' && $recurso === 'comentarios') {
        $cursoId = (int)($_GET['curso_id'] ?? 0);
        http_response_code(200);
        echo json_encode($interaccionModel->listarComentariosCurso($cursoId));

    } elseif ($method === 'POST' && $recurso === 'comentarios') {
        AuthMiddleware::verificarApi('Estudiante');
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['curso_id']) || empty($data['comentario'])) {
            http_response_code(400);
            echo json_encode(["error" => "El comentario es obligatorio."]);
            return;
        }

        $id = $interaccionModel->crearComentario(
            $_SESSION['user_id'],
            (int)$data['curso_id'],
            (int)($data['calificacion'] ?? 5),
            $data['comentario']
        );
        http_response_code(201);
        echo json_encode(["mensaje" => "Reseña publicada.", "id" => $id]);

    } elseif ($method === 'GET' && $recurso === 'mensajes') {
        AuthMiddleware::iniciarSesionSegura();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "No autenticado."]);
            return;
        }

        if (isset($_GET['bandeja']) && $_SESSION['rol'] === 'Instructor') {
            http_response_code(200);
            echo json_encode($interaccionModel->listarBandejaInstructor($_SESSION['user_id']));
        } else {
            $cursoId = (int)($_GET['curso_id'] ?? 0);
            $otroUsuarioId = (int)($_GET['contacto_id'] ?? 0);
            http_response_code(200);
            echo json_encode($interaccionModel->obtenerConversacion($cursoId, $_SESSION['user_id'], $otroUsuarioId));
        }

    } elseif ($method === 'POST' && $recurso === 'mensajes') {
        AuthMiddleware::iniciarSesionSegura();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "No autenticado."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['curso_id']) || empty($data['destinatario_id']) || empty($data['mensaje'])) {
            http_response_code(400);
            echo json_encode(["error" => "Curso, destinatario y mensaje son obligatorios."]);
            return;
        }

        $id = $interaccionModel->enviarMensaje(
            (int)$data['curso_id'],
            $_SESSION['user_id'],
            (int)$data['destinatario_id'],
            $data['mensaje']
        );
        http_response_code(201);
        echo json_encode(["mensaje" => "Mensaje enviado.", "id" => $id]);

    } else {
        http_response_code(405);
        echo json_encode(["error" => "Método o recurso no permitido."]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}