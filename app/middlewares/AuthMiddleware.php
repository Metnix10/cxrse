<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthMiddleware {
    
    public static function verificarSesion() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../../auth/login.php?error=acceso_denegado");
            exit;
        }
    }

    public static function verificarRol($rol_requerido) {
        self::verificarSesion();
        
        if ($_SESSION['rol'] !== $rol_requerido && $_SESSION['rol'] !== 'Admin') {
            header("Location: ../../auth/login.php?error=rol_invalido");
            exit;
        }
    }
}
?>