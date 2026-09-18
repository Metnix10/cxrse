<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Kardex - Cxrse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/kardex.css">
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
                    
                    <tr data-category="Desarrollo Web" data-status="incomplete" data-active="active" data-date="2026-08-10">
                        <td class="course-info-cell">
                            <strong>Master en HTML, CSS y JavaScript</strong>
                            <span class="instructor-tag">Instructor: Carlos López</span>
                        </td>
                        <td><span class="category-badge">Desarrollo Web</span></td>
                        <td>10-Ago-2027</td>
                        <td>14-Sep-2027</td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 60%;"></div>
                            </div>
                            <span class="progress-text">60% (Nivel 3 de 5)</span>
                        </td>
                        <td><span class="status-badge status-progress">En Progreso</span></td>
                        <td>
                            <a href="index.php?url=reproductor" class="btn-action btn-continue">Continuar</a>
                        </td>
                    </tr>

                    <tr data-category="Design" data-status="completed" data-active="active" data-date="2026-06-01">
                        <td class="course-info-cell">
                            <strong>Diseño de Interfaces UI/UX con Figma</strong>
                            <span class="instructor-tag">Instructor: Ana Martínez</span>
                        </td>
                        <td><span class="category-badge">Design</span></td>
                        <td>01-Jun-2027</td>
                        <td>25-Jun-2027</td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar complete" style="width: 100%;"></div>
                            </div>
                            <span class="progress-text">100% (4 de 4 Niveles)</span>
                        </td>
                        <td><span class="status-badge status-complete">Terminado</span></td>
                        <td>
                            <a href="index.php?url=diploma" class="btn-action btn-diploma">Ver Diploma</a>
                        </td>
                    </tr>

                    <tr data-category="IT & Software" data-status="incomplete" data-active="inactive" data-date="2026-05-15">
                        <td class="course-info-cell">
                            <strong>Fundamentos de Redes de Computadoras</strong>
                            <span class="instructor-tag">Instructor: Roberto Gómez</span>
                        </td>
                        <td><span class="category-badge">IT & Software</span></td>
                        <td>15-May-2027</td>
                        <td>20-Mayo-2027</td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: 25%;"></div>
                            </div>
                            <span class="progress-text">25% (Nivel 1 de 4)</span>
                        </td>
                        <td><span class="status-badge status-inactive">Inactivo</span></td>
                        <td>
                            <button class="btn-action btn-disabled" disabled>No Disponible</button>
                        </td>
                    </tr>

                </tbody>
            </table>

            <div id="noKardexResults" class="no-results-msg" style="display: none;">
                No se encontraron cursos que coincidan.
            </div>
        </section>
    </main>

    <script src="js/kardex.js"></script>
</body>
</html>