<?php
require_once '../middlewares/CorsMiddleware.php';
require_once '../middlewares/AuthMiddleware.php';

CorsMiddleware::manejar();
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/database.php';
require_once '../models/Curso.php';
require_once '../models/Inscripcion.php';

class CursoController {
    private $cursoModel;
    private $inscripcionModel;

    public function __construct(Curso $cursoModel, Inscripcion $inscripcionModel) {
        $this->cursoModel = $cursoModel;
        $this->inscripcionModel = $inscripcionModel;
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $recurso = $_GET['recurso'] ?? 'cursos';

        try {
            if ($method === 'GET' && $recurso === 'cursos') {
                if (isset($_GET['id'])) {
                    $curso = $this->cursoModel->obtenerDetalle((int)$_GET['id']);
                    if (!$curso || $curso['estatus'] !== 'Activo') {
                        http_response_code(404);
                        echo json_encode(["error" => "Curso no encontrado o dado de baja."]);
                        return;
                    }
                    http_response_code(200);
                    echo json_encode($curso);
                } else {
                    http_response_code(200);
                    echo json_encode($this->cursoModel->buscarActivos($_GET));
                }

            } elseif ($method === 'GET' && $recurso === 'categorias') {
                http_response_code(200);
                echo json_encode($this->cursoModel->listarCategorias());

            } elseif ($method === 'GET' && $recurso === 'reporte-instructor') {
                AuthMiddleware::verificarApi('Instructor');
                $reporte = $this->cursoModel->obtenerReporteInstructor($_SESSION['user_id']);
                http_response_code(200);
                echo json_encode($reporte);

            } elseif ($method === 'GET' && $recurso === 'estado-inscripcion') {
                AuthMiddleware::iniciarSesionSegura();
                if (!isset($_SESSION['user_id'])) {
                    http_response_code(200);
                    echo json_encode([]);
                    return;
                }
                $cursoId = (int)($_GET['curso_id'] ?? 0);
                http_response_code(200);
                echo json_encode($this->inscripcionModel->obtenerInscripcionesUsuarioCurso($_SESSION['user_id'], $cursoId));

            } elseif ($method === 'GET' && $recurso === 'kardex') {
                AuthMiddleware::verificarApi('Estudiante');
                http_response_code(200);
                echo json_encode($this->inscripcionModel->obtenerKardexUsuario($_SESSION['user_id']));

            } elseif ($method === 'GET' && $recurso === 'reproductor') {
                AuthMiddleware::verificarApi('Estudiante');
                $cursoId = (int)($_GET['curso_id'] ?? 0);
                $detalle = $this->inscripcionModel->obtenerDetalleReproductor($_SESSION['user_id'], $cursoId);
                if (!$detalle) {
                    http_response_code(403);
                    echo json_encode(["error" => "No tienes acceso a este curso."]);
                    return;
                }
                http_response_code(200);
                echo json_encode($detalle);

            } elseif ($method === 'POST' && $recurso === 'cursos') {
                AuthMiddleware::verificarApi('Instructor');
                $data = json_decode(file_get_contents("php://input"), true);

                if (!is_array($data)) {
                    http_response_code(400);
                    echo json_encode(["error" => "Cuerpo de petición JSON inválido."]);
                    return;
                }

                if (empty($data['titulo']) || empty($data['descripcion']) || empty($data['categoria_id']) || empty($data['imagen']) || empty($data['niveles'])) {
                    http_response_code(400);
                    echo json_encode(["error" => "Título, descripción, categoría, imagen y al menos un nivel son obligatorios."]);
                    return;
                }

                foreach ($data['niveles'] as $nivel) {
                    if (empty($nivel['titulo']) || empty($nivel['video_url'])) {
                        http_response_code(400);
                        echo json_encode(["error" => "Cada nivel debe incluir título y video obligatorio."]);
                        return;
                    }
                }

                $data['categoria_id'] = (int)$data['categoria_id'];
                $data['instructor_id'] = $_SESSION['user_id'];
                $cursoId = $this->cursoModel->crearConNiveles($data, $data['niveles']);

                http_response_code(201);
                echo json_encode(["mensaje" => "Curso creado exitosamente.", "curso_id" => $cursoId]);

            } elseif ($method === 'POST' && $recurso === 'inscripciones') {
                AuthMiddleware::verificarApi('Estudiante');
                $data = json_decode(file_get_contents("php://input"), true);

                if (!is_array($data)) {
                    http_response_code(400);
                    echo json_encode(["error" => "Cuerpo de petición JSON inválido."]);
                    return;
                }

                $rawPago = strtolower(trim($data['forma_pago'] ?? ''));
                $mapaPagos = [
                    'paypal'   => 'PayPal',
                    'efectivo' => 'Efectivo',
                    'gratis'   => 'Gratis'
                ];
                $formaPagoNormalizada = $mapaPagos[$rawPago] ?? '';

                if (empty($data['curso_id']) || $formaPagoNormalizada === '') {
                    http_response_code(400);
                    echo json_encode(["error" => "Curso y forma de pago válida (PayPal o Efectivo) requeridos."]);
                    return;
                }

                $usuarioId = $_SESSION['user_id'];
                $cursoId = (int)$data['curso_id'];
                $nivelId = !empty($data['nivel_id']) ? (int)$data['nivel_id'] : null;

                if ($this->inscripcionModel->yaInscrito($usuarioId, $cursoId, $nivelId)) {
                    http_response_code(400);
                    echo json_encode(["error" => "Ya estás inscrito en este curso o nivel."]);
                    return;
                }

                $inscripcionId = $this->inscripcionModel->registrarCompra([
                    'usuario_id'    => $usuarioId,
                    'curso_id'      => $cursoId,
                    'nivel_id'      => $nivelId,
                    'precio_pagado' => $formaPagoNormalizada === 'Gratis' ? 0.00 : ($data['precio_pagado'] ?? 0.00),
                    'forma_pago'    => $formaPagoNormalizada
                ]);

                http_response_code(201);
                echo json_encode(["mensaje" => "Inscripción registrada con éxito.", "inscripcion_id" => $inscripcionId]);

            } elseif ($method === 'POST' && $recurso === 'avance') {
                AuthMiddleware::verificarApi('Estudiante');
                $data = json_decode(file_get_contents("php://input"), true);

                if (!is_array($data) || empty($data['inscripcion_id']) || empty($data['numero_nivel']) || empty($data['curso_id'])) {
                    http_response_code(400);
                    echo json_encode(["error" => "inscripcion_id, numero_nivel y curso_id son obligatorios."]);
                    return;
                }

                $terminado = $this->inscripcionModel->registrarAvance(
                    (int)$data['inscripcion_id'],
                    (int)$data['numero_nivel'],
                    (int)$data['curso_id']
                );

                http_response_code(200);
                echo json_encode([
                    "mensaje" => $terminado ? "¡Curso terminado! Certificado disponible." : "Avance actualizado.",
                    "curso_terminado" => $terminado
                ]);

            } elseif ($method === 'DELETE' && $recurso === 'cursos') {
                AuthMiddleware::verificarApi('Instructor');
                $cursoId = (int)($_GET['id'] ?? 0);
                if (!$cursoId) {
                    http_response_code(400);
                    echo json_encode(["error" => "ID de curso requerido."]);
                    return;
                }

                if (!$this->cursoModel->bajaLogica($cursoId, $_SESSION['user_id'])) {
                    http_response_code(404);
                    echo json_encode(["error" => "Curso no encontrado o sin permisos."]);
                    return;
                }

                http_response_code(200);
                echo json_encode(["mensaje" => "Curso dado de baja lógicamente."]);
            } else {
                http_response_code(405);
                echo json_encode(["error" => "Método o recurso no permitido."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error interno del servidor."]);
        }
    }
}

$database = new Database();
$db = $database->getConnection();
$controller = new CursoController(new Curso($db), new Inscripcion($db));
$controller->handleRequest();