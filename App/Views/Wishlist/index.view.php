<?php

/** @var array $items */
/** @var \Framework\Support\View $view */
/** @var \Framework\Support\LinkGenerator $link */

?>

<div class="container web-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">My wishlist</h1>
    </div>

    <?php if (empty($items)) : ?>
        <p>You don't have any games in your wishlist yet.</p>
        <a href="<?= $link->url('home.index') ?>" class="btn btn-primary mt-2">
            Browse games
        </a>
    <?php else : ?>

        <!-- Controls row: search + sort -->
        <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
                <label for="wishlist-search" class="form-label mb-1">Search in wishlist</label>
                <input type="text" id="wishlist-search" class="form-control" placeholder="Search by game name...">
            </div>
            <div class="col-md-6 d-flex align-items-end justify-content-md-end">
                <div class="input-group mt-2 mt-md-0" style="max-width: 280px;">
                    <label class="input-group-text" for="wishlist-sort">Sort by</label>
                    <select id="wishlist-sort" class="form-select">
                        <option value="date_desc" selected>Release date (newest first)</option>
                        <option value="date_asc">Release date (oldest first)</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Wishlist games as cards in a responsive grid -->
        <div id="wishlist-container" class="row g-3">
            <?php foreach ($items as $row): ?>
                <?php /** @var \App\Models\Wishlist $wishlist */
                $wishlist = $row['wishlist'];
                /** @var \App\Models\Game $game */
                $game = $row['game'];

                $name = $game->getName();
                $releaseDate = $game->getGlobalReleaseDate();
                $img = method_exists($game, 'getImageUrlOrEmpty') ? $game->getImageUrlOrEmpty() : ($game->getImageUrl() ?? '');
                ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 wishlist-game"
                     data-name="<?= htmlspecialchars(strtolower($name)) ?>"
                     data-date="<?= htmlspecialchars($releaseDate ?? '') ?>">

                    <div class="card h-100 shadow-sm">
                        <?php if ($img !== ''): ?>
                            <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>">
                                <img src="<?= htmlspecialchars($img) ?>"
                                     class="card-img-top"
                                     alt="<?= htmlspecialchars($name) ?>">
                            </a>
                        <?php else: ?>
                            <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>"
                               class="card-img-top d-flex align-items-center justify-content-center bg-light text-decoration-none"
                               style="height: 180px;">
                                Img
                            </a>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">
                                <a href="<?= $link->url('game.show', ['id' => $game->getId()]) ?>"
                                   class="text-decoration-none">
                                    <?= htmlspecialchars($name) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted mb-2">
                                <?= htmlspecialchars($releaseDate ?? 'TBA') ?>
                            </p>

                            <div class="mt-auto">
                                <form method="post" action="<?= $link->url('wishlist.remove') ?>" class="d-inline">
                                    <input type="hidden" name="game_id" value="<?= (int)$game->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

        <script>
            (function () {
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

                    // Re-append in sorted order (visible ones first, then hidden stay at the end)
                    visibleCards.forEach(card => container.appendChild(card));
                }

                searchInput.addEventListener('input', applyFiltersAndSort);
                sortSelect.addEventListener('change', applyFiltersAndSort);

                // Initial sort on load
                applyFiltersAndSort();
            })();
        </script>

    <?php endif; ?>
</div>
