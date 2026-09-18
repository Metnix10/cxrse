document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const filterCategory = document.getElementById('filterCategory');
    const filterInstructor = document.getElementById('filterInstructor');
    const categoryItems = document.querySelectorAll('.category-item');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const courseCards = document.querySelectorAll('.course-card');
    const noResults = document.getElementById('noResults');

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        aplicarFiltros();
    });

    searchInput.addEventListener('input', aplicarFiltros);
    filterCategory.addEventListener('change', aplicarFiltros);
    filterInstructor.addEventListener('change', aplicarFiltros);

    categoryItems.forEach(item => {
        item.addEventListener('click', function () {
            categoryItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            const selectedCat = this.getAttribute('data-category');
            filterCategory.value = selectedCat === 'all' ? '' : selectedCat;
            aplicarFiltros();
        });
    });

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const tab = this.getAttribute('data-tab');
            ordenarCursos(tab);
        });
    });

    function aplicarFiltros() {
        const query = searchInput.value.toLowerCase().trim();
        const categoriaSel = filterCategory.value;
        const instructorSel = filterInstructor.value;

        let visibleCount = 0;

        courseCards.forEach(card => {
            const titulo = card.querySelector('.course-title').innerText.toLowerCase();
            const categoria = card.getAttribute('data-category');
            const instructor = card.querySelector('.course-instructor').innerText;

            const coincideTitulo = query === '' || titulo.includes(query);
            const coincideCategoria = categoriaSel === '' || categoria === categoriaSel;
            const coincideInstructor = instructorSel === '' || instructor.includes(instructorSel);

            if (coincideTitulo && coincideCategoria && coincideInstructor) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function ordenarCursos(criterio) {
        const container = document.getElementById('coursesContainer');
        const cardsArray = Array.from(courseCards);

        cardsArray.sort((a, b) => {
            if (criterio === 'best-rated') {
                return parseFloat(b.getAttribute('data-rating')) - parseFloat(a.getAttribute('data-rating'));
            } else if (criterio === 'best-sellers') {
                return parseInt(b.getAttribute('data-sales')) - parseInt(a.getAttribute('data-sales'));
            } else if (criterio === 'recent') {
                return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
            }
            return 0;
        });

        cardsArray.forEach(card => container.appendChild(card));
    }
});