/*vypracované pomocou AI*/
document.addEventListener('DOMContentLoaded', () => {
    function setupTableFilter(inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!input || !table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        input.addEventListener('input', () => {
            const query = input.value.trim().toLowerCase();
            const rows = tbody.querySelectorAll('tr');

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                if (!query || name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Hook up filters for platforms and genres tables in admin dashboard
    setupTableFilter('platform-search', 'platform-table');
    setupTableFilter('genre-search', 'genre-table');
});
