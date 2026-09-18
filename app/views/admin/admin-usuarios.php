<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios - Cxrse</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/admin.css">
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
                        <tr>
                            <td>milton@gmail.com</td>
                            <td>Milton Torres</td>
                            <td><span class="role-badge role-student">Estudiante</span></td>
                            <td>0</td>
                            <td><span class="status-badge status-active">Activo</span></td>
                            <td>
                                <button class="btn-action btn-block" onclick="toggleUserStatus(this)">Bloquear</button>
                            </td>
                        </tr>
                        <tr class="blocked-row">
                            <td>juan.perez@outlook.com</td>
                            <td>Juan Pérez</td>
                            <td><span class="role-badge role-student">Estudiante</span></td>
                            <td class="failed-attempts">3 (Límite superado)</td>
                            <td><span class="status-badge status-blocked">Deshabilitado</span></td>
                            <td>
                                <button class="btn-action btn-unblock" onclick="toggleUserStatus(this)">Desbloquear</button>
                            </td>
                        </tr>
                        <tr>
                            <td>carlos.instructor@outloik.com</td>
                            <td>Carlos López</td>
                            <td><span class="role-badge role-instructor">Instructor</span></td>
                            <td>0</td>
                            <td><span class="status-badge status-active">Activo</span></td>
                            <td>
                                <button class="btn-action btn-block" onclick="toggleUserStatus(this)">Bloquear</button>
                            </td>
                        </tr>
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

    </main>

    <script src="js/admin-usuarios.js"></script>
</body>
</html>