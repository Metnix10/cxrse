document.addEventListener('DOMContentLoaded', function () {
    const reportTypeSelect = document.getElementById('reportType');
    const tableHeader = document.getElementById('reportTableHeader');
    const tableBody = document.getElementById('reportTableBody');

    const reportData = {
        instructor: [
            { user: 'carlos.instructor@ejemplo.com', name: 'Carlos López', date: '15-Ene-2026', courses: 3, earnings: '$86,805.00 MXN' },
            { user: 'ana.martinez@ejemplo.com', name: 'Ana Martínez', date: '20-Feb-2026', courses: 2, earnings: '$33,250.00 MXN' },
            { user: 'roberto.gomez@ejemplo.com', name: 'Roberto Gómez', date: '10-Mar-2026', courses: 1, earnings: '$0.00 MXN' }
        ],
        student: [
            { user: 'milton@ejemplo.com', name: 'Milton Torres', date: '10-Ago-2026', courses: 3, completedPct: '33.3%' },
            { user: 'maria.fer@ejemplo.com', name: 'María Fernández', date: '12-Ago-2026', courses: 2, completedPct: '100.0%' },
            { user: 'jorge.ramirez@ejemplo.com', name: 'Jorge Ramírez', date: '18-Ago-2026', courses: 1, completedPct: '0.0%' }
        ]
    };

    function renderReport(type) {
        tableHeader.innerHTML = '';
        tableBody.innerHTML = '';

        if (type === 'instructor') {
            tableHeader.innerHTML = `
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Fecha Ingreso</th>
                    <th>Cursos Ofrecidos</th>
                    <th>Total Ganancias</th>
                </tr>
            `;

            reportData.instructor.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.user}</td>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.date}</td>
                    <td>${item.courses}</td>
                    <td class="amount">${item.earnings}</td>
                `;
                tableBody.appendChild(tr);
            });

        } else if (type === 'student') {
            tableHeader.innerHTML = `
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Fecha Ingreso</th>
                    <th>Cursos Inscritos</th>
                    <th>% Cursos Terminados</th>
                </tr>
            `;

            reportData.student.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.user}</td>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.date}</td>
                    <td>${item.courses}</td>
                    <td><strong>${item.completedPct}</strong></td>
                `;
                tableBody.appendChild(tr);
            });
        }
    }

    reportTypeSelect.addEventListener('change', function () {
        renderReport(this.value);
    });

    renderReport('instructor');
});

function toggleUserStatus(button) {
    const row = button.closest('tr');
    const statusBadge = row.querySelector('.status-badge');
    const attemptsCell = row.querySelectorAll('td')[3];

    if (button.classList.contains('btn-block')) {
        statusBadge.className = 'status-badge status-blocked';
        statusBadge.innerText = 'Deshabilitado';
        button.className = 'btn-action btn-unblock';
        button.innerText = 'Desbloquear';
    } else {
        statusBadge.className = 'status-badge status-active';
        statusBadge.innerText = 'Activo';
        attemptsCell.innerText = '0';
        attemptsCell.className = '';
        button.className = 'btn-action btn-block';
        button.innerText = 'Bloquear';
    }
}