<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Curso - Cxrse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard-instructor.css">
    <link rel="stylesheet" href="css/crear-curso.css">
</head>
<body>

    <header class="navbar">
        <div class="nav-container">
            <a href="index.php?url=home" class="logo">
                <img src="img/logo.png" alt="Logo Cxrse">
            </a>
            
            <nav class="nav-links">
                <a href="index.php?url=home">Cursos</a>
                <a href="index.php?url=kardex">Kardex</a>
                <a href="index.php?url=instructor" class="active">Instructor</a>
                <a href="index.php?url=admin">Admin</a>
            </nav>

            <div class="user-profile-menu">
                <a href="index.php?url=perfil" class="avatar-link">
                    <img src="https://picsum.photos/40" alt="Avatar Instructor" class="user-avatar">
                    <span class="user-name">Carlos López (Instructor)</span>
                </a>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        
        <div class="dashboard-header">
            <div>
                <h1>Crear Nuevo Curso</h1>
                <p>Completa la información del curso que deseas agregar.</p>
            </div>
            <a href="index.php?url=instructor" class="btn-secondary">← Volver al Panel</a>
        </div>

        <form id="createCourseForm" action="index.php?url=guardar-curso" method="POST" enctype="multipart/form-data" class="admin-card">
            
            <h2>1. Información General del Curso</h2>
            
            <div class="form-group mb-15">
                <label for="titulo">Título del Curso</label>
                <input type="text" id="titulo" name="titulo" class="form-control" required>
            </div>

            <div class="form-group mb-15">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="form-control" placeholder="Explica de qué trata tu curso" required></textarea>
            </div>

            <div class="form-row mb-20">
                <div class="form-group flex-1">
                    <label for="categoria_id">Categoría</label>
                    <select id="categoria_id" name="categoria_id" class="form-control" required>
                        <option value="">Selecciona una categoría</option>
                        <option value="1">Desarrollo Web</option>
                        <option value="2">Design</option>
                        <option value="3">IT & Software</option>
                        <option value="4">Marketing</option>
                    </select>
                </div>

                <div class="form-group flex-1">
                    <label for="precio">Precio (MXN)</label>
                    <input type="number" step="1.0" min="0" id="precio" name="precio" class="form-control" placeholder="100.00" required>
                </div>
            </div>

            <div class="form-group mb-25">
                <label for="imagen">Imagen de Portada</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" class="file-input" required>
            </div>

            <hr class="section-divider">

            <div class="section-header">
                <h2>2. Niveles / Módulos del Curso</h2>
                <button type="button" id="btnAddLevel" class="btn-create-course btn-add-level">+ Agregar Otro Nivel</button>
            </div>

            <div id="levelsContainer">
                
                <div class="level-card" data-level="1">
                    <div class="level-card-header">
                        <h3 class="level-title">Nivel 1</h3>
                    </div>

                    <div class="form-group mb-10">
                        <label>Título del Nivel / Lección</label>
                        <input type="text" name="niveles[1][titulo]" class="form-control" required>
                    </div>

                    <div class="form-group mb-10">
                        <label>Descripción del Nivel</label>
                        <textarea name="niveles[1][descripcion]" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Video de la Lección (MP4 / WebM)</label>
                        <input type="file" name="niveles[1][video]" accept="video/*" class="file-input" required>
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-filter btn-submit-course">Publicar Curso</button>
            </div>

        </form>

    </main>

    <script src="js/crear-curso.js"></script>
</body>
</html>