document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('instructorFilterForm');
    const summaryTableBody = document.getElementById('summaryTableBody');
    const detailTableBody = document.getElementById('detailTableBody');
    const selectedCourseName = document.getElementById('selectedCourseName');
    const selectedCourseTotal = document.getElementById('selectedCourseTotal');
    const filterCategory = document.getElementById('filterCategory');

    let cursosInstructor = [];

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    function formatoMoneda(valor) {
        const num = parseFloat(valor) || 0;
        return `$${num.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
    }

    async function cargarReporteInstructor() {
        try {
            const response = await fetch('../app/controllers/CursoController.php?recurso=reporte-instructor', {
                method: 'GET',
                credentials: 'include'
            });

            const texto = await response.text();
            if (!texto) throw new Error('Respuesta vacía del servidor.');

            const data = JSON.parse(texto);
            if (!response.ok) throw new Error(data.error || 'Error al cargar el reporte.');

            cursosInstructor = Array.isArray(data) ? data : [];
            poblarCategorias(cursosInstructor);
            aplicarFiltros();
        } catch (error) {
            console.error(error);
            summaryTableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#dc2626;">${escaparHTML(error.message)}</td></tr>`;
        }
    }

    function poblarCategorias(cursos) {
        const categorias = [...new Set(cursos.map(c => c.categoria_nombre).filter(Boolean))];
        filterCategory.innerHTML = '<option value="all">Todas las categorías</option>' +
            categorias.map(cat => `<option value="${escaparHTML(cat)}">${escaparHTML(cat)}</option>`).join('');
    }

    function renderizarResumen(lista) {
        if (lista.length === 0) {
            summaryTableBody.innerHTML = '<tr><td colspan="7" style="text-align:center;">No hay cursos registrados con estos criterios.</td></tr>';
            actualizarDesglosePagos([]);
            return;
        }

        summaryTableBody.innerHTML = lista.map(curso => {
            const esActivo = curso.estatus === 'Activo';
            const badgeEstatus = esActivo
                ? '<span class="badge badge-active">Activo</span>'
                : '<span class="badge badge-inactive">Dado de baja</span>';

            const botonBaja = esActivo
                ? `<button type="button" class="btn-baja-curso" data-id="${curso.id}" style="background:#fee2e2; color:#dc2626; border:1px solid #fecaca; padding:6px 10px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; margin-left:6px;">Dar de baja</button>`
                : '';

            return `
                <tr>
                    <td><strong>${escaparHTML(curso.titulo)}</strong></td>
                    <td><span class="badge">${escaparHTML(curso.categoria_nombre)}</span></td>
                    <td>${badgeEstatus}</td>
                    <td>${parseInt(curso.total_alumnos, 10) || 0}</td>
                    <td>Nivel ${parseFloat(curso.nivel_promedio || 0).toFixed(1)}</td>
                    <td class="amount font-bold">${formatoMoneda(curso.total_ingresos)}</td>
                    <td>
                        <button type="button" class="btn-view-detail" data-course="${curso.id}">Ver Alumnos</button>
                        ${botonBaja}
                    </td>
                </tr>
            `;
        }).join('');

        document.querySelectorAll('.btn-view-detail').forEach(btn => {
            btn.addEventListener('click', function () {
                const courseId = this.getAttribute('data-course');
                const curso = cursosInstructor.find(c => String(c.id) === String(courseId));
                if (curso) mostrarDetalleCurso(curso);
            });
        });

        document.querySelectorAll('.btn-baja-curso').forEach(btn => {
            btn.addEventListener('click', function () {
                const courseId = this.getAttribute('data-id');
                darDeBajaCurso(courseId);
            });
        });

        actualizarDesglosePagos(lista);

        mostrarDetalleCurso(lista[0]);
    }

    function actualizarDesglosePagos(listaCursos) {
        let paypal = 0;
        let efectivo = 0;

        listaCursos.forEach(curso => {
            (curso.alumnos || []).forEach(al => {
                const monto = parseFloat(al.precio_pagado) || 0;
                const metodo = (al.forma_pago || '').toLowerCase();

                if (metodo.includes('paypal')) {
                    paypal += monto;
                } else if (metodo.includes('efectivo')) {
                    efectivo += monto;
                }
            });
        });

        const totalGeneral = paypal + efectivo;

        document.getElementById('totalPaypal').textContent = formatoMoneda(paypal);
        document.getElementById('totalEfectivo').textContent = formatoMoneda(efectivo);
        document.getElementById('totalAcumulado').textContent = formatoMoneda(totalGeneral);
    }

    function mostrarDetalleCurso(curso) {
        selectedCourseName.textContent = curso.titulo;
        selectedCourseTotal.textContent = formatoMoneda(curso.total_ingresos);

        const alumnos = curso.alumnos || [];
        const totalNiveles = parseInt(curso.total_niveles, 10) || 1;

        if (alumnos.length === 0) {
            detailTableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Aún no hay alumnos inscritos en este curso.</td></tr>';
            return;
        }

        detailTableBody.innerHTML = alumnos.map(al => {
            const avance = parseInt(al.avance_porcentaje, 10) || 0;
            const nivelActual = parseInt(al.nivel_actual, 10) || 1;
            return `
                <tr>
                    <td>${escaparHTML(al.alumno_nombre)}</td>
                    <td>${escaparHTML(al.fecha_inscripcion)}</td>
                    <td>Nivel ${nivelActual} de ${totalNiveles} (${avance}%)</td>
                    <td class="amount">${formatoMoneda(al.precio_pagado)}</td>
                    <td>${escaparHTML(al.forma_pago)}</td>
                </tr>
            `;
        }).join('');
    }

    async function darDeBajaCurso(cursoId) {
        if (!confirm('¿Seguro que deseas dar de baja este curso? Dejará de mostrarse en el catálogo público, pero los alumnos inscritos conservarán su historial.')) {
            return;
        }

        try {
            const response = await fetch(`../app/controllers/CursoController.php?recurso=cursos&id=${cursoId}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id: cursoId })
            });

            const texto = await response.text();
            const data = texto ? JSON.parse(texto) : {};

            if (!response.ok) {
                throw new Error(data.error || 'No se pudo dar de baja el curso.');
            }

            alert('Curso dado de baja correctamente.');
            cargarReporteInstructor();
        } catch (error) {
            console.error(error);
            alert('Error: ' + error.message);
        }
    }

    function aplicarFiltros() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const category = filterCategory.value;
        const status = document.getElementById('filterStatus').value;

        const filtrados = cursosInstructor.filter(curso => {
            const cursoStatus = curso.estatus === 'Activo' ? 'active' : 'inactive';
            const matchCat = (category === 'all' || curso.categoria_nombre === category);
            const matchStatus = (status === 'all' || cursoStatus === status);
            let matchDate = true;

            if (startDate) matchDate = matchDate && (curso.fecha_creacion >= startDate);
            if (endDate) matchDate = matchDate && (curso.fecha_creacion <= endDate);

            return matchCat && matchStatus && matchDate;
        });

        renderizarResumen(filtrados);
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            aplicarFiltros();
        });
    }

    cargarReporteInstructor();

    const threadsList = document.getElementById('instructorThreadsList');
    const chatHistory = document.getElementById('instructorChatHistory');
    const replyForm = document.getElementById('instructorReplyForm');
    const replyInput = document.getElementById('instructorReplyInput');
    let chatActivo = null;

    async function cargarBandejaInstructor() {
        if (!threadsList) return;
        const res = await fetch('../app/controllers/InteraccionController.php?recurso=mensajes&bandeja=1', { credentials: 'include' });
        const hilos = await res.json();

        if (!Array.isArray(hilos) || hilos.length === 0) {
            threadsList.innerHTML = '<p style="font-size:13px; color:#6b7280;">No tienes mensajes de alumnos todavía.</p>';
            return;
        }

        threadsList.innerHTML = hilos.map(h => `
            <div class="thread-item" data-curso="${h.curso_id}" data-alumno="${h.alumno_id}" style="padding:10px; border-bottom:1px solid #e5e7eb; cursor:pointer; background:#fff; border-radius:6px; margin-bottom:6px;">
                <strong style="display:block; font-size:14px;">${escaparHTML(h.alumno_nombre)}</strong>
                <span style="display:block; font-size:12px; color:#2563eb;">${escaparHTML(h.curso_titulo)}</span>
                <small style="color:#6b7280;">${escaparHTML(h.ultimo_mensaje || '')}</small>
            </div>
        `).join('');

        document.querySelectorAll('.thread-item').forEach(item => {
            item.addEventListener('click', () => {
                chatActivo = {
                    cursoId: item.getAttribute('data-curso'),
                    alumnoId: item.getAttribute('data-alumno')
                };
                abrirConversacionInstructor();
            });
        });
    }

    async function abrirConversacionInstructor() {
        if (!chatActivo) return;
        const res = await fetch(`../app/controllers/InteraccionController.php?recurso=mensajes&curso_id=${chatActivo.cursoId}&contacto_id=${chatActivo.alumnoId}`, {
            credentials: 'include'
        });
        const msgs = await res.json();
        replyForm.style.display = 'flex';

        chatHistory.innerHTML = msgs.map(m => `
            <div style="padding:8px 10px; border-radius:6px; background:${m.remitente_rol === 'Instructor' ? '#dbeafe' : '#f3f4f6'};">
                <strong style="font-size:12px;">${escaparHTML(m.remitente_nombre)}</strong> <small style="color:#6b7280;">${escaparHTML(m.fecha)}</small>
                <p style="font-size:13px; margin-top:2px;">${escaparHTML(m.mensaje)}</p>
            </div>
        `).join('');
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    replyForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!chatActivo || !replyInput.value.trim()) return;

        const res = await fetch('../app/controllers/InteraccionController.php?recurso=mensajes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({
                curso_id: parseInt(chatActivo.cursoId, 10),
                destinatario_id: parseInt(chatActivo.alumnoId, 10),
                mensaje: replyInput.value.trim()
            })
        });
        if (res.ok) {
            replyInput.value = '';
            abrirConversacionInstructor();
            cargarBandejaInstructor();
        }
    });

    cargarBandejaInstructor();
});