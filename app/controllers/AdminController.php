<?php
require_once '../middlewares/CorsMiddleware.php';
require_once '../middlewares/AuthMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../models/Usuario.php';
require_once '../models/Categoria.php';
require_once '../models/Interaccion.php';

AuthMiddleware::verificarApi('Admin');

$database = new Database();
$db = $database->getConnection();

$usuarioModel = new Usuario($db);
$categoriaModel = new Categoria($db);
$interaccionModel = new Interaccion($db);

$method = $_SERVER['REQUEST_METHOD'];
$recurso = $_GET['recurso'] ?? '';

try {
    if ($method === 'GET' && $recurso === 'usuarios') {
        http_response_code(200);
        echo json_encode($usuarioModel->listarTodosParaAdmin());

    } elseif ($method === 'GET' && $recurso === 'reporte-usuarios') {
        $tipo = $_GET['tipo'] ?? 'instructor';
        $datos = ($tipo === 'student')
            ? $usuarioModel->obtenerReporteEstudiantes()
            : $usuarioModel->obtenerReporteInstructores();
        http_response_code(200);
        echo json_encode($datos);

    } elseif ($method === 'PUT' && $recurso === 'usuario-estatus') {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = (int)($data['id'] ?? 0);
        $estatus = $data['estatus'] ?? '';

        if (!$id || !in_array($estatus, ['Activo', 'Deshabilitado'], true)) {
            http_response_code(400);
            echo json_encode(["error" => "ID y estatus válido (Activo / Deshabilitado) requeridos."]);
            return;
        }

        if (!$usuarioModel->cambiarEstatus($id, $estatus)) {
            http_response_code(400);
            echo json_encode(["error" => "No se pudo actualizar el usuario o es una cuenta Admin."]);
            return;
        }

        http_response_code(200);
        echo json_encode(["mensaje" => "Estado de usuario actualizado a $estatus."]);

    } elseif ($method === 'GET' && $recurso === 'categorias') {
        http_response_code(200);
        echo json_encode($categoriaModel->listarTodas());

    } elseif ($method === 'POST' && $recurso === 'categorias') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre de la categoría es obligatorio."]);
            return;
        }

        $id = $categoriaModel->crear($data['nombre'], $data['descripcion'] ?? '', $_SESSION['user_id']);
        http_response_code(201);
        echo json_encode(["mensaje" => "Categoría creada correctamente.", "id" => $id]);

    } elseif ($method === 'PUT' && $recurso === 'categorias') {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = (int)($data['id'] ?? 0);
        if (!$id || empty($data['nombre'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID y nombre de categoría obligatorios."]);
            return;
        }

        $categoriaModel->actualizar($id, $data['nombre'], $data['descripcion'] ?? '');
        http_response_code(200);
        echo json_encode(["mensaje" => "Categoría actualizada correctamente."]);

    } elseif ($method === 'DELETE' && $recurso === 'categorias') {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID de categoría requerido."]);
            return;
        }

        $categoriaModel->eliminar($id);
        http_response_code(200);
        echo json_encode(["mensaje" => "Categoría eliminada correctamente."]);

    } elseif ($method === 'GET' && $recurso === 'comentarios') {
        http_response_code(200);
        echo json_encode($interaccionModel->listarTodosComentariosAdmin());

    } elseif ($method === 'DELETE' && $recurso === 'comentarios') {
        $data = json_decode(file_get_contents("php://input"), true);
        $id = (int)($data['id'] ?? $_GET['id'] ?? 0);
        $causa = trim($data['causa'] ?? '');

        if (!$id || $causa === '') {
            http_response_code(400);
            echo json_encode(["error" => "Debes indicar la causa de eliminación del comentario."]);
            return;
        }

        $interaccionModel->bajaLogicaComentario($id, $causa);
        http_response_code(200);
        echo json_encode(["mensaje" => "Comentario dado de baja correctamente."]);

    } else {
        http_response_code(405);
        echo json_encode(["error" => "Método o recurso no permitido."]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}