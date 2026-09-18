<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body class="auth-page">

    <div class="registro-container">
        <h2>Crear Cuenta</h2>
        
        <form id="registroForm">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="genero">Género</label>
                <select id="genero" name="genero" required>
                    <option value="">Selecciona una opción</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Masculino">Masculino</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>

            <div class="form-group">
                <label for="foto">Foto de Perfil</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <span class="helper-text">Mínimo 8 caracteres, con al menos 1 mayúscula, 1 número y 1 carácter especial.</span>
            </div>

            <div id="error-msg" class="error-message"></div>

            <button type="submit" class="btn-submit">Registrarse</button>
        </form>

        <div class="login-link">
            ¿Ya tienes una cuenta? <a href="index.php?url=login">Inicia Sesión aquí</a>
        </div>
        <div class="login-link" style="margin-top: 10px;">
            <a href="index.php?url=home" style="color: #6b7280; font-weight: normal;">Volver al inicio</a>
        </div>
    </div>

    <script src="js/registro.js"></script>
</body>
</html>