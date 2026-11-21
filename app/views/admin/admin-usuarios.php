<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

   <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

    <main class="admin-container">
        
        <div class="admin-subnav">
            <a href="index.php?url=admin" class="subnav-link active">Gestión de Usuarios y Reportes</a>
            <a href="index.php?url=admin-categorias" class="subnav-link">Gestión de Categorías</a>
        </div>

        <div class="admin-header">
            <h1>Control de Usuarios</h1>
            <p>Habilita, deshabilita usuarios bloqueados por intentos fallidos.</p>
        </div>

        <section class="admin-card">
            <h2>1. Control de Estado de Usuarios</h2>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Usuario (Email)</th>
                            <th>Nombre Completo</th>
                            <th>Rol</th>
                            <th>Intentos Fallidos</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="userControlTable">
                        <tr><td colspan="6" style="text-align:center;">Cargando usuarios...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-card">
            <div class="report-header-bar">
                <h2>2. Reporte de Usuarios Activos</h2>
                
                <div class="report-toggle">
                    <label for="reportType">Tipo de usuario:</label>
                    <select id="reportType">
                        <option value="instructor">Instructores</option>
                        <option value="student">Estudiantes</option>
                    </select>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead id="reportTableHeader">
                    </thead>
                    <tbody id="reportTableBody">
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-card">
            <h2>3. Moderación de Comentarios de Cursos</h2>
            <p style="font-size:14px; color:#6b7280; margin-bottom:14px;">Da de baja comentarios inapropiados registrando la causa de eliminación.</p>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Alumno</th>
                            <th>Calificación</th>
                            <th>Comentario</th>
                            <th>Estatus</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="commentsModerationBody">
                        <tr><td colspan="6" style="text-align:center;">Cargando comentarios...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="js/admin-usuarios.js?v=2"></script>
</body>
</html>