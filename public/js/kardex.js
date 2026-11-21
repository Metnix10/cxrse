document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('kardexFilterForm');
    const tableBody = document.getElementById('kardexTableBody');
    const noResultsMsg = document.getElementById('noKardexResults');
    const filterCategory = document.getElementById('filterCategory');

    let kardexData = [];

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    async function cargarKardex() {
        try {
            const response = await fetch('../app/controllers/CursoController.php?recurso=kardex', {
                method: 'GET',
                credentials: 'include'
            });

            const texto = await response.text();
            const data = texto ? JSON.parse(texto) : [];

            if (!response.ok) {
                throw new Error(data.error || 'Error al cargar el Kardex.');
            }

            kardexData = Array.isArray(data) ? data : [];
            poblarCategorias(kardexData);
            aplicarFiltros();
        } catch (error) {
            console.error(error);
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#dc2626;">${escaparHTML(error.message)}</td></tr>`;
        }
    }

    function poblarCategorias(lista) {
        const categorias = [...new Set(lista.map(item => item.categoria_nombre).filter(Boolean))];
        filterCategory.innerHTML = '<option value="all">Todas las categorías</option>' +
            categorias.map(cat => `<option value="${escaparHTML(cat)}">${escaparHTML(cat)}</option>`).join('');
    }

    function renderizarTabla(lista) {
        if (lista.length === 0) {
            tableBody.innerHTML = '';
            noResultsMsg.style.display = 'block';
            return;
        }

        noResultsMsg.style.display = 'none';

        tableBody.innerHTML = lista.map(item => {
            const avance = parseInt(item.avance_porcentaje, 10) || 0;
            const nivelActual = parseInt(item.nivel_actual, 10) || 1;
            const totalNiveles = parseInt(item.total_niveles, 10) || 1;
            const terminado = item.estatus_curso === 'Terminado' || avance >= 100;
            const cursoActivo = item.curso_estatus === 'Activo';

            const subtituloNivel = item.nivel_id
                ? `<span class="instructor-tag" style="color:#2563eb;">Nivel individual: ${item.nivel_comprado_numero}. ${escaparHTML(item.nivel_comprado_titulo)}</span>`
                : '';

            let estatusBadge = '';
            if (!cursoActivo) {
                estatusBadge = '<span class="status-badge status-inactive">Inactivo</span>';
            } else if (terminado) {
                estatusBadge = '<span class="status-badge status-complete">Terminado</span>';
            } else {
                estatusBadge = '<span class="status-badge status-progress">En Progreso</span>';
            }

            let accionBtn = '';
            if (!cursoActivo) {
                accionBtn = '<button class="btn-action btn-disabled" disabled>No Disponible</button>';
            } else if (terminado) {
                accionBtn = `
                    <a href="index.php?url=diploma&inscripcion_id=${item.inscripcion_id}" class="btn-action btn-diploma">Ver Diploma</a>
                    <a href="index.php?url=reproductor&curso_id=${item.curso_id}" class="btn-action btn-continue" style="margin-top:4px; display:inline-block;">Repasar</a>
                `;
            } else {
                accionBtn = `<a href="index.php?url=reproductor&curso_id=${item.curso_id}" class="btn-action btn-continue">Continuar</a>`;
            }

            return `
                <tr>
                    <td class="course-info-cell">
                        <strong>${escaparHTML(item.curso_titulo)}</strong>
                        <span class="instructor-tag">Instructor: ${escaparHTML(item.instructor_nombre)}</span>
                        ${subtituloNivel}
                    </td>
                    <td><span class="category-badge">${escaparHTML(item.categoria_nombre)}</span></td>
                    <td>${escaparHTML(item.fecha_inscripcion)}</td>
                    <td>${escaparHTML(item.ultimo_ingreso || item.fecha_inscripcion)}</td>
                    <td>
                        <div class="progress-bar-container">
                            <div class="progress-bar ${terminado ? 'complete' : ''}" style="width: ${avance}%;"></div>
                        </div>
                        <span class="progress-text">${avance}% (Nivel ${nivelActual} de ${totalNiveles})</span>
                    </td>
                    <td>${estatusBadge}</td>
                    <td>${accionBtn}</td>
                </tr>
            `;
        }).join('');
    }

    function aplicarFiltros() {
        const startDateVal = document.getElementById('startDate').value;
        const endDateVal = document.getElementById('endDate').value;
        const categoryVal = filterCategory.value;
        const statusVal = document.getElementById('filterStatus').value;
        const activeVal = document.getElementById('filterActive').value;

        const filtrados = kardexData.filter(item => {
            const terminado = item.estatus_curso === 'Terminado' || parseInt(item.avance_porcentaje, 10) >= 100;
            const statusStr = terminado ? 'completed' : 'incomplete';
            const activeStr = item.curso_estatus === 'Activo' ? 'active' : 'inactive';

            const matchesCategory = (categoryVal === 'all' || item.categoria_nombre === categoryVal);
            const matchesStatus = (statusVal === 'all' || statusStr === statusVal);
            const matchesActive = (activeVal === 'all' || activeStr === activeVal);

            let matchesDate = true;
            if (startDateVal) matchesDate = matchesDate && (item.fecha_inscripcion_raw >= startDateVal);
            if (endDateVal) matchesDate = matchesDate && (item.fecha_inscripcion_raw <= endDateVal);

            return matchesCategory && matchesStatus && matchesActive && matchesDate;
        });

        renderizarTabla(filtrados);
    }

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        aplicarFiltros();
    });

    cargarKardex();
});