<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cxrse - Catálogo de Cursos</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>

    <header class="navbar">
        <div class="nav-container">
            <a href="index.php?url=home" class="logo">
                <img src="img/logo.png" alt="Logo Cxrse">
            </a>
            
            <nav class="nav-links">
                <a href="index.php?url=home" class="active">Cursos</a>
                <a href="index.php?url=kardex">Kardex</a>
                <a href="index.php?url=instructor">Instructor</a>
                <a href="index.php?url=admin">Admin</a>
            </nav>

            <div class="auth-buttons">
                <a href="index.php?url=login" class="btn-secondary">Iniciar Sesión</a>
                <a href="index.php?url=registro" class="btn-primary">Registrarse</a>
            </div>
        </div>
    </header>

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
                
                <article class="course-card" data-category="Desarrollo Web" data-rating="4.9" data-sales="120" data-date="2026-08-10">
                    <div class="card-badge badge-top">Más Vendido</div>
                    <img src="https://via.placeholder.com/300x180?text=Desarrollo+Web+Completo" alt="Portada Curso" class="course-img">
                    <div class="course-body">
                        <span class="course-category">Desarrollo Web</span>
                        <h3 class="course-title">Master en HTML, CSS y JavaScript desde Cero</h3>
                        <p class="course-instructor">Por: Carlos López</p>
                        <div class="course-rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(120 alumnos)</span>
                        </div>
                        <div class="course-footer">
                            <span class="course-price">$499.00 MXN</span>
                            <a href="index.php?url=detalle-curso" class="btn-card">Ver Detalles</a>
                        </div>
                    </div>
                </article>

                <article class="course-card" data-category="Design" data-rating="4.8" data-sales="95" data-date="2026-08-20">
                    <div class="card-badge badge-rating">Mejor Calificado</div>
                    <img src="https://via.placeholder.com/300x180?text=Dise%C3%B1o+UI%2FUX+Figma" alt="Portada Curso" class="course-img">
                    <div class="course-body">
                        <span class="course-category">Design</span>
                        <h3 class="course-title">Diseño de Interfaces UI/UX con Figma</h3>
                        <p class="course-instructor">Por: Ana Martínez</p>
                        <div class="course-rating">
                            <span class="stars">★★★★★</span>
                            <span class="rating-score">4.8</span>
                            <span class="rating-count">(95 alumnos)</span>
                        </div>
                        <div class="course-footer">
                            <span class="course-price">$350.00 MXN</span>
                            <a href="index.php?url=detalle-curso" class="btn-card">Ver Detalles</a>
                        </div>
                    </div>
                </article>

                <article class="course-card" data-category="IT & Software" data-rating="4.7" data-sales="40" data-date="2026-09-01">
                    <div class="card-badge badge-recent">Nuevo</div>
                    <img src="https://via.placeholder.com/300x180?text=Fundamentos+de+Redes" alt="Portada Curso" class="course-img">
                    <div class="course-body">
                        <span class="course-category">IT & Software</span>
                        <h3 class="course-title">Fundamentos de Redes de Computadoras y Seguridad</h3>
                        <p class="course-instructor">Por: Roberto Gómez</p>
                        <div class="course-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="rating-score">4.7</span>
                            <span class="rating-count">(40 alumnos)</span>
                        </div>
                        <div class="course-footer">
                            <span class="course-price font-free">Gratis</span>
                            <a href="index.php?url=detalle-curso" class="btn-card">Ver Detalles</a>
                        </div>
                    </div>
                </article>

                <article class="course-card" data-category="Marketing" data-rating="4.6" data-sales="75" data-date="2026-07-15">
                    <img src="https://via.placeholder.com/300x180?text=Marketing+Digital" alt="Portada Curso" class="course-img">
                    <div class="course-body">
                        <span class="course-category">Marketing</span>
                        <h3 class="course-title">Estrategias de Marketing Digital</h3>
                        <p class="course-instructor">Por: Carlos López</p>
                        <div class="course-rating">
                            <span class="stars">★★★★☆</span>
                            <span class="rating-score">4.6</span>
                            <span class="rating-count">(75 alumnos)</span>
                        </div>
                        <div class="course-footer">
                            <span class="course-price">$299.00 MXN</span>
                            <a href="index.php?url=detalle-curso" class="btn-card">Ver Detalles</a>
                        </div>
                    </div>
                </article>

            </div>

            <div id="noResults" class="no-results" style="display: none;">
                <p>No se encontraron cursos activos con tu búsqueda.</p>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2026 Cxrse </p>
    </footer>

    <script src="js/index.js"></script>
</body>
</html>