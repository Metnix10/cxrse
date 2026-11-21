document.addEventListener('DOMContentLoaded', function () {
    const userControlTable = document.getElementById('userControlTable');
    const reportTypeSelect = document.getElementById('reportType');
    const tableHeader = document.getElementById('reportTableHeader');
    const tableBody = document.getElementById('reportTableBody');
    const commentsModerationBody = document.getElementById('commentsModerationBody');

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    function formatoMoneda(valor) {
        const num = parseFloat(valor) || 0;
        return `$${num.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MXN`;
    }

    async function cargarUsuarios() {
        try {
            const res = await fetch('../app/controllers/AdminController.php?recurso=usuarios', { credentials: 'include' });
            const usuarios = await res.json();
            if (!res.ok) throw new Error(usuarios.error || 'Error al cargar usuarios.');

            userControlTable.innerHTML = usuarios.map(u => {
                const esActivo = u.estatus === 'Activo';
                const esAdmin = u.rol === 'Admin';
                const intentos = parseInt(u.intentos_fallidos, 10) || 0;

                let roleClass = 'role-student';
                if (u.rol === 'Instructor') roleClass = 'role-instructor';
                if (u.rol === 'Admin') roleClass = 'role-admin';

                const accionHTML = esAdmin
                    ? '<span style="color:#6b7280; font-size:12px;">Cuenta Administrador</span>'
                    : `<button type="button" class="btn-action ${esActivo ? 'btn-block' : 'btn-unblock'}"
                           data-id="${u.id}" data-next="${esActivo ? 'Deshabilitado' : 'Activo'}">
                           ${esActivo ? 'Bloquear' : 'Desbloquear'}
                       </button>`;

                return `
                    <tr class="${esActivo ? '' : 'blocked-row'}">
                        <td>${escaparHTML(u.email)}</td>
                        <td><strong>${escaparHTML(u.nombre)}</strong></td>
                        <td><span class="role-badge ${roleClass}">${escaparHTML(u.rol)}</span></td>
                        <td class="${intentos >= 3 ? 'failed-attempts' : ''}">${intentos >= 3 ? intentos + ' (Límite superado)' : intentos}</td>
                        <td><span class="status-badge ${esActivo ? 'status-active' : 'status-blocked'}">${escaparHTML(u.estatus)}</span></td>
                        <td>${accionHTML}</td>
                    </tr>
                `;
            }).join('');

            document.querySelectorAll('#userControlTable button[data-id]').forEach(btn => {
                btn.addEventListener('click', () => cambiarEstatusUsuario(
                    btn.getAttribute('data-id'),
                    btn.getAttribute('data-next')
                ));
            });
        } catch (error) {
            userControlTable.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#dc2626;">${escaparHTML(error.message)}</td></tr>`;
        }
    }

    async function cambiarEstatusUsuario(id, nuevoEstatus) {
        try {
            const res = await fetch('../app/controllers/AdminController.php?recurso=usuario-estatus', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id: parseInt(id, 10), estatus: nuevoEstatus })
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo actualizar el estatus.');

            cargarUsuarios();
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    async function cargarReporte(tipo) {
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Cargando reporte...</td></tr>';

        try {
            const res = await fetch(`../app/controllers/AdminController.php?recurso=reporte-usuarios&tipo=${tipo}`, { credentials: 'include' });
            const datos = await res.json();
            if (!res.ok) throw new Error(datos.error || 'Error al cargar reporte.');

            if (tipo === 'instructor') {
                tableHeader.innerHTML = `
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Fecha Ingreso</th>
                        <th>Cursos Ofrecidos</th>
                        <th>Total Ganancias</th>
                    </tr>
                `;
                tableBody.innerHTML = datos.length === 0
                    ? '<tr><td colspan="5" style="text-align:center;">No hay instructores registrados.</td></tr>'
                    : datos.map(item => `
                        <tr>
                            <td>${escaparHTML(item.email)}</td>
                            <td><strong>${escaparHTML(item.nombre)}</strong></td>
                            <td>${escaparHTML(item.fecha_ingreso)}</td>
                            <td>${parseInt(item.cursos_ofrecidos, 10) || 0}</td>
                            <td class="amount">${formatoMoneda(item.total_ganancias)}</td>
                        </tr>
                    `).join('');
            } else {
                tableHeader.innerHTML = `
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Fecha Ingreso</th>
                        <th>Cursos Inscritos</th>
                        <th>% Cursos Terminados</th>
                    </tr>
                `;
                tableBody.innerHTML = datos.length === 0
                    ? '<tr><td colspan="5" style="text-align:center;">No hay estudiantes registrados.</td></tr>'
                    : datos.map(item => `
                        <tr>
                            <td>${escaparHTML(item.email)}</td>
                            <td><strong>${escaparHTML(item.nombre)}</strong></td>
                            <td>${escaparHTML(item.fecha_ingreso)}</td>
                            <td>${parseInt(item.cursos_inscritos, 10) || 0}</td>
                            <td><strong>${parseFloat(item.porcentaje_terminados || 0).toFixed(1)}%</strong></td>
                        </tr>
                    `).join('');
            }
        } catch (error) {
            tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:#dc2626;">${escaparHTML(error.message)}</td></tr>`;
        }
    }

    async function cargarComentariosAdmin() {
        if (!commentsModerationBody) return;
        try {
            const res = await fetch('../app/controllers/AdminController.php?recurso=comentarios', { credentials: 'include' });
            const lista = await res.json();
            if (!res.ok) throw new Error(lista.error || 'Error al cargar comentarios.');

            if (!Array.isArray(lista) || lista.length === 0) {
                commentsModerationBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hay comentarios publicados aún.</td></tr>';
                return;
            }

            commentsModerationBody.innerHTML = lista.map(com => {
                const eliminado = parseInt(com.eliminado, 10) === 1;
                const estrellas = '★'.repeat(com.calificacion) + '☆'.repeat(5 - com.calificacion);

                return `
                    <tr class="${eliminado ? 'blocked-row' : ''}">
                        <td><strong>${escaparHTML(com.curso_titulo)}</strong></td>
                        <td>${escaparHTML(com.alumno_nombre)}<br><small style="color:#6b7280;">${escaparHTML(com.fecha_envio)}</small></td>
                        <td style="color:#f59e0b;">${estrellas}</td>
                        <td>
                            ${escaparHTML(com.comentario)}
                            ${eliminado ? `<div style="font-size:12px; color:#dc2626; margin-top:4px;">Causa: ${escaparHTML(com.causa_eliminacion || '')}</div>` : ''}
                        </td>
                        <td><span class="status-badge ${eliminado ? 'status-blocked' : 'status-active'}">${eliminado ? 'Eliminado' : 'Visible'}</span></td>
                        <td>
                            ${eliminado
                                ? '<span style="color:#6b7280; font-size:12px;">Dado de baja</span>'
                                : `<button type="button" class="btn-action btn-block btn-del-comment" data-id="${com.id}">Eliminar</button>`
                            }
                        </td>
                    </tr>
                `;
            }).join('');

            document.querySelectorAll('.btn-del-comment').forEach(btn => {
                btn.addEventListener('click', () => eliminarComentario(btn.getAttribute('data-id')));
            });
        } catch (error) {
            commentsModerationBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#dc2626;">${escaparHTML(error.message)}</td></tr>`;
        }
    }

    async function eliminarComentario(id) {
        const causa = prompt('Escribe la causa de eliminación de este comentario:');
        if (causa === null) return;
        if (!causa.trim()) {
            alert('Debes especificar una causa para dar de baja el comentario.');
            return;
        }

        try {
            const res = await fetch('../app/controllers/AdminController.php?recurso=comentarios', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ id: parseInt(id, 10), causa: causa.trim() })
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'No se pudo eliminar el comentario.');

            alert(data.mensaje);
            cargarComentariosAdmin();
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    reportTypeSelect.addEventListener('change', function () {
        cargarReporte(this.value);
    });

    cargarUsuarios();
    cargarReporte('instructor');
    cargarComentariosAdmin();
});