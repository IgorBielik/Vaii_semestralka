// Client-side filtering for games table on home page
// This expects an input with id="search" and a table with id="games-table" on the page

document.addEventListener('DOMContentLoaded', function () {
    console.log('script.js DOMContentLoaded');

    // --- Home page filtering ---
    const searchInput = document.getElementById('search');
    const table = document.getElementById('games-table');
    if (searchInput && table) {
        const tbody = table.querySelector('tbody');
        if (tbody) {
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
        }
    }

    // --- Game create: cover image picker ---
    const wrapper = document.getElementById('cover-image-wrapper');
    const fileInput = document.getElementById('cover_image');
    const previewImg = document.getElementById('cover-image-preview');
    const placeholder = wrapper ? wrapper.querySelector('.game-create-image-placeholder') : null;

    if (wrapper && fileInput && previewImg) {
        wrapper.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            const file = fileInput.files && fileInput.files[0];
            if (!file) {
                previewImg.style.display = 'none';
                if (placeholder) placeholder.style.display = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = '';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }
});
