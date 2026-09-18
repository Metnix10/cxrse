document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('instructorFilterForm');
    const summaryRows = document.querySelectorAll('#summaryTableBody tr');
    const detailButtons = document.querySelectorAll('.btn-view-detail');
    const selectedCourseName = document.getElementById('selectedCourseName');

    const courseDetails = {
        '1': {
            name: 'Master en HTML, CSS y JavaScript',
            total: '$59,880.00 MXN',
            students: [
                { name: 'Milton Torres', date: '10-Ago-2026', progress: 'Nivel 3 (60%)', price: '$499.00 MXN', method: 'Tarjeta de Crédito' },
                { name: 'María Fernández', date: '12-Ago-2026', progress: 'Nivel 5 (100%)', price: '$499.00 MXN', method: 'PayPal' },
                { name: 'Jorge Ramírez', date: '18-Ago-2026', progress: 'Nivel 1 (20%)', price: '$499.00 MXN', method: 'Transferencia SPEI' }
            ]
        },
        '2': {
            name: 'Estrategias de Marketing Digital',
            total: '$22,425.00 MXN',
            students: [
                { name: 'Laura Gómez', date: '15-Jul-2026', progress: 'Nivel 2 (40%)', price: '$299.00 MXN', method: 'Tarjeta de Crédito' },
                { name: 'Pedro Sánchez', date: '20-Jul-2026', progress: 'Nivel 4 (100%)', price: '$299.00 MXN', method: 'PayPal' }
            ]
        },
        '3': {
            name: 'Introducción a PHP Puro (Baja Lógica)',
            total: '$4,500.00 MXN',
            students: [
                { name: 'Andrés Castro', date: '01-Abr-2026', progress: 'Nivel 4 (100%)', price: '$300.00 MXN', method: 'Tarjeta de Débito' }
            ]
        }
    };

    detailButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const courseId = this.getAttribute('data-course');
            const data = courseDetails[courseId];

            if (data) {
                selectedCourseName.innerText = data.name;
                document.querySelector('.total-amount').innerText = data.total;

                const detailTbody = document.getElementById('detailTableBody');
                detailTbody.innerHTML = '';

                data.students.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${s.name}</td>
                        <td>${s.date}</td>
                        <td>${s.progress}</td>
                        <td class="amount">${s.price}</td>
                        <td>${s.method}</td>
                    `;
                    detailTbody.appendChild(tr);
                });
            }
        });
    });

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const category = document.getElementById('filterCategory').value;
        const status = document.getElementById('filterStatus').value;

        summaryRows.forEach(row => {
            const rowCat = row.getAttribute('data-category');
            const rowStatus = row.getAttribute('data-status');
            const rowDate = row.getAttribute('data-date');

            let matchCat = (category === 'all' || rowCat === category);
            let matchStatus = (status === 'all' || rowStatus === status);
            let matchDate = true;

            if (startDate) matchDate = matchDate && (rowDate >= startDate);
            if (endDate) matchDate = matchDate && (rowDate <= endDate);

            if (matchCat && matchStatus && matchDate) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});