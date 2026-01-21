<?php
/*vypracované pomocou AI*/
/** @var array $items */
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
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 wishlist-game"
                     data-name="<?= htmlspecialchars($row['nameLower']) ?>"
                     data-date="<?= htmlspecialchars($row['releaseDate']) ?>">

                    <div class="card h-100 shadow-sm">
                        <?php if ($row['imageUrl'] !== ''): ?>
                            <a href="<?= $link->url('game.show', ['id' => $row['game']->getId()]) ?>">
                                <img src="<?= htmlspecialchars($row['imageUrl']) ?>"
                                     class="card-img-top"
                                     alt="<?= htmlspecialchars($row['name']) ?>">
                            </a>
                        <?php else: ?>
                            <a href="<?= $link->url('game.show', ['id' => $row['game']->getId()]) ?>"
                               class="card-img-top d-flex align-items-center justify-content-center bg-light text-decoration-none"
                               style="height: 180px;">
                                Img
                            </a>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1">
                                <a href="<?= $link->url('game.show', ['id' => $row['game']->getId()]) ?>"
                                   class="text-decoration-none">
                                    <?= htmlspecialchars($row['name']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted mb-2">
                                <?= htmlspecialchars($row['releaseDate'] ?: 'TBA') ?>
                            </p>

                            <div class="mt-auto">
                                <form method="post" action="<?= $row['removeUrl'] ?>" class="d-inline">
                                    <input type="hidden" name="game_id" value="<?= (int)$row['game']->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script src="<?= $link->asset('js/wishlist.js') ?>"></script>

