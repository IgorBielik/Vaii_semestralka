// Client-side filtering for games table on home page
// This expects an input with id="search" and a table with id="games-table" on the page

document.addEventListener('DOMContentLoaded', function () {
    console.log('script.js DOMContentLoaded');
    const searchInput = document.getElementById('search');
    const table = document.getElementById('games-table');
    console.log('searchInput:', searchInput, 'table:', table);
    if (!searchInput || !table) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr'));

    function normalize(text) {
        return text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    function applyFilter() {
        const query = normalize(searchInput.value.trim());
        rows.forEach(row => {
            const text = normalize(row.innerText || row.textContent || '');
            row.style.display = (!query || text.includes(query)) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilter);
});
