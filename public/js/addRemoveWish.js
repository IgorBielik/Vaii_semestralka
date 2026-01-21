// Minimal JS for home page wishlist Add/Remove via AJAX
// Assumes a table with id="games-table" containing forms that post to
// routes that end with "wishlist.add" or "wishlist.remove".
/*vypracované pomocou AI*/
document.addEventListener('DOMContentLoaded', function () {
    const gamesTable = document.getElementById('games-table');
    if (!gamesTable) return;

    console.log('[wishlist] attaching AJAX handler to games-table');

    gamesTable.addEventListener('click', function (e) {
        const target = e.target;
        if (!target) return;

        const btn = target.closest ? target.closest('button') : null;
        if (!btn) return;

        const form = btn.closest ? btn.closest('form') : null;
        if (!form) return;

        const action = form.getAttribute('action') || '';
        // action is full URL like "/?c=wishlist&a=add" – look for "wishlist" and "add/remove"
        const isAdd = action.includes('wishlist') && action.includes('add');
        const isRemove = action.includes('wishlist') && action.includes('remove');
        if (!isAdd && !isRemove) {
            return; // not a wishlist form
        }

        console.log('[wishlist] intercept', isAdd ? 'add' : 'remove', '->', action);

        e.preventDefault();

        const formData = new FormData(form);

        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then(resp => {
                console.log('[wishlist] response status', resp.status);
                return resp.ok ? resp.json() : Promise.reject(resp.status);
            })
            .then(data => {
                console.log('[wishlist] json', data);
                if (!data || !data.success) {
                    return;
                }

                const inWishlist = !!data.inWishlist;

                if (inWishlist) {
                    // Now in wishlist -> show Remove
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-outline-danger');
                    btn.textContent = 'Remove';
                    if (!isRemove) {
                        form.setAttribute('action', action.replace('add', 'remove'));
                    }
                } else {
                    // Removed from wishlist -> show Add
                    btn.classList.remove('btn-outline-danger');
                    btn.classList.add('btn-outline-primary');
                    btn.textContent = 'Add';
                    if (!isAdd) {
                        form.setAttribute('action', action.replace('remove', 'add'));
                    }
                }
            })
            .catch(err => {
                console.error('[wishlist] AJAX error', err);
                // On error keep current state; user can refresh if needed.
            });
    });
});
