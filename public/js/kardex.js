document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('kardexFilterForm');
    const tableRows = document.querySelectorAll('#kardexTableBody tr');
    const noResultsMsg = document.getElementById('noKardexResults');

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const startDateVal = document.getElementById('startDate').value;
        const endDateVal = document.getElementById('endDate').value;
        const categoryVal = document.getElementById('filterCategory').value;
        const statusVal = document.getElementById('filterStatus').value;
        const activeVal = document.getElementById('filterActive').value;

        let visibleRows = 0;

        tableRows.forEach(row => {
            const rowCategory = row.getAttribute('data-category');
            const rowStatus = row.getAttribute('data-status');
            const rowActive = row.getAttribute('data-active');
            const rowDateStr = row.getAttribute('data-date');

            let matchesCategory = (categoryVal === 'all' || rowCategory === categoryVal);
            let matchesStatus = (statusVal === 'all' || rowStatus === statusVal);
            let matchesActive = (activeVal === 'all' || 
                                (activeVal === 'active' && rowActive === 'active') || 
                                (activeVal === 'inactive' && rowActive === 'inactive'));

            let matchesDate = true;
            if (startDateVal) {
                matchesDate = matchesDate && (rowDateStr >= startDateVal);
            }
            if (endDateVal) {
                matchesDate = matchesDate && (rowDateStr <= endDateVal);
            }

            if (matchesCategory && matchesStatus && matchesActive && matchesDate) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        noResultsMsg.style.display = visibleRows === 0 ? 'block' : 'none';
    });
});