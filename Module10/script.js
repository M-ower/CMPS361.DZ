
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('reportSearch');
    const stockFilter = document.getElementById('stockFilter');
    const table = document.getElementById('reportTable');
    const reportCount = document.getElementById('reportCount');
    const noResults = document.getElementById('noResults');

    if (!searchInput || !stockFilter || !table) {
        return;
    }

    const rows = Array.from(
        table.querySelectorAll('tbody tr[data-status]')
    );

    function filterProducts() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedStatus = stockFilter.value;

        let visibleCount = 0;

        rows.forEach(function (row) {
            const productText = row.dataset.search || '';
            const productStatus = row.dataset.status;

            const matchesSearch = productText.includes(searchText);

            const matchesStatus =
                selectedStatus === 'all' ||
                productStatus === selectedStatus;

            const shouldShow = matchesSearch && matchesStatus;

            row.hidden = !shouldShow;

            if (shouldShow) {
                visibleCount++;
            }
        });

        reportCount.textContent =
            'Showing ' + visibleCount + ' of ' + rows.length + ' products';

        noResults.hidden = visibleCount !== 0;
    }

    searchInput.addEventListener('input', filterProducts);
    stockFilter.addEventListener('change', filterProducts);
});
