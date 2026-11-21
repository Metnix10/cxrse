<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Curso - Cxrse</title>
    <link rel="stylesheet" href="css/global.css?v=2">
    <link rel="stylesheet" href="css/detalle-curso.css">
</head>
<body>

    <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

    <section class="course-hero">
        <div class="hero-inner">
            <div class="hero-info">
                <span class="hero-category" id="cursoCategoria">Cargando...</span>
                <h1 id="cursoTitulo">Cargando detalles del curso...</h1>
                <p class="hero-description" id="cursoDescripcion"></p>
                <div class="hero-meta">
                    <span id="cursoInstructor">Por: --</span>
                    <span>•</span>
                    <span class="hero-rating" id="cursoRating">★★★★★ 0.0</span>
                    <span>•</span>
                    <span id="cursoTotalNiveles">0 niveles</span>
                </div>
            </div>
        </div>
    </section>

    <main class="detail-layout">
        <div class="detail-main">
            <section class="content-card">
                <h2>Contenido del Curso (Niveles)</h2>
                <p class="section-subtitle">Puedes adquirir el curso completo o comprar niveles de forma individual.</p>
                <div id="nivelesList" class="niveles-list">
                    <p>Cargando temario...</p>
                </div>
            </section>
        </div>

        <aside class="detail-sidebar">
            <div class="purchase-card">
                <img id="cursoImagen" src="https://via.placeholder.com/400x220?text=Curso" alt="Portada del curso" class="purchase-cover">
                
                <div class="purchase-body">
                    <div class="price-display">
                        <span class="price-label">Precio del curso completo</span>
                        <span class="price-value" id="cursoPrecio">$0.00 MXN</span>
                    </div>

                    <div id="enrolledBanner" class="enrolled-banner" style="display: none;">
                        <p>Ya tienes acceso a este curso.</p>
                        <a id="btnIrReproductor" href="index.php?url=reproductor" class="btn-go-player">Ir al Reproductor</a>
                    </div>

                    <form id="purchaseForm" class="purchase-form">
                        <div class="form-group">
                            <label for="tipoCompra">Modalidad de inscripción:</label>
                            <select id="tipoCompra">
                                <option value="completo">Curso Completo</option>
                            </select>
                        </div>

                        <div class="form-group" id="grupoFormaPago">
                            <label for="formaPago">Forma de pago:</label>
                            <select id="formaPago" required>
                                <option value="Tarjeta">Efectivo</option>
                                <option value="PayPal">PayPal</option>
                            </select>
                        </div>

                        <div class="checkout-summary">
                            <span>Total a pagar:</span>
                            <strong id="totalPagar">$0.00 MXN</strong>
                        </div>

                        <button type="submit" id="btnComprar" class="btn-buy">Inscribirme Ahora</button>
                        <p id="purchaseFeedback" class="purchase-feedback"></p>
                    </form>
                </div>
            </div>
        </aside>
    </main>

    <script>
        window.CXRSE_USER = {
            loggedIn: <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>,
            rol: <?= json_encode($_SESSION['rol'] ?? '') ?>
        };
    </script>
    <script src="js/detalle-curso.js"></script>
</body>
</html>