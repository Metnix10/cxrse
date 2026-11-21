document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const cursoId = params.get('id');

    if (!cursoId) {
        window.location.href = 'index.php?url=home';
        return;
    }

    const tipoCompraSelect = document.getElementById('tipoCompra');
    const formaPagoSelect = document.getElementById('formaPago');
    const grupoFormaPago = document.getElementById('grupoFormaPago');
    const totalPagarEl = document.getElementById('totalPagar');
    const purchaseForm = document.getElementById('purchaseForm');
    const btnComprar = document.getElementById('btnComprar');
    const purchaseFeedback = document.getElementById('purchaseFeedback');
    const nivelesList = document.getElementById('nivelesList');

    let cursoActual = null;
    let inscripcionesPrevias = [];

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    function formatoMoneda(valor) {
        const num = parseFloat(valor) || 0;
        return num <= 0 ? 'Gratis' : `$${num.toFixed(2)} MXN`;
    }

    async function inicializar() {
        try {
            const [resCurso, resEstado] = await Promise.all([
                fetch(`../app/controllers/CursoController.php?recurso=cursos&id=${cursoId}`, { credentials: 'include' }),
                fetch(`../app/controllers/CursoController.php?recurso=estado-inscripcion&curso_id=${cursoId}`, { credentials: 'include' })
            ]);

            const curso = await resCurso.json();
            if (!resCurso.ok) {
                throw new Error(curso.error || 'No se pudo cargar el curso.');
            }

            cursoActual = curso;
            inscripcionesPrevias = resEstado.ok ? await resEstado.json() : [];

            renderizarDetalle(cursoActual);
            actualizarTotal();
            await cargarResenasPublicas();
        } catch (error) {
            console.error(error);
            document.getElementById('cursoTitulo').textContent = 'Curso no disponible';
            nivelesList.innerHTML = `<p style="color:#dc2626;">${escaparHTML(error.message)}</p>`;
            purchaseForm.style.display = 'none';
        }
    }

    async function cargarResenasPublicas() {
        const reviewsList = document.getElementById('publicReviewsList');
        if (!reviewsList) return;

        try {
            const res = await fetch(`../app/controllers/InteraccionController.php?recurso=comentarios&curso_id=${cursoId}`);
            const comentarios = await res.json();

            if (!Array.isArray(comentarios) || comentarios.length === 0) {
                reviewsList.innerHTML = '<p style="color:#6b7280; font-size:14px;">Este curso aún no tiene reseñas publicadas.</p>';
                return;
            }

            reviewsList.innerHTML = comentarios.map(c => {
                const eliminado = parseInt(c.eliminado, 10) === 1;
                if (eliminado) {
                    return `
                        <div class="nivel-item" style="background:#f9fafb; border-left:3px solid #9ca3af;">
                            <p style="margin:0; font-size:14px; color:#6b7280; font-style:italic;">${escaparHTML(c.comentario)}</p>
                        </div>
                    `;
                }
                const calif = Math.max(1, Math.min(5, parseInt(c.calificacion, 10) || 5));
                const stars = '★'.repeat(calif) + '☆'.repeat(5 - calif);
                return `
                    <div class="nivel-item" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                            <strong>${escaparHTML(c.usuario_nombre)} <span style="color:#f59e0b; margin-left:6px;">${stars}</span></strong>
                            <small style="color:#6b7280;">${escaparHTML(c.fecha)}</small>
                        </div>
                        <p style="margin:0; font-size:14px; color:#374151;">${escaparHTML(c.comentario)}</p>
                    </div>
                `;
            }).join('');
        } catch (err) {
            reviewsList.innerHTML = '<p style="color:#dc2626; font-size:14px;">No se pudieron cargar las reseñas.</p>';
        }
    }

    function renderizarDetalle(curso) {
        document.getElementById('cursoCategoria').textContent = curso.categoria_nombre || 'Curso';
        document.getElementById('cursoTitulo').textContent = curso.titulo;
        document.getElementById('cursoDescripcion').textContent = curso.descripcion;
        document.getElementById('cursoInstructor').textContent = `Por: ${curso.instructor_nombre}`;

        const rating = parseFloat(curso.promedio_calificacion || 0);
        const estrellas = '★'.repeat(Math.round(rating)) + '☆'.repeat(5 - Math.round(rating));
        document.getElementById('cursoRating').textContent = `${estrellas} ${rating.toFixed(1)}`;

        const niveles = curso.niveles || [];
        document.getElementById('cursoTotalNiveles').textContent = `${niveles.length} ${niveles.length === 1 ? 'nivel' : 'niveles'}`;

        if (curso.imagen && curso.imagen.startsWith('http')) {
            document.getElementById('cursoImagen').src = curso.imagen;
        }

        document.getElementById('cursoPrecio').textContent = formatoMoneda(curso.precio);

        const tieneCursoCompleto = inscripcionesPrevias.some(i => i.nivel_id === null);
        if (tieneCursoCompleto) {
            document.getElementById('enrolledBanner').style.display = 'block';
            document.getElementById('btnIrReproductor').href = `index.php?url=reproductor&curso_id=${curso.id}`;
            purchaseForm.style.display = 'none';
        }

        tipoCompraSelect.innerHTML = `<option value="completo">Curso Completo (${formatoMoneda(curso.precio)})</option>`;
        niveles.forEach(niv => {
            const precioNiv = parseFloat(niv.precio) || 0;
            if (precioNiv > 0) {
                tipoCompraSelect.innerHTML += `<option value="${niv.id}">Nivel ${niv.numero_nivel}: ${escaparHTML(niv.titulo)} (${formatoMoneda(precioNiv)})</option>`;
            }
        });

        if (niveles.length === 0) {
            nivelesList.innerHTML = '<p>Este curso aún no tiene niveles publicados.</p>';
            return;
        }

        nivelesList.innerHTML = niveles.map(niv => {
            const precioNiv = parseFloat(niv.precio) || 0;
            const nivelAdquirido = tieneCursoCompleto || inscripcionesPrevias.some(i => String(i.nivel_id) === String(niv.id));

            let tags = '<span class="tag-recurso">Video incluido</span>';
            if (niv.pdf_url) tags += '<span class="tag-recurso">PDF adjunto</span>';
            if (niv.link_externo) tags += '<span class="tag-recurso">Recurso externo</span>';

            let accionHTML = '';
            if (nivelAdquirido) {
                accionHTML = `<a href="index.php?url=reproductor&curso_id=${curso.id}&nivel=${niv.numero_nivel}" class="btn-buy-level">Ver clase</a>`;
            } else if (precioNiv > 0) {
                accionHTML = `
                    <span class="nivel-price">${formatoMoneda(precioNiv)}</span>
                    <button type="button" class="btn-buy-level" data-nivel-id="${niv.id}">Seleccionar nivel</button>
                `;
            } else {
                accionHTML = `<span class="nivel-price">Incluido en curso</span>`;
            }

            return `
                <div class="nivel-item">
                    <div class="nivel-info">
                        <h4>Nivel ${niv.numero_nivel}: ${escaparHTML(niv.titulo)}</h4>
                        <p>${escaparHTML(niv.descripcion || 'Contenido práctico del nivel.')}</p>
                        <div class="nivel-tags">${tags}</div>
                    </div>
                    <div class="nivel-action">
                        ${accionHTML}
                    </div>
                </div>
            `;
        }).join('');

        document.querySelectorAll('.btn-buy-level[data-nivel-id]').forEach(btn => {
            btn.addEventListener('click', function () {
                tipoCompraSelect.value = this.getAttribute('data-nivel-id');
                actualizarTotal();
                purchaseForm.scrollIntoView({ behavior: 'smooth' });
            });
        });
    }

    function obtenerPrecioSeleccionado() {
        if (!cursoActual) return 0;
        const seleccion = tipoCompraSelect.value;
        if (seleccion === 'completo') {
            return parseFloat(cursoActual.precio) || 0;
        }
        const nivel = (cursoActual.niveles || []).find(n => String(n.id) === String(seleccion));
        return nivel ? (parseFloat(nivel.precio) || 0) : 0;
    }

    function actualizarTotal() {
        const monto = obtenerPrecioSeleccionado();
        totalPagarEl.textContent = formatoMoneda(monto);

        if (monto <= 0) {
            grupoFormaPago.style.display = 'none';
            btnComprar.textContent = 'Inscribirme Gratis';
        } else {
            grupoFormaPago.style.display = 'block';
            btnComprar.textContent = 'Completar Inscripción';
        }
    }

    tipoCompraSelect.addEventListener('change', actualizarTotal);

    purchaseForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        purchaseFeedback.textContent = '';

        if (!window.CXRSE_USER || !window.CXRSE_USER.loggedIn) {
            window.location.href = 'index.php?url=login';
            return;
        }

        if (window.CXRSE_USER.rol !== 'Estudiante') {
            purchaseFeedback.style.color = '#dc2626';
            purchaseFeedback.textContent = 'Solo las cuentas con rol Estudiante pueden inscribirse a cursos.';
            return;
        }

        const monto = obtenerPrecioSeleccionado();
        const seleccion = tipoCompraSelect.value;
        const payload = {
            curso_id: parseInt(cursoActual.id, 10),
            nivel_id: seleccion === 'completo' ? null : parseInt(seleccion, 10),
            forma_pago: monto <= 0 ? 'Gratis' : formaPagoSelect.value,
            precio_pagado: monto
        };

        btnComprar.disabled = true;
        btnComprar.textContent = 'Procesando...';

        try {
            const response = await fetch('../app/controllers/CursoController.php?recurso=inscripciones', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify(payload)
            });

            const texto = await response.text();
            const data = texto ? JSON.parse(texto) : {};

            if (!response.ok) {
                throw new Error(data.error || 'No se pudo completar la inscripción.');
            }

            alert(data.mensaje || '¡Inscripción realizada con éxito!');
            window.location.href = 'index.php?url=kardex';
        } catch (error) {
            purchaseFeedback.style.color = '#dc2626';
            purchaseFeedback.textContent = error.message;
            actualizarTotal();
        } finally {
            btnComprar.disabled = false;
        }
    });

    inicializar();
});