<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Instructor - Cxrse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard-instructor.css">
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
                <h1>Reporte de Ventas e Ingresos</h1>
                <p>Consulta el rendimiento financiero y avance de alumnos en tus cursos.</p>
            </div>
            <a href="index.php?url=crear-curso" class="btn-create-course">+ Crear Nuevo Curso</a>
        </div>

        <section class="filters-card">
            <h3>Filtros de Reporte</h3>
            <form id="instructorFilterForm" class="filters-grid">
                
                <div class="filter-group">
                    <label for="startDate">Fecha creación desde:</label>
                    <input type="date" id="startDate">
                </div>

                <div class="filter-group">
                    <label for="endDate">Fecha creación hasta:</label>
                    <input type="date" id="endDate">
                </div>

                <div class="filter-group">
                    <label for="filterCategory">Categoría:</label>
                    <select id="filterCategory">
                        <option value="all">Todas las categorías</option>
                        <option value="Desarrollo Web">Desarrollo Web</option>
                        <option value="Marketing">Marketing</option>
                        <option value="IT & Software">IT & Software</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filterStatus">Estatus:</label>
                    <select id="filterStatus">
                        <option value="all">Todos los cursos</option>
                        <option value="active">Solo cursos activos</option>
                        <option value="inactive">Cursos dados de baja</option>
                    </select>
                </div>

                <div class="filter-group btn-group">
                    <button type="submit" class="btn-filter">Aplicar Filtros</button>
                </div>
            </form>
        </section>

        <section class="report-section">
            <h2>1. Resumen General de Cursos Ofrecidos</h2>
            
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Categoría</th>
                            <th>Estatus</th>
                            <th>Alumnos Inscritos</th>
                            <th>Nivel Promedio</th>
                            <th>Total Ingresos</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <tr data-category="Desarrollo Web" data-status="active" data-date="2026-08-10">
                            <td><strong>Master en HTML, CSS y JavaScript</strong></td>
                            <td><span class="badge">Desarrollo Web</span></td>
                            <td><span class="badge badge-active">Activo</span></td>
                            <td>120</td>
                            <td>Nivel 3.4</td>
                            <td class="amount font-bold">$59,880.00 MXN</td>
                            <td>
                                <button class="btn-view-detail" data-course="1">Ver Alumnos</button>
                            </td>
                        </tr>
                        <tr data-category="Marketing" data-status="active" data-date="2026-07-15">
                            <td><strong>Estrategias de Marketing Digital</strong></td>
                            <td><span class="badge">Marketing</span></td>
                            <td><span class="badge badge-active">Activo</span></td>
                            <td>75</td>
                            <td>Nivel 2.1</td>
                            <td class="amount font-bold">$22,425.00 MXN</td>
                            <td>
                                <button class="btn-view-detail" data-course="2">Ver Alumnos</button>
                            </td>
                        </tr>
                        <tr data-category="IT & Software" data-status="inactive" data-date="2026-04-01">
                            <td><strong>Introducción a PHP Puro (Baja Lógica)</strong></td>
                            <td><span class="badge">IT & Software</span></td>
                            <td><span class="badge badge-inactive">Dado de baja</span></td>
                            <td>15</td>
                            <td>Nivel 4.0</td>
                            <td class="amount font-bold">$4,500.00 MXN</td>
                            <td>
                                <button class="btn-view-detail" data-course="3">Ver Alumnos</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="payment-breakdown-card">
                <h3>Total de Ingresos Desglosado por Forma de Pago</h3>
                <div class="breakdown-grid">
                    <div class="breakdown-item">
                        <span class="payment-method">Tarjeta de Crédito / Débito:</span>
                        <span class="payment-amount">$52,305.00 MXN</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="payment-method">PayPal:</span>
                        <span class="payment-amount">$28,500.00 MXN</span>
                    </div>
                    <div class="breakdown-item">
                        <span class="payment-method">Transferencia SPEI:</span>
                        <span class="payment-amount">$6,000.00 MXN</span>
                    </div>
                    <div class="breakdown-item breakdown-total">
                        <span class="payment-method">TOTAL ACUMULADO:</span>
                        <span class="payment-amount">$86,805.00 MXN</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="report-section" id="detailSection">
            <div class="section-title-bar">
                <h2>2. Detalle de Alumnos Inscritos: <span id="selectedCourseName">Master en HTML, CSS y JavaScript</span></h2>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Fecha Inscripción</th>
                            <th>Nivel de Avance</th>
                            <th>Precio Pagado</th>
                            <th>Forma de Pago</th>
                        </tr>
                    </thead>
                    <tbody id="detailTableBody">
                        <tr>
                            <td>Milton Torres</td>
                            <td>10-Ago-2026</td>
                            <td>Nivel 3 (60%)</td>
                            <td class="amount">$499.00 MXN</td>
                            <td>Tarjeta de Crédito</td>
                        </tr>
                        <tr>
                            <td>María Fernández</td>
                            <td>12-Ago-2026</td>
                            <td>Nivel 5 (100%)</td>
                            <td class="amount">$499.00 MXN</td>
                            <td>PayPal</td>
                        </tr>
                        <tr>
                            <td>Jorge Ramírez</td>
                            <td>18-Ago-2026</td>
                            <td>Nivel 1 (20%)</td>
                            <td class="amount">$499.00 MXN</td>
                            <td>Transferencia SPEI</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="course-total-bar">
                <span>Total generado por este curso:</span>
                <span class="total-amount">$59,880.00 MXN</span>
            </div>
        </section>

    </main>

    <script src="js/dashboard-instructor.js"></script>
</body>
</html>