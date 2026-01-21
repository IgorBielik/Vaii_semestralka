// Wishlist search and sort functionality
/*vypracované pomocou AI*/
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('wishlist-search');
    const sortSelect = document.getElementById('wishlist-sort');
    const container = document.getElementById('wishlist-container');

    if (!searchInput || !sortSelect || !container) return;

    function parseDate(str) {
        if (!str) return null;
        const parts = str.split('-');
        if (parts.length !== 3) return null;
        const [y, m, d] = parts.map(Number);
        if (!y || !m || !d) return null;
        return new Date(y, m - 1, d);
    }

    function applyFiltersAndSort() {
        const term = searchInput.value.trim().toLowerCase();
        const sort = sortSelect.value;

        const cards = Array.from(container.querySelectorAll('.wishlist-game'));

        // Filter by name (text search)
        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const visible = !term || name.includes(term);
            card.style.display = visible ? '' : 'none';
        });

        // Sort visible cards
        const visibleCards = cards.filter(c => c.style.display !== 'none');

        visibleCards.sort((a, b) => {
            const na = (a.getAttribute('data-name') || '').toString();
            const nb = (b.getAttribute('data-name') || '').toString();

            const da = parseDate(a.getAttribute('data-date') || '');
            const db = parseDate(b.getAttribute('data-date') || '');

            switch (sort) {
                case 'date_asc':
                    if (da && db) return da - db;
                    if (da) return -1;
                    if (db) return 1;
                    return 0;
                case 'date_desc':
                    if (da && db) return db - da;
                    if (da) return -1;
                    if (db) return 1;
                    return 0;
                case 'name_desc':
                    return nb.localeCompare(na);
                case 'name_asc':
                default:
                    return na.localeCompare(nb);
            }
        });

        // Re-append in sorted order
        visibleCards.forEach(card => container.appendChild(card));
    }

    searchInput.addEventListener('input', applyFiltersAndSort);
    sortSelect.addEventListener('change', applyFiltersAndSort);

    // Initial sort on load
    applyFiltersAndSort();
});
