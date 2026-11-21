<?php
class AuthMiddleware {
    public static function iniciarSesionSegura() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params(0, '/');
            session_start();
        }
    }

    public static function verificarVista($rolRequerido = null) {
        self::iniciarSesionSegura();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /cxrse/public/index.php?url=login&error=acceso_denegado");
            exit;
        }
        if ($rolRequerido !== null && ($_SESSION['rol'] ?? '') !== $rolRequerido && ($_SESSION['rol'] ?? '') !== 'Admin') {
            header("Location: /cxrse/public/index.php?url=login&error=rol_invalido");
            exit;
        }
    }

    public static function verificarSesion() {
        self::verificarVista();
    }

    public static function verificarRol($rolRequerido) {
        self::verificarVista($rolRequerido);
    }

    public static function verificarApi($rolRequerido = null) {
        self::iniciarSesionSegura();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "No autenticado. Inicia sesión para continuar."]);
            exit;
        }
        if ($rolRequerido !== null && ($_SESSION['rol'] ?? '') !== $rolRequerido) {
            http_response_code(403);
            echo json_encode(["error" => "Acceso denegado. Se requiere rol: " . $rolRequerido]);
            exit;
        }
    }
}