<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin-categorias.css">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

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
                            <tr><td colspan="4" style="text-align:center;">Cargando categorías...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

    </main>

    <script src="js/admin-categorias.js?v=2"></script>
</body>
</html>