<?php
require_once __DIR__ . '/../../middlewares/AuthMiddleware.php';
AuthMiddleware::verificarRol('Estudiante');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Inscripcion.php';

$inscripcionId = (int)($_GET['inscripcion_id'] ?? 0);
$database = new Database();
$db = $database->getConnection();
$inscripcionModel = new Inscripcion($db);
$diploma = $inscripcionModel->obtenerDiploma($inscripcionId, $_SESSION['user_id']);

if (!$diploma) {
    header('Location: index.php?url=kardex');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diploma - <?= htmlspecialchars($diploma['curso_titulo']) ?></title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/diploma.css">
</head>
<body>

    <div class="no-print">
        <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>
    </div>

    <div class="diploma-actions no-print">
        <a href="index.php?url=kardex" class="btn-back">← Volver al Kardex</a>
        <button type="button" onclick="window.print()" class="btn-print">Descargar / Imprimir Diploma (PDF)</button>
    </div>

    <div class="diploma-wrapper">
        <div class="diploma-sheet">
            <div class="gradient-left"></div>
            <div class="gradient-bottom"></div>

            <div class="ribbon-badge">
                <svg viewBox="0 0 120 150" class="medal-svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#fde68a"/>
                            <stop offset="45%" stop-color="#d4a03e"/>
                            <stop offset="75%" stop-color="#fef08a"/>
                            <stop offset="100%" stop-color="#926619"/>
                        </linearGradient>
                        <radialGradient id="goldCenter" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#fef9c3"/>
                            <stop offset="55%" stop-color="#eab308"/>
                            <stop offset="100%" stop-color="#a16207"/>
                        </radialGradient>
                    </defs>
                    <polygon points="35,95 18,142 36,130 48,145 56,98" fill="url(#goldGrad)"/>
                    <polygon points="85,95 102,142 84,130 72,145 64,98" fill="url(#goldGrad)"/>
                    <circle cx="60" cy="58" r="46" fill="url(#goldGrad)" stroke="#b45309" stroke-width="2" stroke-dasharray="6,3"/>
                    <circle cx="60" cy="58" r="39" fill="url(#goldCenter)" stroke="#fef08a" stroke-width="2"/>
                    <circle cx="60" cy="58" r="31" fill="none" stroke="#92400e" stroke-width="1.5"/>
                </svg>
            </div>
            
            <div class="diploma-content">
                <h1 class="diploma-title">DIPLOMA</h1>
                <p class="diploma-subtitle">CERTIFICADO</p>

                <div class="student-name">
                    <?= htmlspecialchars($diploma['alumno_nombre']) ?>
                </div>
                <div class="dotted-line"></div>

                <p class="diploma-text">
                    Se otorga este diploma como testimonio de su logro de haber completado<br>
                    el curso de <strong><?= htmlspecialchars($diploma['curso_titulo']) ?></strong>. Felicidades por su dedicación y logro.
                </p>

                <div class="instructor-block">
                    <div class="instructor-name"><?= htmlspecialchars($diploma['instructor_nombre']) ?></div>
                    <div class="solid-line"></div>
                    <span class="instructor-label">INSTRUCTOR</span>
                </div>

                <div class="dates-row">
                    <span>FECHA DE INICIO: <strong><?= htmlspecialchars($diploma['fecha_inicio']) ?></strong></span>
                    <span>FECHA DE FIN: <strong><?= htmlspecialchars($diploma['fecha_fin']) ?></strong></span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>