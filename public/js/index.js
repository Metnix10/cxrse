document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const filterCategory = document.getElementById('filterCategory');
    const filterInstructor = document.getElementById('filterInstructor');
    const filterDateFrom = document.getElementById('filterDateFrom');
    const filterDateTo = document.getElementById('filterDateTo');
    const categoryList = document.getElementById('categoryList');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const coursesContainer = document.getElementById('coursesContainer');
    const noResults = document.getElementById('noResults');

    let cursosData = [];
    let criterioOrdenActual = 'all';

    function escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto ?? '';
        return div.innerHTML;
    }

    function generarEstrellas(rating) {
        const llenas = Math.round(Math.max(0, Math.min(5, rating)));
        return '★'.repeat(llenas) + '☆'.repeat(5 - llenas);
    }

    function formatearPrecio(precio) {
        const valor = parseFloat(precio) || 0;
        if (valor <= 0) {
            return '<span class="course-price font-free">Gratis</span>';
        }
        return `<span class="course-price">$${valor.toFixed(2)} MXN</span>`;
    }

    async function cargarCursos() {
        try {
            const response = await fetch('../app/controllers/CursoController.php?recurso=cursos', {
                method: 'GET',
                credentials: 'include'
            });

            const texto = await response.text();
            if (!texto) {
                throw new Error('El servidor devolvió una respuesta vacía.');
            }

            const data = JSON.parse(texto);
            if (!response.ok) {
                throw new Error(data.error || 'Error al obtener el catálogo de cursos.');
            }

            cursosData = Array.isArray(data) ? data : [];
            poblarFiltrosDinamicos(cursosData);
            renderizarCursos(cursosData);
        } catch (error) {
            console.error('Error cargando cursos:', error);
            if (coursesContainer) {
                coursesContainer.innerHTML = '';
            }
            if (noResults) {
                noResults.style.display = 'block';
                noResults.querySelector('p').textContent = 'No se pudieron cargar los cursos en este momento.';
            }
        }
    }

    function poblarFiltrosDinamicos(cursos) {
        const categoriasUnicas = [...new Set(
            cursos.map(c => c.categoria || c.categoria_nombre).filter(Boolean)
        )];
        const instructoresUnicos = [...new Set(
            cursos.map(c => c.instructor || c.instructor_nombre).filter(Boolean)
        )];

        if (categoriasUnicas.length > 0 && filterCategory) {
            filterCategory.innerHTML = '<option value="">Todas las categorías</option>' +
                categoriasUnicas.map(cat => `<option value="${escaparHTML(cat)}">${escaparHTML(cat)}</option>`).join('');
        }

        if (categoriasUnicas.length > 0 && categoryList) {
            categoryList.innerHTML = `
                <li class="category-item active" data-category="all">
                    <span>Todas las categorías</span>
                </li>
            ` + categoriasUnicas.map(cat => `
                <li class="category-item" data-category="${escaparHTML(cat)}">
                    <span>${escaparHTML(cat)}</span>
                </li>
            `).join('');
            enlazarEventosCategorias();
        }

        if (filterInstructor) {
            filterInstructor.innerHTML = '<option value="">Todos los instructores</option>' +
                instructoresUnicos.map(inst => `<option value="${escaparHTML(inst)}">${escaparHTML(inst)}</option>`).join('');
        }
    }

    function renderizarCursos(lista) {
        if (!coursesContainer) return;

        if (lista.length === 0) {
            coursesContainer.innerHTML = '';
            if (noResults) {
                noResults.style.display = 'block';
                noResults.querySelector('p').textContent = 'No se encontraron cursos activos con tu búsqueda.';
            }
            return;
        }

        if (noResults) {
            noResults.style.display = 'none';
        }

        coursesContainer.innerHTML = lista.map(curso => {
            const id = curso.id;
            const titulo = escaparHTML(curso.titulo);
            const categoria = escaparHTML(curso.categoria || curso.categoria_nombre || 'General');
            const instructor = escaparHTML(curso.instructor || curso.instructor_nombre || 'Instructor');
            const rating = parseFloat(curso.promedio_calificacion ?? curso.calificacion_promedio ?? 0);
            const alumnos = parseInt(curso.total_alumnos ?? curso.total_inscritos ?? 0, 10);
            const fechaRaw = (curso.fecha_creacion || curso.created_at || '').split(' ')[0];
            const imagen = (curso.imagen && curso.imagen.startsWith('http'))
                ? escaparHTML(curso.imagen)
                : `https://via.placeholder.com/300x180?text=${encodeURIComponent(curso.titulo || 'Curso')}`;

            let badgeHTML = '';
            if (alumnos >= 10) {
                badgeHTML = '<div class="card-badge badge-top">Más Vendido</div>';
            } else if (rating >= 4.5) {
                badgeHTML = '<div class="card-badge badge-rating">Mejor Calificado</div>';
            } else {
                badgeHTML = '<div class="card-badge badge-recent">Nuevo</div>';
            }

            return `
                <article class="course-card"
                    data-category="${categoria}"
                    data-rating="${rating}"
                    data-sales="${alumnos}"
                    data-date="${escaparHTML(fechaRaw)}">
                    ${badgeHTML}
                    <img src="${imagen}" alt="Portada ${titulo}" class="course-img">
                    <div class="course-body">
                        <span class="course-category">${categoria}</span>
                        <h3 class="course-title">${titulo}</h3>
                        <p class="course-instructor">Por: ${instructor}</p>
                        <div class="course-rating">
                            <span class="stars">${generarEstrellas(rating)}</span>
                            <span class="rating-score">${rating.toFixed(1)}</span>
                            <span class="rating-count">(${alumnos} ${alumnos === 1 ? 'alumno' : 'alumnos'})</span>
                        </div>
                        <div class="course-footer">
                            ${formatearPrecio(curso.precio)}
                            <a href="index.php?url=detalle-curso&id=${id}" class="btn-card">Ver Detalles</a>
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    }

    function aplicarFiltrosYOrden() {
        const query = (searchInput?.value || '').toLowerCase().trim();
        const categoriaSel = filterCategory?.value || '';
        const instructorSel = filterInstructor?.value || '';
        const fechaDesde = filterDateFrom?.value || '';
        const fechaHasta = filterDateTo?.value || '';

        let filtrados = cursosData.filter(curso => {
            const titulo = (curso.titulo || '').toLowerCase();
            const categoria = curso.categoria || curso.categoria_nombre || '';
            const instructor = curso.instructor || curso.instructor_nombre || '';
            const fechaCurso = (curso.fecha_creacion || curso.created_at || '').split(' ')[0];

            const coincideTitulo = query === '' || titulo.includes(query);
            const coincideCategoria = categoriaSel === '' || categoria === categoriaSel;
            const coincideInstructor = instructorSel === '' || instructor === instructorSel;
            const coincideDesde = fechaDesde === '' || (fechaCurso !== '' && fechaCurso >= fechaDesde);
            const coincideHasta = fechaHasta === '' || (fechaCurso !== '' && fechaCurso <= fechaHasta);

            return coincideTitulo && coincideCategoria && coincideInstructor && coincideDesde && coincideHasta;
        });

        filtrados.sort((a, b) => {
            if (criterioOrdenActual === 'best-rated') {
                return parseFloat(b.promedio_calificacion ?? b.calificacion_promedio ?? 0) - parseFloat(a.promedio_calificacion ?? a.calificacion_promedio ?? 0);
            }
            if (criterioOrdenActual === 'best-sellers') {
                return parseInt(b.total_alumnos ?? b.total_inscritos ?? 0, 10) - parseInt(a.total_alumnos ?? a.total_inscritos ?? 0, 10);
            }
            if (criterioOrdenActual === 'recent') {
                return new Date(b.fecha_creacion || b.created_at || 0) - new Date(a.fecha_creacion || a.created_at || 0);
            }
            return 0;
        });

        renderizarCursos(filtrados);
    }

    function enlazarEventosCategorias() {
        const items = document.querySelectorAll('.category-item');
        items.forEach(item => {
            item.addEventListener('click', function () {
                items.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                const selectedCat = this.getAttribute('data-category');
                if (filterCategory) {
                    filterCategory.value = selectedCat === 'all' ? '' : selectedCat;
                }
                aplicarFiltrosYOrden();
            });
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            aplicarFiltrosYOrden();
        });
    }

    searchInput?.addEventListener('input', aplicarFiltrosYOrden);
    filterCategory?.addEventListener('change', aplicarFiltrosYOrden);
    filterInstructor?.addEventListener('change', aplicarFiltrosYOrden);
    filterDateFrom?.addEventListener('change', aplicarFiltrosYOrden);
    filterDateTo?.addEventListener('change', aplicarFiltrosYOrden);

    enlazarEventosCategorias();

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            criterioOrdenActual = this.getAttribute('data-tab') || 'all';
            aplicarFiltrosYOrden();
        });
    });

    cargarCursos();
});