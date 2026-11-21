<?php
require_once __DIR__ . '/../../middlewares/AuthMiddleware.php';
AuthMiddleware::verificarRol('Instructor'); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Instructor - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/dashboard-instructor.css?v=3">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <tr>
                            <td colspan="7" style="text-align:center;">Cargando cursos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="payment-breakdown-card">
                <h3>Total de Ingresos Desglosado por Forma de Pago</h3>
                <div class="breakdown-grid">
    <div class="breakdown-item">
        <span class="payment-method">PayPal:</span>
        <span class="payment-amount" id="totalPaypal">$0.00 MXN</span>
    </div>
    <div class="breakdown-item">
        <span class="payment-method">Efectivo:</span>
        <span class="payment-amount" id="totalEfectivo">$0.00 MXN</span>
    </div>
    <div class="breakdown-item breakdown-total">
        <span class="payment-method">TOTAL ACUMULADO:</span>
        <span class="payment-amount" id="totalAcumulado">$0.00 MXN</span>
    </div>
</div>
            </div>
        </section>

        <section class="report-section" id="detailSection">
            <div class="section-title-bar">
                <h2>2. Detalle de Alumnos Inscritos: <span id="selectedCourseName">Selecciona un curso</span></h2>
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
                            <td colspan="5" style="text-align:center;">Haz clic en "Ver Alumnos" en algún curso de la tabla superior.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="course-total-bar">
                <span>Total generado por este curso:</span>
                <span class="total-amount" id="selectedCourseTotal">$0.00 MXN</span>
            </div>
        </section>

        <section class="report-section" id="messagesSection">
            <h2>3. Mensajes Privados de Alumnos</h2>
            <div style="display:grid; grid-template-columns: 300px 1fr; gap:16px; margin-top:14px;">
                <div id="instructorThreadsList" style="border:1px solid #e5e7eb; border-radius:8px; padding:10px; background:#f9fafb; max-height:340px; overflow-y:auto;">
                    <p style="font-size:13px; color:#6b7280;">Cargando conversaciones...</p>
                </div>
                <div style="border:1px solid #e5e7eb; border-radius:8px; padding:14px; display:flex; flex-direction:column; justify-content:space-between; min-height:300px;">
                    <div id="instructorChatHistory" style="max-height:220px; overflow-y:auto; display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
                        <p style="font-size:13px; color:#6b7280;">Selecciona una conversación a la izquierda para responder.</p>
                    </div>
                    <form id="instructorReplyForm" style="display:none; gap:8px;">
                        <input type="text" id="instructorReplyInput" placeholder="Escribe tu respuesta al alumno..." required style="flex:1; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                        <button type="submit" class="btn-filter">Responder</button>
                    </form>
                </div>
            </div>
        </section>

    </main>

    <script src="js/dashboard-instructor.js?v=6"></script>
</body>
</html>