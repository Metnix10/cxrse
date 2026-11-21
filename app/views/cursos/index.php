<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cxrse - Catálogo de Cursos</title>
    <link rel="stylesheet" href="css/global.css?v=2">
<link rel="stylesheet" href="css/index.css?v=2">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

    <section class="hero-search">
        <div class="search-container">
            <h1>Encuentra tu curso ideal</h1>
            <p>Aprende con instructores expertos.</p>
            
            <form id="searchForm" class="search-box">
                <div class="input-group">
                    <input type="text" id="searchInput" placeholder="Buscar por título o palabra clave">
                    <button type="submit" class="btn-search">Buscar</button>
                </div>
                
                <div class="advanced-filters">
                    <div class="filter-item">
                        <label for="filterCategory">Categoría:</label>
                        <select id="filterCategory">
                            <option value="">Todas las categorías</option>
                            <option value="IT & Software">IT & Software</option>
                            <option value="Design">Design</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Desarrollo Web">Desarrollo Web</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label for="filterInstructor">Instructor:</label>
                        <select id="filterInstructor">
                            <option value="">Todos los instructores</option>
                            <option value="Carlos López">Carlos López</option>
                            <option value="Ana Martínez">Ana Martínez</option>
                            <option value="Roberto Gómez">Roberto Gómez</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label for="filterDateFrom">Desde:</label>
                        <input type="date" id="filterDateFrom">
                    </div>

                    <div class="filter-item">
                        <label for="filterDateTo">Hasta:</label>
                        <input type="date" id="filterDateTo">
                    </div>
                </div>
            </form>
        </div>
    </section>

    <main class="main-layout">
        
        <aside class="sidebar-categories">
            <h3>Categorías</h3>
            <ul id="categoryList">
                <li class="category-item active" data-category="all">
                    <span>Todas las categorías</span>
                </li>
                <li class="category-item" data-category="IT & Software">
                    <span>IT & Software</span>
                </li>
                <li class="category-item" data-category="Design">
                    <span>Design</span>
                </li>
                <li class="category-item" data-category="Marketing">
                    <span>Marketing</span>
                </li>
                <li class="category-item" data-category="Desarrollo Web">
                    <span>Desarrollo Web</span>
                </li>
            </ul>
        </aside>

        <section class="courses-section">
            
            <div class="tabs-bar">
                <button class="tab-btn active" data-tab="all">Todos los cursos</button>
                <button class="tab-btn" data-tab="best-rated">Mejor calificados</button>
                <button class="tab-btn" data-tab="best-sellers">Más vendidos</button>
                <button class="tab-btn" data-tab="recent">Más recientes</button>
            </div>

            <div class="courses-grid" id="coursesContainer">
            </div>

            <div id="noResults" class="no-results" style="display: none;">
                <p>No se encontraron cursos activos con tu búsqueda.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2026 Cxrse </p>
    </footer>

    <script src="js/index.js?v=4"></script>
</body>
</html>