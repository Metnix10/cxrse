<?php

require_once '../app/config/Database.php';

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
        require_once '../app/views/cursos/kardex.php';
        break;

    case 'instructor':
        require_once '../app/views/instructor/dashboard-instructor.php';
        break;

    case 'admin':
        require_once '../app/views/admin/admin-usuarios.php';
        break;

    case 'crear-curso':
        require_once '../app/views/instructor/crear-curso.php';
        break;

    case 'reproductor':
        require_once '../app/views/cursos/reproductor.php';
        break;

    case 'admin-categorias':
        require_once '../app/views/admin/admin-categorias.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        break;
}