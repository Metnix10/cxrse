<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproductor de Curso - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/reproductor.css?v=3">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

    <main class="player-layout">
        
        <section class="main-player-content">
            
            <div class="video-container">
                <video id="courseVideo" controls>
                    Tu navegador no soporta el reproductor de video.
                </video>
            </div>

            <div class="lesson-info">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px;">
                    <span class="lesson-badge" id="lessonBadge">Nivel 1</span>
                    <button type="button" id="btnCompleteLevel" class="btn-download" style="background-color:#10b981; border:none; cursor:pointer;">
                        Marcar nivel como completado
                    </button>
                </div>

                <h1 id="lessonTitle">Cargando clase...</h1>
                <p id="lessonDescription"></p>

                <div class="resources-card" id="resourcesCard" style="display: none;">
                    <h3>Material Complementario</h3>
                    <div id="resourcesLinks" style="display:flex; gap:10px; flex-wrap:wrap;"></div>
                </div>
            </div>

            <div class="comments-section">
                <div style="display:flex; gap:10px; margin-bottom:14px; border-bottom:1px solid #e5e7eb; padding-bottom:10px;">
                    <button type="button" id="tabChatPrivado" class="btn-submit-comment" style="background-color:#2563eb;">Mensaje Privado al Instructor</button>
                    <button type="button" id="tabResenasCurso" class="btn-submit-comment" style="background-color:#6b7280;">Calificar y Comentar Curso</button>
                </div>

                <div id="panelChatPrivado">
                    <p class="comments-subtitle">Conversación privada directa con el instructor de este curso.</p>
                    <div id="chatMessagesList" style="max-height:260px; overflow-y:auto; display:flex; flex-direction:column; gap:10px; margin-bottom:14px; padding:10px; background:#f9fafb; border-radius:8px; border:1px solid #e5e7eb;"></div>
                    <form id="privateMessageForm">
                        <textarea id="privateMessageText" rows="2" placeholder="Escribe un mensaje privado para tu instructor..." required></textarea>
                        <button type="submit" class="btn-submit-comment">Enviar Mensaje Privado</button>
                    </form>
                </div>

                <div id="panelResenasCurso" style="display:none;">
                    <p class="comments-subtitle">Comparte tu opinión y calificación (1 a 5 estrellas) sobre este curso.</p>
                    <form id="commentForm">
                        <div style="margin-bottom:10px;">
                            <label for="ratingSelect" style="font-weight:600; font-size:14px;">Calificación: </label>
                            <select id="ratingSelect" style="padding:6px 10px; border-radius:6px; border:1px solid #d1d5db;">
                                <option value="5">★★★★★ (5 - Excelente)</option>
                                <option value="4">★★★★☆ (4 - Muy bueno)</option>
                                <option value="3">★★★☆☆ (3 - Bueno)</option>
                                <option value="2">★★☆☆☆ (2 - Regular)</option>
                                <option value="1">★☆☆☆☆ (1 - Malo)</option>
                            </select>
                        </div>
                        <textarea id="commentText" rows="3" placeholder="Escribe tu reseña del curso..." required></textarea>
                        <button type="submit" class="btn-submit-comment">Publicar Reseña</button>
                    </form>
                    <hr class="comments-divider">
                    <div id="commentsList"></div>
                </div>
            </div>

        </section>

        <aside class="sidebar-playlist">
            <h2 id="courseSidebarTitle">Contenido del Curso</h2>
            
            <div class="levels-list" id="levelsList">
                <p>Cargando niveles...</p>
            </div>

            <div class="progress-section">
                <label id="progressLabel">Tu avance general: 0%</label>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" id="progressBarFill" style="width: 0%;"></div>
                </div>
                <a id="btnDiplomaPlayer" href="#" class="btn-download" style="display:none; text-align:center; margin-top:14px; background-color:#2563eb;">
                    Ver Diploma Obtenido
                </a>
            </div>
        </aside>

    </main>

    <script src="js/reproductor.js?v=5"></script>
</body>
</html>