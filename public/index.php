<?php

$origenes_permitidos = [
    'http://localhost',
    'http://127.0.0.1'
];

$origen = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origen, $origenes_permitidos)) {
    header("Access-Control-Allow-Origin: " . $origen);
} elseif ($origen !== '') {

    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado por políticas de CORS"]);
    exit();
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../app/config/Database.php';
require_once '../app/middlewares/AuthMiddleware.php'; 

$url = $_GET['url'] ?? 'home';

switch ($url) {

    case 'home':
        require_once '../app/views/cursos/index.php';
        break;

    case 'login':
        require_once '../app/views/auth/login.php';
        break;

    case 'registro':
        require_once '../app/views/auth/registro.php';
        break;

    case 'kardex':
        AuthMiddleware::verificarSesion();
        require_once '../app/views/cursos/kardex.php';
        break;

    case 'reproductor':
        AuthMiddleware::verificarSesion();
        require_once '../app/views/cursos/reproductor.php';
        break;

    case 'instructor':
        AuthMiddleware::verificarRol('Instructor');
        require_once '../app/views/instructor/dashboard-instructor.php';
        break;

    case 'crear-curso':
        AuthMiddleware::verificarRol('Instructor');
        require_once '../app/views/instructor/crear-curso.php';
        break;

    case 'admin':
        AuthMiddleware::verificarRol('Admin');
        require_once '../app/views/admin/admin-usuarios.php';
        break;

    case 'admin-categorias':
        AuthMiddleware::verificarRol('Admin');
        require_once '../app/views/admin/admin-categorias.php';
        break;

    case 'upload':
        require_once '../app/controllers/UploadController.php';
        $controller = new UploadController();
        $controller->subirImagen();
    break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        break;
}
?>