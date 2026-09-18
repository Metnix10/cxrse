<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproductor de Curso - Cxrse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/reproductor.css">
</head>
<body>

    <header class="navbar">
        <div class="nav-container">
            <a href="index.php?url=home" class="logo">
                <img src="img/logo.png" alt="Logo Cxrse">
            </a>
            
            <nav class="nav-links">
                <a href="index.php?url=home">Cursos</a>
                <a href="index.php?url=kardex" class="active">Kardex</a>
                <a href="index.php?url=instructor">Instructor</a>
                <a href="index.php?url=admin">Admin</a>
            </nav>

            <div class="user-profile-menu">
                <a href="index.php?url=perfil" class="avatar-link">
                    <img src="https://picsum.photos/40" alt="Avatar Usuario" class="user-avatar">
                    <span class="user-name">Milton Torres</span>
                </a>
            </div>
        </div>
    </header>

    <main class="player-layout">
        
        <section class="main-player-content">
            
            <div class="video-container">
                <video id="courseVideo" controls poster="https://via.placeholder.com/800x450?text=Cargando+Video...">
                    <source src="uploads/videos/ejemplo.mp4" type="video/mp4">
                    Tu navegador no soporta el reproductor de video.
                </video>
            </div>

            <div class="lesson-info">
                <span class="lesson-badge">Nivel 1</span>
                <h1 id="lessonTitle">Master en HTML, CSS y JavaScript desde Cero</h1>
                <p id="lessonDescription">En esta lección aprenderás las etiquetas fundamentales para estructurar tu primera página web.</p>

                <div class="resources-card">
                    <h3>Material</h3>
                    <a href="uploads/pdf/guia-clase.pdf" download class="btn-download">Descargar Guía en PDF</a>
                </div>
            </div>

            <div class="comments-section">
                <h2>Preguntas o Comentarios al Instructor</h2>
                <p class="comments-subtitle">¿Tienes alguna duda sobre este nivel? Escríbela aquí y el instructor te responderá.</p>

                <form id="commentForm">
                    <textarea id="commentText" rows="3" required></textarea>
                    <button type="submit" class="btn-submit-comment">Enviar Mensaje</button>
                </form>

                <hr class="comments-divider">

                <div id="commentsList">
                    <div class="comment-item">
                    </div>
                </div>
            </div>

        </section>

        <aside class="sidebar-playlist">
            <h2>Contenido del Curso</h2>
            
            <div class="levels-list">
                
                <div class="level-item active">
                    <div>
                        <strong>Nivel 1: Introducción a HTML</strong>
                        <span class="level-duration">12 min</span>
                    </div>
                    <span class="active">O</span>
                </div>

                <div class="level-item">
                    <div>
                        <strong>Nivel 2: Estilos CSS y Flexbox</strong>
                        <span class="level-duration">25 min</span>
                    </div>
                    <span class="locked">-</span>
                </div>

                <div class="level-item">
                    <div>
                        <strong>Nivel 3: Fundamentos de JS</strong>
                        <span class="level-duration">40 min</span>
                    </div>
                    <span class="locked">-</span>
                </div>

            </div>

            <div class="progress-section">
                <label>Tu avance general: 33%</label>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: 33%;"></div>
                </div>
            </div>
        </aside>

    </main>

    <script src="js/reproductor.js"></script>
</body>
</html>