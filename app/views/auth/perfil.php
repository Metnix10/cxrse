<?php
require_once __DIR__ . '/../../middlewares/AuthMiddleware.php';
AuthMiddleware::iniciarSesionSegura();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?url=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/perfil.css">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

    <main class="profile-container">
        <div class="profile-header">
            <h1>Mi Perfil</h1>
            <p>Administra tu información personal, fotografía de avatar y contraseña.</p>
        </div>

        <div class="profile-grid">
            <aside class="profile-card summary-card">
                <div class="avatar-wrapper">
                    <img id="avatarPreview" src="https://via.placeholder.com/140?text=Usuario" alt="Foto de perfil" class="avatar-img">
                </div>
                <h2 id="summaryName">Cargando...</h2>
                <span id="summaryRole" class="role-pill">--</span>
                <p id="summaryEmail" class="summary-email">--</p>
                <p class="summary-date">Miembro desde: <strong id="summaryDate">--</strong></p>
            </aside>

            <section class="profile-card form-card">
                <h2>Editar Datos Personales</h2>

                <form id="profileForm">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="genero">Género</label>
                            <select id="genero" required>
                                <option value="Femenino">Femenino</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fechaNacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fechaNacimiento" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico (no editable)</label>
                        <input type="email" id="email" disabled class="input-disabled">
                    </div>

                    <div class="form-group">
                        <label for="fotoInput">Cambiar Foto de Perfil (opcional)</label>
                        <input type="file" id="fotoInput" accept="image/*">
                        <small class="help-text">Formatos JPG, PNG o WebP. Se subirá automáticamente a la nube.</small>
                    </div>

                    <hr class="divider">

                    <h3>Seguridad</h3>
                    <div class="form-group">
                        <label for="password">Nueva Contraseña (dejar en blanco para conservar la actual)</label>
                        <input type="password" id="password" placeholder="Mínimo 8 caracteres, mayúscula, minúscula, número y símbolo">
                    </div>

                    <div class="form-actions">
                        <button type="submit" id="btnSaveProfile" class="btn-save">Guardar Cambios</button>
                    </div>
                    <p id="profileFeedback" class="feedback-msg"></p>
                </form>
            </section>
        </div>
    </main>

    <script src="js/perfil.js?v=3"></script>
</body>
</html>