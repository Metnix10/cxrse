<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Cxrse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin-categorias.css">
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
                <a href="index.php?url=instructor">Instructor</a>
                <a href="index.php?url=admin" class="active">Admin</a>
            </nav>

            <div class="user-profile-menu">
                <a href="index.php?url=perfil" class="avatar-link">
                    <img src="https://picsum.photos/40" alt="Avatar Admin" class="user-avatar">
                    <span class="user-name">Admin</span>
                </a>
            </div>
        </div>
    </header>

    <main class="admin-container">
        
        <div class="admin-subnav">
            <a href="index.php?url=admin" class="subnav-link">Gestión de Usuarios y Reportes</a>
            <a href="index.php?url=admin-categorias" class="subnav-link active">Gestión de Categorías</a>
        </div>

        <div class="admin-header">
            <h1>Administración de Categorías</h1>
            <p>Registra nuevas categorías para la plataforma, modifica las existentes o elimínalas.</p>
        </div>

        <div class="category-grid">
            
            <section class="admin-card">
                <h2 id="formTitle">Crear Nueva Categoría</h2>
                
                <form id="categoryForm" action="index.php?url=guardar-categoria" method="POST">
                    <input type="hidden" id="categoryId" name="id" value="">

                    <div class="form-group mb-15">
                        <label for="nombre">Nombre de la Categoría</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej. Inteligencia Artificial" required>
                    </div>

                    <div class="form-group mb-20">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" class="form-control" placeholder="Describe brevemente el tipo de cursos que pertenecen a esta categoría..."></textarea>
                    </div>

                    <div class="form-actions-category">
                        <button type="button" id="btnCancelEdit" class="btn-cancel" style="display: none;">Cancelar</button>
                        <button type="submit" id="btnSubmitCategory" class="btn-filter">Guardar Categoría</button>
                    </div>
                </form>
            </section>

            <section class="admin-card">
                <h2>Categorías Registradas</h2>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <tr data-id="1">
                                <td><strong>Desarrollo Web</strong></td>
                                <td>Cursos de HTML, CSS, JavaScript, PHP, etc.</td>
                                <td>18-Sep-2026</td>
                                <td>
                                    <button class="btn-action btn-edit" onclick="editCategory(1, 'Desarrollo Web', 'Cursos de HTML, CSS, JavaScript, PHP, etc.')">Editar</button>
                                    <button class="btn-action btn-delete" onclick="deleteCategory(1)">Eliminar</button>
                                </td>
                            </tr>
                            <tr data-id="2">
                                <td><strong>Design</strong></td>
                                <td>Diseño UI/UX, Figma, Ilustración</td>
                                <td>18-Sep-2026</td>
                                <td>
                                    <button class="btn-action btn-edit" onclick="editCategory(2, 'Design', 'Diseño UI/UX, Figma, Ilustración')">Editar</button>
                                    <button class="btn-action btn-delete" onclick="deleteCategory(2)">Eliminar</button>
                                </td>
                            </tr>
                            <tr data-id="3">
                                <td><strong>IT & Software</strong></td>
                                <td>Redes, Seguridad y Sistemas Operativos</td>
                                <td>18-Sep-2026</td>
                                <td>
                                    <button class="btn-action btn-edit" onclick="editCategory(3, 'IT & Software', 'Redes, Seguridad y Sistemas Operativos')">Editar</button>
                                    <button class="btn-action btn-delete" onclick="deleteCategory(3)">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

    </main>

    <script src="js/admin-categorias.js"></script>
</body>
</html>