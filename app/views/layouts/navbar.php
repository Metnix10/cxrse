<?php
$currentUrl = $_GET['url'] ?? 'home';
$isCatalogo = in_array($currentUrl, ['home', 'detalle-curso'], true);
$isKardex = in_array($currentUrl, ['kardex', 'reproductor'], true);
$isInstructor = in_array($currentUrl, ['instructor', 'crear-curso'], true);
$isAdmin = in_array($currentUrl, ['admin', 'admin-categorias'], true);
?>
<header class="navbar">
    <div class="nav-container">
        <a href="index.php?url=home" class="logo">
            <img src="img/logo.png" alt="Logo Cxrse">
        </a>
        
        <nav class="nav-links">
            <a href="index.php?url=home" class="<?= $isCatalogo ? 'active' : '' ?>">Cursos</a>
            <a href="index.php?url=kardex" class="<?= $isKardex ? 'active' : '' ?>">Kardex</a>
            <a href="index.php?url=instructor" class="<?= $isInstructor ? 'active' : '' ?>">Instructor</a>
            <a href="index.php?url=admin" class="<?= $isAdmin ? 'active' : '' ?>">Admin</a>
        </nav>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
                $nombreUsuario = htmlspecialchars($_SESSION['nombre'] ?? 'Usuario');
                $rolUsuario = htmlspecialchars($_SESSION['rol'] ?? 'Estudiante');
                $rolClase = strtolower($rolUsuario);
                $fotoSession = $_SESSION['foto'] ?? '';
                $avatarUrl = (strpos($fotoSession, 'http') === 0)
                    ? htmlspecialchars($fotoSession)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($nombreUsuario) . '&background=2563eb&color=fff';
            ?>
            <div class="user-profile-menu">
                <a href="index.php?url=perfil" class="avatar-link" title="Ver mi perfil">
                    <img src="<?= $avatarUrl ?>" alt="Avatar" class="user-avatar">
                    <div class="user-meta">
                        <span class="user-name"><?= $nombreUsuario ?></span>
                        <span class="role-badge role-<?= $rolClase ?>"><?= $rolUsuario ?></span>
                    </div>
                </a>
                <a href="index.php?url=logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        <?php else: ?>
            <div class="auth-buttons">
                <a href="index.php?url=login" class="btn-secondary">Iniciar Sesión</a>
                <a href="index.php?url=registro" class="btn-primary">Registrarse</a>
            </div>
        <?php endif; ?>
    </div>
</header>