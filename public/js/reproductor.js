document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const cursoId = params.get('curso_id');
    const nivelInicialParam = parseInt(params.get('nivel') || '0', 10);

    if (!cursoId) {
        window.location.href = 'index.php?url=kardex';
        return;
    }

    const courseVideo = document.getElementById('courseVideo');
    const lessonBadge = document.getElementById('lessonBadge');
    const lessonTitle = document.getElementById('lessonTitle');
    const lessonDescription = document.getElementById('lessonDescription');
    const resourcesCard = document.getElementById('resourcesCard');
    const resourcesLinks = document.getElementById('resourcesLinks');
    const levelsList = document.getElementById('levelsList');
    const progressLabel = document.getElementById('progressLabel');
    const progressBarFill = document.getElementById('progressBarFill');
    const btnCompleteLevel = document.getElementById('btnCompleteLevel');
    const btnDiplomaPlayer = document.getElementById('btnDiplomaPlayer');

    let reproductorData = null;
    let nivelSeleccionado = null;

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    async function cargarReproductor() {
        try {
            const response = await fetch(`../app/controllers/CursoController.php?recurso=reproductor&curso_id=${cursoId}`, {
                method: 'GET',
                credentials: 'include'
            });

            const texto = await response.text();
            const data = texto ? JSON.parse(texto) : {};

            if (!response.ok) {
                throw new Error(data.error || 'No tienes acceso a este curso.');
            }

            reproductorData = data;
            document.getElementById('courseSidebarTitle').textContent = data.curso.titulo;

            actualizarBarraProgreso(data.inscripcion);
            renderizarListaNiveles(data.niveles);

            const niveles = data.niveles || [];
            let inicial = niveles.find(n => n.desbloqueado && n.numero_nivel === nivelInicialParam)
                || niveles.find(n => n.desbloqueado && parseInt(n.numero_nivel, 10) === parseInt(data.inscripcion.nivel_actual, 10))
                || niveles.find(n => n.desbloqueado);

            if (inicial) {
                seleccionarNivel(inicial);
            }
        } catch (error) {
            alert(error.message);
            window.location.href = 'index.php?url=kardex';
        }
    }

    function actualizarBarraProgreso(inscripcion) {
        const avance = parseInt(inscripcion.avance_porcentaje, 10) || 0;
        progressLabel.textContent = `Tu avance general: ${avance}%`;
        progressBarFill.style.width = `${avance}%`;

        if (avance >= 100 || inscripcion.estatus_curso === 'Terminado') {
            btnDiplomaPlayer.style.display = 'block';
            btnDiplomaPlayer.href = `index.php?url=diploma&inscripcion_id=${inscripcion.id}`;
        }
    }

    function renderizarListaNiveles(niveles) {
        levelsList.innerHTML = niveles.map(niv => {
            const activo = nivelSeleccionado && String(nivelSeleccionado.id) === String(niv.id);
            const icono = niv.desbloqueado ? (activo ? '▶' : '✓') : '🔒';
            const claseItem = `level-item ${activo ? 'active' : ''}`;

            return `
                <div class="${claseItem}" data-nivel-id="${niv.id}" style="cursor:${niv.desbloqueado ? 'pointer' : 'not-allowed'}; opacity:${niv.desbloqueado ? '1' : '0.6'};">
                    <div>
                        <strong>Nivel ${niv.numero_nivel}: ${escaparHTML(niv.titulo)}</strong>
                        <span class="level-duration">${niv.desbloqueado ? 'Disponible' : 'Nivel no adquirido'}</span>
                    </div>
                    <span class="${niv.desbloqueado ? 'active' : 'locked'}">${icono}</span>
                </div>
            `;
        }).join('');

        document.querySelectorAll('.level-item[data-nivel-id]').forEach(el => {
            el.addEventListener('click', () => {
                const id = el.getAttribute('data-nivel-id');
                const niv = reproductorData.niveles.find(n => String(n.id) === String(id));
                if (!niv) return;

                if (!niv.desbloqueado) {
                    alert('Este nivel no está incluido en tu compra. Puedes adquirirlo desde el detalle del curso.');
                    return;
                }
                seleccionarNivel(niv);
            });
        });
    }

    function seleccionarNivel(nivel) {
        nivelSeleccionado = nivel;

        lessonBadge.textContent = `Nivel ${nivel.numero_nivel}`;
        lessonTitle.textContent = nivel.titulo;
        lessonDescription.textContent = nivel.descripcion || '';

        if (reproductorData.curso.imagen && reproductorData.curso.imagen.startsWith('http')) {
            courseVideo.poster = reproductorData.curso.imagen;
        }
        courseVideo.src = nivel.video_url;
        courseVideo.load();

        let linksHTML = '';
        if (nivel.pdf_url) {
            linksHTML += `<a href="${escaparHTML(nivel.pdf_url)}" target="_blank" rel="noopener" class="btn-download">Abrir / Descargar PDF</a>`;
        }
        if (nivel.link_externo) {
            linksHTML += `<a href="${escaparHTML(nivel.link_externo)}" target="_blank" rel="noopener" class="btn-download" style="background-color:#4b5563;">Abrir Enlace Externo</a>`;
        }

        if (linksHTML) {
            resourcesCard.style.display = 'block';
            resourcesLinks.innerHTML = linksHTML;
        } else {
            resourcesCard.style.display = 'none';
        }

        renderizarListaNiveles(reproductorData.niveles);
    }

    async function marcarNivelCompletado() {
        if (!reproductorData || !nivelSeleccionado) return;

        btnCompleteLevel.disabled = true;
        btnCompleteLevel.textContent = 'Guardando avance...';

        try {
            const response = await fetch('../app/controllers/CursoController.php?recurso=avance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({
                    inscripcion_id: parseInt(reproductorData.inscripcion.id, 10),
                    numero_nivel: parseInt(nivelSeleccionado.numero_nivel, 10),
                    curso_id: parseInt(reproductorData.curso.id, 10)
                })
            });

            const texto = await response.text();
            const data = texto ? JSON.parse(texto) : {};

            if (!response.ok) {
                throw new Error(data.error || 'No se pudo actualizar el avance.');
            }

            alert(data.mensaje);
            await cargarReproductor();

            const siguiente = reproductorData.niveles.find(
                n => n.desbloqueado && parseInt(n.numero_nivel, 10) === parseInt(nivelSeleccionado.numero_nivel, 10) + 1
            );
            if (siguiente) {
                seleccionarNivel(siguiente);
            }
        } catch (error) {
            alert('Error: ' + error.message);
        } finally {
            btnCompleteLevel.disabled = false;
            btnCompleteLevel.textContent = 'Marcar nivel como completado';
        }
    }

    btnCompleteLevel.addEventListener('click', marcarNivelCompletado);
    courseVideo.addEventListener('ended', marcarNivelCompletado);

    cargarReproductor();

    const tabChatPrivado = document.getElementById('tabChatPrivado');
    const tabResenasCurso = document.getElementById('tabResenasCurso');
    const panelChatPrivado = document.getElementById('panelChatPrivado');
    const panelResenasCurso = document.getElementById('panelResenasCurso');
    const chatMessagesList = document.getElementById('chatMessagesList');
    const privateMessageForm = document.getElementById('privateMessageForm');
    const commentForm = document.getElementById('commentForm');
    const commentsList = document.getElementById('commentsList');

    tabChatPrivado?.addEventListener('click', () => {
        panelChatPrivado.style.display = 'block';
        panelResenasCurso.style.display = 'none';
        tabChatPrivado.style.backgroundColor = '#2563eb';
        tabResenasCurso.style.backgroundColor = '#6b7280';
    });

    tabResenasCurso?.addEventListener('click', () => {
        panelChatPrivado.style.display = 'none';
        panelResenasCurso.style.display = 'block';
        tabResenasCurso.style.backgroundColor = '#2563eb';
        tabChatPrivado.style.backgroundColor = '#6b7280';
        cargarResenas();
    });

    async function cargarChatPrivado() {
        if (!reproductorData) return;
        const instructorId = reproductorData.curso.instructor_id;
        try {
            const res = await fetch(`../app/controllers/InteraccionController.php?recurso=mensajes&curso_id=${cursoId}&contacto_id=${instructorId}`, {
                credentials: 'include'
            });
            const mensajes = await res.json();
            if (!Array.isArray(mensajes) || mensajes.length === 0) {
                chatMessagesList.innerHTML = '<p style="color:#6b7280; font-size:13px;">Aún no has enviado mensajes al instructor en este curso.</p>';
                return;
            }
            chatMessagesList.innerHTML = mensajes.map(m => {
                const esInstructor = m.remitente_rol === 'Instructor';
                const avatar = (m.remitente_foto && m.remitente_foto.startsWith('http'))
                    ? escaparHTML(m.remitente_foto)
                    : `https://ui-avatars.com/api/?name=${encodeURIComponent(m.remitente_nombre)}&background=2563eb&color=fff&size=64`;
                return `
                    <div style="display:flex; gap:10px; align-items:flex-start; padding:10px 12px; border-radius:8px; background:${esInstructor ? '#dbeafe' : '#ffffff'}; border:1px solid #e5e7eb;">
                        <img src="${avatar}" alt="Avatar" style="width:36px; height:36px; border-radius:50%; object-fit:cover; flex-shrink:0;">
                        <div>
                            <strong style="font-size:13px;">${escaparHTML(m.remitente_nombre)} (${escaparHTML(m.remitente_rol)})</strong>
                            <small style="color:#6b7280; margin-left:6px;">${escaparHTML(m.fecha)}</small>
                            <p style="margin-top:4px; font-size:14px;">${escaparHTML(m.mensaje)}</p>
                        </div>
                    </div>
                `;
            }).join('');
            chatMessagesList.scrollTop = chatMessagesList.scrollHeight;
        } catch (err) {
            console.error(err);
        }
    }

    privateMessageForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('privateMessageText');
        const texto = input.value.trim();
        if (!texto || !reproductorData) return;

        const res = await fetch('../app/controllers/InteraccionController.php?recurso=mensajes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({
                curso_id: parseInt(cursoId, 10),
                destinatario_id: parseInt(reproductorData.curso.instructor_id, 10),
                mensaje: texto
            })
        });
        if (res.ok) {
            input.value = '';
            cargarChatPrivado();
        }
    });

    async function cargarResenas() {
        const res = await fetch(`../app/controllers/InteraccionController.php?recurso=comentarios&curso_id=${cursoId}`);
        const comentarios = await res.json();
        if (!Array.isArray(comentarios) || comentarios.length === 0) {
            commentsList.innerHTML = '<p style="color:#6b7280; font-size:14px;">Sé el primero en calificar este curso.</p>';
            return;
        }
        commentsList.innerHTML = comentarios.map(c => {
            const eliminado = parseInt(c.eliminado, 10) === 1;
            if (eliminado) {
                return `
                    <div class="comment-item" style="background:#f9fafb; border-left:3px solid #9ca3af; padding:10px;">
                        <p style="color:#6b7280; font-style:italic; margin:0;">${escaparHTML(c.comentario)}</p>
                    </div>
                `;
            }
            const stars = '★'.repeat(c.calificacion) + '☆'.repeat(5 - c.calificacion);
            return `
                <div class="comment-item">
                    <strong>${escaparHTML(c.usuario_nombre)} <span style="color:#f59e0b;">${stars}</span> <small>- ${escaparHTML(c.fecha)}</small></strong>
                    <p>${escaparHTML(c.comentario)}</p>
                </div>
            `;
        }).join('');
    }

    commentForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const textEl = document.getElementById('commentText');
        const ratingEl = document.getElementById('ratingSelect');
        if (!textEl.value.trim()) return;

        const res = await fetch('../app/controllers/InteraccionController.php?recurso=comentarios', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({
                curso_id: parseInt(cursoId, 10),
                calificacion: parseInt(ratingEl.value, 10),
                comentario: textEl.value.trim()
            })
        });
        const data = await res.json();
        if (res.ok) {
            textEl.value = '';
            cargarResenas();
        } else {
            alert(data.error || 'No se pudo publicar la reseña.');
        }
    });

    setTimeout(cargarChatPrivado, 500);
});