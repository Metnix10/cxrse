<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Kardex - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/kardex.css">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

    <main class="kardex-container">
        <div class="kardex-header">
            <h1>Kardex de Cursos</h1>
            <p>Historial académico, avance de niveles y certificados obtenidos.</p>
        </div>

        <section class="filters-card">
            <h3>Filtrar Kardex</h3>
            <form id="kardexFilterForm" class="kardex-filters-grid">
                
                <div class="filter-group">
                    <label for="startDate">Inscripción desde:</label>
                    <input type="date" id="startDate">
                </div>

                <div class="filter-group">
                    <label for="endDate">Inscripción hasta:</label>
                    <input type="date" id="endDate">
                </div>

                <div class="filter-group">
                    <label for="filterCategory">Categoría:</label>
                    <select id="filterCategory">
                        <option value="all">Todas las categorías</option>
                        <option value="Desarrollo Web">Desarrollo Web</option>
                        <option value="Design">Design</option>
                        <option value="IT & Software">IT & Software</option>
                        <option value="Marketing">Marketing</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filterStatus">Estado del curso:</label>
                    <select id="filterStatus">
                        <option value="all">Todos los cursos</option>
                        <option value="completed">Solo terminados</option>
                        <option value="incomplete">Solo en progreso</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filterActive">Disponibilidad:</label>
                    <select id="filterActive">
                        <option value="all">Todos</option>
                        <option value="active">Solo cursos activos</option>
                        <option value="inactive">Cursos dados de baja</option>
                    </select>
                </div>

                <div class="filter-group btn-group">
                    <button type="submit" class="btn-filter">Aplicar Filtros</button>
                </div>
            </form>
        </section>

        <section class="kardex-table-wrapper">
            <table class="kardex-table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Categoría</th>
                        <th>Fecha Inscripción</th>
                        <th>Último Ingreso</th>
                        <th>Avance</th>
                        <th>Estatus</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="kardexTableBody">
                    <tr>
                        <td colspan="7" style="text-align:center;">Cargando tu historial académico...</td>
                    </tr>
                </tbody>
            </table>

            <div id="noKardexResults" class="no-results-msg" style="display: none;">
                No se encontraron cursos que coincidan.
            </div>
        </section>
    </main>

    <script src="js/kardex.js?v=2"></script>
</body>
</html>