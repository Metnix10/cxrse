<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body class="auth-page">

    <div class="registro-container">
        <h2>Iniciar Sesión</h2>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div id="error-msg" class="error-message"></div>

            <button type="submit" class="btn-submit">Ingresar</button>
        </form>

        <div class="login-link">
            ¿No tienes una cuenta? <a href="index.php?url=registro">Regístrate aquí</a>
        </div>
        <div class="login-link" style="margin-top: 10px;">
            <a href="index.php?url=home" style="color: #6b7280; font-weight: normal;">Volver al inicio</a>
        </div>
    </div>
    
    <script src="js/login.js"></script>
</body>
</html>